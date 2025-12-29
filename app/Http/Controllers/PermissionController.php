<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function create()
    {
        return view('admin.permissions.create');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $validated['name']]);
        return redirect()->route('permissions.index')->with('status', 'Permission created');
    }

    public function show(Permission $permission)
    {
        return redirect()->route('permissions.index');
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->name = $validated['name'];
        $permission->save();
        return redirect()->route('permissions.index')->with('status', 'Permission updated');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('status', 'Permission deleted');
    }
}
