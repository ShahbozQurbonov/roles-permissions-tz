<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::with('roles', 'permissions')->get());
    }


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


    public function show(User $user)
    {
        return response()->json($user->load('roles', 'permissions'));
    }


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


    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }


    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'required|string|exists:roles,name',
        ]);

        $user->assignRole($request->roles);

        return response()->json(['message' => "Roles assigned successfully"]);
    }


    public function givePermission(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $user->givePermissionTo($request->permissions);

        return response()->json(['message' => "Permissions granted successfully"]);
    }


    public function removeRole($id, $role)
    {
        $user = User::find($id);
        if (!$user->hasRole($role)) {
            return response()->json(['message' => 'Error'], 400);
        }

        $user->removeRole($role);

        return response()->json(['message' => 'Deleted']);
    }


    public function revokePermissionTo($id, $permission)
    {
        $user = User::find($id);

        if (!$user->hasPermissionTo($permission)) {
            return response()->json(['message' => 'Error'], 400);
        }

        $user->revokePermissionTo($permission);

        return response()->json(['message' => 'Deleted']);
    }
}
