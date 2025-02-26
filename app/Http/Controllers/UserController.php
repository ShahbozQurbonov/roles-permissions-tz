<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="User management APIs"
 * ),
 *
 * @OA\Get(
 *     path="/api/users",
 *     summary="Get all users",
 *     tags={"Users"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\Response(
 *         response=200,
 *         description="List of users",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="User"),
 *             @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
 *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="admin")),
 *             @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="edit posts")),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-17T12:34:56Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-02-17T12:34:56Z"),
 *         ))
 *     )
 * ),
 *
 * @OA\Post(
 *     path="/api/users",
 *     summary="Create a new user",
 *     tags={"Users"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password"},
 *             @OA\Property(property="name", type="string", example="User"),
 *             @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(response=201, description="User created successfully")
 * ),
 *
 *
 * @OA\Get(
 *     path="/api/users/{id}",
 *     summary="Get a specific user",
 *     tags={"Users"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="User ID"
 *     ),
 *     @OA\Response(response=200, description="User details", @OA\JsonContent(
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="User"),
 *         @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
 *         @OA\Property(property="roles", type="array", @OA\Items(type="string", example="admin")),
 *         @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="edit posts")),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-17T12:34:56Z"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-02-17T12:34:56Z"),))
 * ),
 *
 * @OA\Put(
 *     path="/api/users/{id}",
 *     summary="Update a user",
 *     tags={"Users"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="User ID"
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="User"),
 *             @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(response=200, description="User updated successfully")
 * ),
 *
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     summary="Delete a user",
 *     tags={"Users"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="User ID"
 *     ),
 *     @OA\Response(response=200, description="User deleted")
 * ),
 *
 * @OA\Post(
 *     path="/api/users/{user}/assign-role",
 *     summary="Assign role(s) to a user",
 *     description="Assign one or more roles to a specified user.",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},

 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),

 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"roles"},
 *             @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"admin", "editor"})
 *         )
 *     ),

 *     @OA\Response(
 *         response=200,
 *         description="Roles assigned successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Roles assigned successfully")
 *         )
 *     ),

 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The roles field is required."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),

 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     ),

 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="You do not have permission to assign roles.")
 *         )
 *     )
 * ),
 *
 * @OA\Post(
 *     path="/api/users/{user}/give-permission",
 *     summary="Assign permissions to a user",
 *     description="Assign one or more permissions to a specified user.",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"permissions"},
 *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"create user", "edit user", "delete user", "view user"})
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Permissions assigned successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Permissions granted successfully")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The permissions field is required."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="You do not have permission to grant permissions.")
 *         )
 *     )
 * ),
 *
 * @OA\Delete(
 *     path="/api/users/{id}/remove-role/{role}",
 *     summary="Remove a role from a user",
 *     tags={"Users"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="User ID"
 *     ),
 *     @OA\Parameter(
 *         name="role",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="Roles: admin, user"
 *     ),
 *     @OA\Response(response=200, description="Deleted")
 * ),
 *
 * @OA\Delete(
 *     path="/api/users/{id}/revoke-permission-to/{permission}",
 *     summary="Revoke a permission from a user",
 *     tags={"Users"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="User ID"
 *     ),
 *     @OA\Parameter(
 *         name="permission",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="Permissions: create user, edit user, delete user, view user"
 *     ),
 *     @OA\Response(response=200, description="Deleted")
 * )
 */

class UserController extends Controller
{
    //
}
