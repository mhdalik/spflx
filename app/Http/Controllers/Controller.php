<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Task management application API",
    description: "API Documentation for the Task management application. Protect endpoints by authenticating using Sanctum."
)]
#[OA\Server(
    url: "https://spflx.nalikeram.in",
    description: "Demo Server"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local development server"
)]
#[OA\Server(
    url: "/",
    description: "Relative URL"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Sanctum token authentication. Enter your token (without 'Bearer ' prefix)."
)]
abstract class Controller
{
    //
}
