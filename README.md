# Task Management API

A minimal task management REST API built with Laravel 13, Sanctum token authentication, and interactive Swagger/OpenAPI documentation.

## **Live Demo & API Documentation:** [https://spflx.nalikeram.in/api/documentation](https://spflx.nalikeram.in/api/documentation)

### Demo User Credentials

You can use the following seeded accounts to test the authenticated endpoints:
* **User 1:**
  - Email: `demo1@demo1.com`
  - Password: `demo1@demo1.com`
* **User 2:**
  - Email: `demo2@demo2.com`
  - Password: `demo2@demo2.com`

## Requirements

-   PHP ^8.3 (tested on PHP 8.5)
-   Composer

## Setup & Installation

You can get the application up and running in a few steps.

1. **Clone the project and enter the directory:**

    ```bash
    git clone https://github.com/mhdalik/spflx
    cd spflx
    ```

2. **Run automatic setup:**
   The project has a composer script to automate the setup process (dependencies install, environment config, key generation, database migration, and frontend build):

    ```bash
    composer setup
    ```

    _Alternatively, you can run these steps manually:_

    ```bash
    composer install
    cp .env.example .env
    php artisan key:generate
    touch database/database.sqlite
    php artisan migrate --seed
    ```

3. **Start the local server:**
    ```bash
    php artisan serve
    ```
    By default, the server will start at `http://localhost:8000`.

## API Documentation (Swagger)

This API includes interactive Swagger UI documentation generated from OpenAPI annotations.

-   **Documentation URL:** `http://localhost:8000/api/documentation`
-   **Regenerate Documentation:** If you add or modify endpoints, regenerate the Swagger specifications by running:
    ```bash
    php artisan l5-swagger:generate
    ```
