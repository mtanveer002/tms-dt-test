## Setup project
1. copy docker folder to .docker folder. -> cd .docker and in .env
2. set `COMPOSE_PROJECT_NAME` e.g `tms`
3. set `HTTP_PORT`, `MYSQL_PORT`, `MYSQL_DATABASE` and `MYSQL_ROOT_PASSWORD`

## set database connection in main .env 
1. set DB_HOST= with `db` parameter
2. set DB_DATABASE = `MYSQL_DATABASE` ('docker/.env')
3. set DB_USERNAME = `root` ()
4. set DB_PASSWORD = `MYSQL_ROOT_PASSWORD` (docker/.env)
5. RUN `docker compose build`
6. RUN `docker compose up`

After successfully setup you login to docker cli using this command
`docker exec -u docker_app_user -it COMPOSE_PROJECT_NAME_php_service bash` to run php artisan command

## Project Description
This project is a Translation Management API that allows users to create, update, search, and manage translations for different locales. The API provides features such as translation tagging, searching by tags, assigning tags, and exporting translations. The translations are stored in a structured JSON format to support multiple languages dynamically.

## Repository Pattern
In this project, the Repository Pattern is used to manage both business logic and database operations within dedicated repository classes. This approach simplifies the application structure by centralizing responsibilities in one place.

Why Use the Repository Pattern?
Consolidated Logic – Both data access and business logic are handled within the repository, reducing the need for multiple layers.

Cleaner Controllers – Controllers focus solely on handling HTTP requests and responses, while the repository handles all underlying logic.

Improved Testability – Repositories can be easily mocked, making unit testing more effective and faster.

Maintainable & Scalable – Updates to queries or business rules are made in one location, making the system easier to maintain and extend.

In this project, the TranslationRepository is responsible for handling all core logic and database interactions related to translations.

Key Benefits of the Repository Pattern
✅ 1. Clear Separation of Concerns

Keeps database and business logic out of controllers.

Centralizes related logic for better organization.

✅ 2. Easier Maintenance & Scalability

Any changes in logic or database structure are handled in one place.

Makes it easier to adapt to new requirements or data sources.

✅ 3. Better Testability

Repositories can be mocked in tests, allowing for isolated and reliable unit testing.

Reduces dependency on actual database interactions during testing.

## Tests
1. Unit Tests
Unit tests are used to test individual components (like functions, repositories, or services) in isolation. They ensure that each method works correctly without external dependencies like databases or HTTP requests.

To verify that the business logic works correctly.
To catch errors early in individual methods.
To make debugging easier by testing small parts of the application separately.

2. Feature Tests
Feature tests simulate real HTTP requests to test the API's behavior as a whole. They check how different components work together, including the controller, service, and database.

To validate API endpoints and responses.
To ensure that user interactions work as expected.
To test full workflows like creating, updating, or deleting translations.

3. Performace Tests

To determine the system's capacity and breaking point.

To ensure the application performs well under expected user loads.

To identify performance bottlenecks, such as slow database queries or memory leaks.

To maintain acceptable response times for API endpoints


## Swagger Documentation
Swagger is integrated into this project for API documentation and testing. It provides:

Interactive API Documentation – Developers can test API endpoints directly in the browser.
Clear API Contracts – Endpoints, request parameters, and responses are well-documented.
Standardization – Swagger follows OpenAPI standards, making it easier for other developers to understand and integrate the API.
The documentation includes endpoints for creating, searching, updating, assigning tags, exporting translations, and more.

`After project setup you can visit http://localhost/api/documentation for api documentation`

To reflect documentation changes in Swagger UI, run the following command:
`php artisan l5-swagger:generate`

## Run Queue worker
1. login into docker container
RUN `php artisan queue:work`


## 📚 API Endpoints Overview

### 📌 Create Translation

```http
POST /api/translations
```

### ✏️ Update Translation

```http
PUT /api/translations/{id}
```

### 🌍 Get Translations by Locale

```http
GET /api/translations/{locale}
```

### 🔑 Get Translation by Key or ID

```http
GET /api/translations/{identifier}
```

### 📦 Export Translations

```http
GET /api/translations/export
```

### 🔍 Search Translations

```http
GET /api/translations/search?query={keyword}
