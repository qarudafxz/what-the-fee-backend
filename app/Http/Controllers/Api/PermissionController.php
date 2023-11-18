<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Admin;

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
                'admins.admin_id as permit_admin_id',
                'permissions.*'
            )
            ->where('admins.role', '!=', 'super')
            ->get();

        return response()->json([
            'message' => 'Permissions retrieved',
            'permissions' => $permissions,
        ]);
    }

    public function canUpdatePermission(string $admin_id, Request $request)
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

        $admin = Admin::where('admin_id', $admin_id)->first();

        // Check if values from the frontend are different from the values in the database
        if ($request->can_update !== $permission->can_update) {
            $permission->can_update = $request->can_update;

            try {
                $permission->save();
            } catch (\Exception $e) {
                return response()->json(
                    [
                        'error' => 'Failed to update permission',
                        'message' => $e->getMessage(),
                    ],
                    500
                );
            }

            $isTrue = $permission->can_update ? 'true' : 'false';

            $log = new Log();
            $log->admin_id = $request->header('admin_id');
            $log->ar_no = $admin->admin_id;
            $log->label =
                ' set the permission of ' .
                $admin->first_name .
                ' to ' .
                $isTrue .
                ' for updating';
            $log->method = 'UPDATE';
            $log->save();

            return response()->json([
                'message' =>
                    $isTrue === 'true'
                        ? 'Admin can now update records'
                        : 'Admin can no longer update records',
                'permission' => $permission,
            ]);
        } else {
            // Values are the same, no need to update
            return response()->json([
                'message' => 'Permission values are the same, no update needed',
                'permission' => $permission,
            ]);
        }
    }

    public function canDeletePermission(string $admin_id, Request $request)
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

        $admin = Admin::where('admin_id', $admin_id)->first();

        // Check if values from the frontend are different from the values in the database
        if ($request->can_delete !== $permission->can_delete) {
            $permission->can_delete = $request->can_delete;

            try {
                $permission->save();
            } catch (\Exception $e) {
                return response()->json(
                    [
                        'error' => 'Failed to update permission',
                        'message' => $e->getMessage(),
                    ],
                    500
                );
            }

            $isTrue = $permission->can_delete ? 'true' : 'false';

            $log = new Log();
            $log->admin_id = $request->header('admin_id');
            $log->ar_no = $admin->admin_id;
            $log->label =
                ' set the permission of ' .
                $admin->first_name .
                ' to ' .
                $isTrue .
                ' for deleting';
            $log->method = 'UPDATE';
            $log->save();

            return response()->json([
                'message' =>
                    $isTrue === 'true'
                        ? 'Admin can now delete records'
                        : 'Admin can no longer delete records',
                'permission' => $permission,
            ]);
        } else {
            // Values are the same, no need to update
            return response()->json([
                'message' => 'Permission values are the same, no update needed',
                'permission' => $permission,
            ]);
        }
    }
}
