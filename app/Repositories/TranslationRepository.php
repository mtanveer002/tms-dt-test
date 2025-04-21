<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Jobs\ExportTranslationsJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\TranslationResource;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TranslationRepository
{
    public function getAll(int $perPage = 50): array
    {
        $query = Translation::query();

        $translations = $query->with('tags')->paginate($perPage);

        $paginated = $translations->toArray();
        $paginated['data'] = TranslationResource::collection($translations)->resolve();

        return $paginated;
    }

    public function create(array $data): Translation
    {
        $translation = Translation::create($data);

        if (isset($data['tags'])) {
            $this->attachTags($translation, $data['tags']);
        }

        ExportTranslationsJob::dispatch();

        return $translation->load('tags');
    }
    
    public function find(int $id): Translation
    {
        return Cache::remember("translations.{$id}", 60, function () use ($id) {
            return Translation::with('tags')->findOrFail($id);
        });
    }

    public function update(Translation $translation, array $data): Translation
    {
        $translation->update($data);

        Cache::forget("translations.{$translation->id}");

        return $translation;
    }

    public function delete(Translation $translation): bool
    {
        return $translation->delete();
    }

    public function attachTags(Translation $translation, array $tags): Translation
    {
        $tagIds = collect($tags)->map(function (string $tagName): int {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        })->toArray();

        $translation->tags()->sync($tagIds);

        Cache::forget("translations.{$translation->id}");

        return $translation;
    }

    public function searchTranslations(string $query): Collection
    {
        return Translation::search($query)->get();
    }


    public function getTranslationsByTag(string $tagName): Collection
    {
        return Translation::whereHas('tags', function ($query) use ($tagName) {
            $query->where('name', $tagName);
        })->with('tags')->get();
    }

    public function assignTags(Translation $translation, array $tags): Translation
    {
        $tagIds = collect($tags)->map(function (string $tagName): int {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        })->toArray();

        $translation->tags()->attach($tagIds);

        Cache::forget("translations.{$translation->id}");

        return $translation->load('tags');
    }

    public function exportTranslations(): StreamedResponse | JsonResponse
    {
        $filePath = 'translations.json';

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['error' => 'Translations file not found.'], 404);
        }

        return new StreamedResponse(function () use ($filePath) {
            $stream = Storage::disk('local')->readStream($filePath);

            if (!$stream) {
                abort(500, 'Could not open file for reading.');
            }

            // Set a buffer size for optimal reading
            $bufferSize = 8192; // 8 KB buffer, you can adjust as needed

            while (!feof($stream)) {
                // Read and send data in larger chunks to reduce I/O overhead
                echo fread($stream, $bufferSize);
                // No need to flush explicitly after each read, as the output buffer will handle it
            }

            fclose($stream);
        }, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'inline; filename="translations.json"',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

}