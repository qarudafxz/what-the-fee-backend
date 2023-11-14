<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function getAllPermissionsOfAllAdmins()
    {
        $permissions = Permission::join(
            'admins',
            'admins.admin_id',
            '=',
            'permissions.admin_id'
        )
            ->select(
                'admins.first_name',
                'admins.last_name',
                'admins.email',
                'permissions.*'
            )
            ->get();

        return response()->json([
            'message' => 'Permissions retrieved',
            'permissions' => $permissions,
        ]);
    }

    public function updatePermission(string $admin_id)
    {
        $permission = Permission::where('admin_id', $admin_id)->first();

        if (!$permission) {
            return response()->json(
                [
                    'message' => 'Permission not found',
                ],
                404
            );
        }

        $permission->can_add = request('can_add');
        $permission->can_delete = request('can_delete');
        $permission->can_update = request('can_update');
        $permission->save();

        return response()->json([
            'message' => 'Permission updated',
            'permission' => $permission,
        ]);
    }
}
