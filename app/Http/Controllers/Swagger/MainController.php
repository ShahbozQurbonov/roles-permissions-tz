<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Roles-Permissions"
 * ),
 *
 * @OA\PathItem(path="/api")
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication APIs"
 * ),
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */


class MainController extends Controller
{
    //
}
