<?php

namespace App\Http\Controllers\Translation;

use Exception;
use App\Models\Translation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\TranslationRepository;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TranslationController extends Controller
{

    use ApiResponse;

    protected $translationRepository;

    public function __construct(TranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }
    
    /**
     * @OA\SecurityScheme(
     *     securityScheme="sanctum",
     *     type="http",
     *     scheme="bearer"
     * )
     */

    /**
     * Get a list of translations.
     *
     * @OA\Get(
     *     path="/api/translations",
     *     tags={"translations"},
     *     summary="Get all translations",
     *     security={{"sanctum":{}}},
     *     description="Returns a list of translations",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $translations = $this->translationRepository->getAll();

        return $this->sendSuccess($translations, 'Translations retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Create a new translation",
     *     security={{"sanctum":{}}},
     *     tags={"translations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"locale", "key", "content", "tags"},
     *             @OA\Property(property="locale", type="string", example="eng"),
     *             @OA\Property(property="key", type="string", example="welcome_message"),
     *             @OA\Property(property="content", type="string", example="{\`en\`:\`Welcome\`,\`es\`:\`Bienvenido\`}"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"greeating", "welcome"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Translation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="locale", type="string", example="eng"),
     *             @OA\Property(property="key", type="string", example="welcome_message"),
     *             @OA\Property(property="content", type="string", example="{\`en\`:\`Welcome\`,\`es\`:\`Bienvenido\`}"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"greeating", "welcome"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function store(StoreTranslationRequest $request): JsonResponse
    {
        try {
            $translation = $this->translationRepository->create($request->only(['locale', 'key', 'content', 'tags']));

            return $this->sendSuccess($translation, 'Translation created successfully');
        } catch (Exception $e) {
            return $this->sendError('An error occurred', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     summary="Get a specific translation",
     *     security={{"sanctum":{}}},
     *     tags={"translations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Translation ID"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Translation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $translation = $this->translationRepository->find($id);

        return $this->sendSuccess($translation, 'Translation retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     summary="Update an existing translation",
     *     security={{"sanctum":{}}},
     *     tags={"translations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Translation ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"locale", "key", "content", "tags"},
     *             @OA\Property(property="locale", type="string", example="eng"),
     *             @OA\Property(property="key", type="string", example="welcome_message"),
     *             @OA\Property(property="content", type="string", example="{\`en\`:\`Welcome\`,\`es\`:\`Bienvenido\`}"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"greeating", "welcome"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="locale", type="string", example="eng"),
     *             @OA\Property(property="key", type="string", example="welcome_message"),
     *             @OA\Property(property="content", type="string", example="{\`en\`:\`Welcome\`,\`es\`:\`Bienvenido\`}"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"greeating", "welcome"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Translation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function update(UpdateTranslationRequest $request, Translation $translation): JsonResponse
    {
        try {
            $data = $request->only(['locale', 'content', 'tags']);
            $updatedTranslation = $this->translationRepository->update($translation, $data);

            return $this->sendSuccess($updatedTranslation, 'Translation updated successfully');
        } catch (Exception $e) {
            return $this->sendError('An error occurred', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     summary="Delete a translation",
     *     security={{"sanctum":{}}},
     *     tags={"translations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Translation ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Translation deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Translation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function destroy(Translation $translation): JsonResponse
    {
        try {
            $this->translationRepository->delete($translation);

            return $this->sendSuccess(null, 'Translation deleted successfully');
        } catch (Exception $e) {
            return $this->sendError('An error occurred', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/translations/search",
     *     summary="Search translations",
     *     security={{"sanctum":{}}},
     *     tags={"translations"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Search query string"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="locale", type="string", example="eng"),
     *                 @OA\Property(property="key", type="string", example="welcome_message"),
     *                 @OA\Property(property="content", type="string", example="{\`en\`:\`Welcome\`,\`es\`:\`Bienvenido\`}"),
     *                 @OA\Property(
     *                     property="tags",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"greeating", "welcome"}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query');

        if (empty($query)) {
            return $this->sendError('Query parameter is required.');
        }

        $translations = $this->translationRepository->searchTranslations($query);

        return $this->sendSuccess($translations, 'Search results');
    }

    
    /**
     * @OA\Get(
     *     path="/api/translations/tags/{tagName}",
     *     summary="Get translations by tag name",
     *     security={{"sanctum":{}}},
     *     tags={"translations"},
     *     @OA\Parameter(
     *         name="tagName",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Tag name to filter translations"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translations retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="locale", type="string", example="eng"),
     *                 @OA\Property(property="key", type="string", example="welcome_message"),
     *                 @OA\Property(
     *                     property="tags",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"greeating", "welcome"}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function getTranslationsByTag(string $tagName): JsonResponse
    {
        $translations = $this->translationRepository->getTranslationsByTag($tagName);

        return $this->sendSuccess($translations, 'Translations retrieved successfully');
    }

    
    /**
     * @OA\Post(
     *     path="/api/translations/{id}/assign-tags",
     *     summary="Assign tags to a translation",
     *     security={{"sanctum":{}}},
     *     tags={"translations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Translation ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"greeting", "welcome"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tags assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tags assigned successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function assignTags(Request $request, Translation $translation): JsonResponse
    {
        $tags = $request->get('tags', []);

        $translation = $this->translationRepository->assignTags($translation, $tags);

        return $this->sendSuccess($translation, 'Tags assigned successfully');
    }

    
    /**
     * @OA\Get(
     *     path="/api/translations/export",
     *     summary="Export all translations",
     *     security={{"sanctum":{}}},
     *     tags={"translations"},
     *     @OA\Response(
     *         response=200,
     *         description="Exported translations",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function export(): StreamedResponse | JsonResponse
    {
        try {
            return $this->translationRepository->exportTranslations();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
