<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API para gerenciamento de bolões da Copa do Mundo',
    title: 'World Pool API'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    description: 'Token obtido via POST /api/login'
)]
#[OA\Server(
    url: 'http://localhost:8080',
    description: 'Servidor de desenvolvimento'
)]
abstract class Controller {}
