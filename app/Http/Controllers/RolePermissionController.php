<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    // Display list of roles and permissions
    public function index()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $roles = Role::with('permissions')->get();
        } else {
            $roles = Role::where('partner_id', $user->partner_id)->with('permissions')->get();
        }
        return view('roles-permissions.index', compact('roles'));
    }

    public function permissions()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $roles = Role::with('permissions')->get();
        } else {
            $roles = Role::where('partner_id', $user->partner_id)->with('permissions')->get();
        }
        $permissions = Permission::all();
        return view('roles-permissions.permissions', compact('permissions'));
    }

    // Show form to create a new role
    public function createRole()
    {
        $permissions = Permission::all();
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
        }
        return view('roles-permissions.create-role', compact('permissions', 'partners'));
    }

    // Store new role in the database
    public function storeRole(Request $request)
    {
        try {
            $request->validate([
                'name' => [
                    'required',
                    Rule::unique('roles'),
                ],
                'permissions' => 'array',
            ]);

            $role = Role::create([
                'name' => $request->name,
                'partner_id' => $request->partner_id,
            ]);

            $role->syncPermissions($request->permissions);
            return redirect()->route('roles.index')->with('success', 'Role created successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // Show form to edit an existing role
    public function editRole(Role $role)
    {
        $permissions = Permission::all();
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
        }
        return view('roles-permissions.edit-role', compact('role', 'permissions', 'partners'));
    }

    // Update an existing role in the database
    public function updateRole(Request $request, Role $role)
    {
        try {
            $request->validate([
                'name' => [
                    'required',
                    Rule::unique('roles')->ignore($role->id),
                ],
                'permissions' => 'array',
            ]);


            $role->name = $request->name;
            $role->partner_id = $request->partner_id;
            $role->syncPermissions($request->permissions);
            $role->save();

            return redirect()->route('roles.index')->with('success', 'Role updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // Delete a role
    public function deleteRole(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
