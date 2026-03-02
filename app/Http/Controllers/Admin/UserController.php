<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ActivityCategory;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['roles', 'orders.package', 'containers']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form to create a new admin user.
     */
    public function createAdmin()
    {
        return view('admin.users.create-admin');
    }

    /**
     * Store a new admin user.
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('admin');

        ActivityLog::record(
            category: ActivityCategory::Admin,
            event: 'admin_created',
            description: "Admin baru '{$user->name}' ({$user->email}) ditambahkan.",
            metadata: ['user_id' => $user->id],
        );

        return redirect()->route('admin.users.index')->with('success', "Admin {$user->name} berhasil ditambahkan.");
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate(['role' => ['required', 'in:admin,user']]);

        abort_if($user->id === Auth::id(), 422, 'Tidak dapat mengubah role sendiri.');

        $user->syncRoles([$request->role]);

        ActivityLog::record(
            category: ActivityCategory::Admin,
            event: 'user_role_updated',
            description: "Role user '{$user->name}' diubah menjadi '{$request->role}'.",
            metadata: ['user_id' => $user->id, 'role' => $request->role],
        );

        return redirect()->back()->with('success', 'Role user diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === (int) auth('user')->id(), 422, 'Tidak dapat menghapus akun sendiri.');

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User dihapus.');
    }
}
