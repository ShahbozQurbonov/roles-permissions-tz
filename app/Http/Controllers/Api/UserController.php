<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="User management APIs"
 * )
 */
class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users",
     *     tags={"Users"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(User::with('roles', 'permissions')->get());
    }

    /**
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
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->assignRole('viewer');
        return response()->json($user, 201);
    }

    /**
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
     *     @OA\Response(response=200, description="User details", @OA\JsonContent(ref="#/components/schemas/User"))
     * )
     */
    public function show(User $user)
    {
        return response()->json($user->load('roles', 'permissions'));
    }

    /**
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
     * )
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
        ]);

        $user->update(array_filter($data));

        return response()->json($user);
    }

    /**
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
     * )
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

/**
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
 * )
 */
public function assignRole(Request $request, User $user)
{
    $request->validate([
        'roles' => 'required|array',
        'roles.*' => 'required|string|exists:roles,name',
    ]);

    $user->assignRole($request->roles);

    return response()->json(['message' => "Roles assigned successfully"]);
}


/**
 * @OA\Post(
 *     path="/api/users/{user}/give-permission",
 *     summary="Assign permissions to a user",
 *     description="Assign one or more permissions to a specified user.",
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
 *             required={"permissions"},
 *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"edit articles", "delete users"})
 *         )
 *     ),

 *     @OA\Response(
 *         response=200,
 *         description="Permissions assigned successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Permissions granted successfully")
 *         )
 *     ),

 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The permissions field is required."),
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
 *             @OA\Property(property="message", type="string", example="You do not have permission to grant permissions.")
 *         )
 *     )
 * )
 */
public function givePermission(Request $request, User $user)
{
    $request->validate([
        'permissions' => 'required|array',
        'permissions.*' => 'string|exists:permissions,name',
    ]);

    $user->givePermissionTo($request->permissions);

    return response()->json(['message' => "Permissions granted successfully"]);
}


    /**
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
     * )
     */
    public function removeRole($id, $role)
    {
        $user=User::find($id);
        if (!$user->hasRole($role)) {
            return response()->json(['message' => 'Error'], 400);
        }

        $user->removeRole($role);

        return response()->json(['message' => 'Deleted']);
    }

    /**
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
    public function revokePermissionTo($id, $permission)
    {
        $user=User::find($id);

        if (!$user->hasPermissionTo($permission)) {
            return response()->json(['message' => 'Error'], 400);
        }

        $user->revokePermissionTo($permission);

        return response()->json(['message' => 'Deleted']);
    }
}