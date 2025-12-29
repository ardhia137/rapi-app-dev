<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Traits\ActivityLogger;

class UserController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        $users = User::with('roles')->paginate(15);

        // Load activity logs untuk super admin
        $activityLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.index', compact('users', 'activityLogs'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'status' => 'nullable|in:active,inactive',
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->status = $validated['status'] ?? 'active';
        $user->save();

        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Log aktivitas create user
        self::logCreate($user, 'Pengguna', 'Pengguna');

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|in:active,inactive',
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        // Simpan nilai lama untuk logging
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'roles' => $user->roles->pluck('name')->toArray(),
        ];

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        if (isset($validated['status'])) {
            $user->status = $validated['status'];
        }
        $user->save();

        if ($request->has('roles')) {
            $user->syncRoles($validated['roles'] ?? []);
        }

        // Nilai baru untuk logging
        $newValues = [
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'roles' => $user->roles->pluck('name')->toArray(),
        ];

        // Log aktivitas update user
        self::logUpdate($user, 'Pengguna', $oldValues, $newValues, 'Pengguna');

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        // Log aktivitas sebelum delete
        self::logDelete($user, 'Pengguna', 'Pengguna');

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
