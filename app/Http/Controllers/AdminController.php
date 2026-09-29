<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['stopImpersonate']);
        $this->middleware('auth')->only(['stopImpersonate']);
    }

    public function dashboard()
    {
        $totalUser = User::count();

        // Roles count
        $rolesCount = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $stats = [
            'total_user' => $totalUser,
            'admin' => $rolesCount['admin'] ?? 0,
            'osm_service_manager' => $rolesCount['osm_service_manager'] ?? 0,
            'osm_qc' => $rolesCount['osm_qc'] ?? 0,
            'dmo' => $rolesCount['dmo'] ?? 0,
            'procurement' => $rolesCount['procurement'] ?? 0,
        ];

        $overdueInvoices = \App\Models\Invoice::where('payment_status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'overdueInvoices'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $statusFilter = $request->input('status');
        $query = User::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'roleFilter', 'statusFilter'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,osm_service_manager,osm_qc,dmo,procurement',
            'phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($request->password);
        $validated['status'] = 'active';

        $user = User::create($validated);

        ActivityLog::log(
            'CREATE',
            'user',
            $user->id,
            null,
            $user->makeHidden(['password', 'remember_token'])->toArray(),
            "Menambahkan user baru: {$user->name} ({$user->role})"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil ditambahkan.");
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,osm_service_manager,osm_qc,dmo,procurement',
            'status' => 'nullable|in:active,inactive',
            'phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        // Self-protection on status change
        if ($user->id === auth()->id() && isset($validated['status']) && $validated['status'] === 'inactive') {
            return back()->withErrors(['error' => 'Anda tidak bisa menonaktifkan akun Anda sendiri.']);
        }

        $oldData = $user->makeHidden(['password', 'remember_token'])->toArray();

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        $newData = $user->makeHidden(['password', 'remember_token'])->toArray();

        ActivityLog::log(
            'UPDATE',
            'user',
            $user->id,
            $oldData,
            $newData,
            "Memperbarui profil/status user: {$user->name}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $oldStatus = $user->status;
        $newStatus = ($oldStatus === 'active') ? 'inactive' : 'active';

        $user->status = $newStatus;
        $user->save();

        ActivityLog::log(
            'UPDATE',
            'user',
            $user->id,
            ['status' => $oldStatus],
            ['status' => $newStatus],
            "Mengubah status akun {$user->name} menjadi {$newStatus}"
        );

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $user->password = Hash::make('password');
        $user->save();

        ActivityLog::log(
            'UPDATE',
            'user',
            $user->id,
            null,
            null,
            "Mereset password user: {$user->name} ke default"
        );

        return back()->with('success', "Password untuk akun {$user->name} berhasil di-reset menjadi 'password'.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $oldData = $user->makeHidden(['password', 'remember_token'])->toArray();
        $name = $user->name;

        $user->delete();

        ActivityLog::log(
            'DELETE',
            'user',
            $id,
            $oldData,
            null,
            "Menghapus akun pengguna: {$name}"
        );

        return back()->with('success', "Akun user {$name} berhasil dihapus.");
    }

    /**
     * Masuk sementara sebagai user lain untuk mengecek hak akses / testing.
     */
    public function impersonate($id)
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === auth()->id()) {
            return back()->with('warning', 'Anda sudah berada di akun ini.');
        }

        $admin = auth()->user();
        session()->put('impersonator_id', $admin->id);

        ActivityLog::log(
            'IMPERSONATE_START',
            'user',
            (string) $targetUser->id,
            null,
            null,
            "Admin {$admin->name} masuk sebagai user {$targetUser->name} ({$targetUser->role})"
        );

        auth()->login($targetUser);

        return redirect()->route('dashboard')
            ->with('info', "Anda sedang login sebagai {$targetUser->name} (Role: {$targetUser->role}).");
    }

    /**
     * Keluar dari mode impersonate dan kembali ke akun Admin.
     */
    public function stopImpersonate()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $adminId = session()->pull('impersonator_id');
        $admin = User::findOrFail($adminId);

        ActivityLog::log(
            'IMPERSONATE_STOP',
            'user',
            (string) auth()->id(),
            null,
            null,
            "Admin {$admin->name} keluar dari mode impersonate akun " . auth()->user()->name
        );

        auth()->login($admin);

        return redirect()->route('admin.users.index')
            ->with('success', "Kembali ke akun Administrator ({$admin->name}).");
    }
}
