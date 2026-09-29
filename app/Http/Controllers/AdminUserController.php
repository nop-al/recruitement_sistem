<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Candidate;
use App\Models\CandidateProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all system users.
     */
    public function index(Request $request): View
    {
        $query = User::with(['role', 'candidate.profile'])->latest();

        // Search by keyword (name, email, phone, or candidate NIK)
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('candidate', function ($cQuery) use ($search) {
                        $cQuery->where('phone_number', 'like', "%{$search}%")
                            ->orWhereHas('profile', function ($pQuery) use ($search) {
                                $pQuery->where('nik', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role_id', $request->query('role'));
        }

        // Filter by status (active, inactive, blacklisted)
        if ($request->filled('status')) {
            $status = $request->query('status');
            if ($status === 'blacklisted') {
                $query->where('is_blacklisted', true);
            } elseif ($status === 'active') {
                $query->where('is_active', true)->where('is_blacklisted', false);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false)->where('is_blacklisted', false);
            }
        }

        $users = $query->paginate(12)->withQueryString();
        $roles = Role::orderBy('name')->get();

        // Summary statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->where('is_blacklisted', false)->count(),
            'blacklisted' => User::where('is_blacklisted', true)->count(),
            'candidates' => User::whereHas('role', function ($q) {
                $q->where('name', 'kandidat');
            })->count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'stats' => $stats,
            'searchTerm' => $request->query('search'),
            'selectedRole' => $request->query('role'),
            'selectedStatus' => $request->query('status'),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::all();

        return view('admin.users.create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'nik' => ['nullable', 'string', 'digits:16', 'unique:candidate_profiles,nik'],
            'is_active' => ['nullable'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'role_id.required' => 'Pilih peran / role pengguna.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'nik.digits' => 'Nomor Induk Kependudukan (NIK) harus berjumlah 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar pada akun kandidat lain. 1 NIK hanya untuk 1 akun.',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $role->id,
            'password' => Hash::make($validated['password']),
            'is_active' => $request->has('is_active'),
            'is_blacklisted' => false,
        ]);

        // If candidate role, create corresponding candidate record and profile
        if ($role->name === 'kandidat') {
            $nameParts = explode(' ', $validated['name'], 2);
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $validated['email'],
                'phone_number' => $request->input('phone_number', '081234567890'),
                'is_active' => $user->is_active,
            ]);

            $nikVal = ! empty($validated['nik']) ? trim($validated['nik']) : null;
            CandidateProfile::create([
                'candidate_id' => $candidate->id,
                'nik' => $nikVal,
                'summary' => 'Profil kandidat dibuat melalui Panel Administrator.',
            ]);
        }

        // Record activity log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'CREATE_USER',
            'module' => 'User Management',
            'description' => "Menambahkan pengguna baru: {$user->name} ({$user->email}) dengan peran {$role->display_name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna {$user->name} ({$role->display_name}) berhasil dibuat!");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $user->load(['role', 'candidate.profile']);
        $roles = Role::all();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $candidate = $user->candidate;
        $profile = $candidate?->profile;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable'],
            'is_blacklisted' => ['nullable'],
            'blacklist_reason' => ['nullable', 'string', 'max:1000'],
        ];

        // If user is or becomes candidate, validate NIK
        $selectedRole = Role::find($request->input('role_id'));
        if ($selectedRole && $selectedRole->name === 'kandidat') {
            $rules['nik'] = [
                'nullable',
                'string',
                'digits:16',
                Rule::unique('candidate_profiles', 'nik')->ignore($profile?->id),
            ];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'role_id.required' => 'Peran sistem wajib dipilih.',
            'nik.digits' => 'Nomor Induk Kependudukan (NIK) harus berjumlah 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar pada akun kandidat lain. 1 NIK hanya untuk 1 akun.',
        ]);

        $isBlacklisted = $request->boolean('is_blacklisted');
        $isActive = $request->boolean('is_active');

        // Prevent self-lockout from admin role
        if ($user->id === Auth::id() && $selectedRole && $selectedRole->name !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri menjadi non-administrator.');
        }

        // Prevent self-blacklist
        if ($user->id === Auth::id() && $isBlacklisted) {
            return back()->with('error', 'Anda tidak dapat mem-blacklist akun Anda sendiri.');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'is_active' => $isActive,
            'is_blacklisted' => $isBlacklisted,
            'blacklist_reason' => $isBlacklisted ? ($request->input('blacklist_reason') ?: $user->blacklist_reason ?: 'Ditandai blacklist oleh administrator.') : null,
            'blacklisted_at' => $isBlacklisted ? ($user->blacklisted_at ?: now()) : null,
        ]);

        // Sync role permission
        if ($user->role) {
            $user->syncRoles([$user->role->name]);
        }

        // Handle candidate profile update if applicable
        if ($selectedRole && $selectedRole->name === 'kandidat') {
            $nameParts = explode(' ', trim($validated['name']), 2);
            if (! $candidate) {
                $candidate = Candidate::create([
                    'user_id' => $user->id,
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? '',
                    'email' => $user->email,
                    'phone_number' => $request->input('phone_number', '081200000000'),
                    'is_active' => $user->is_active,
                ]);
            } else {
                $candidate->update([
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? '',
                    'email' => $user->email,
                    'phone_number' => $request->input('phone_number', $candidate->phone_number),
                    'is_active' => $user->is_active,
                ]);
            }

            $profile = CandidateProfile::firstOrNew(['candidate_id' => $candidate->id]);
            if ($request->filled('nik')) {
                $profile->nik = trim($request->input('nik'));
            }
            $profile->save();
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'UPDATE_USER',
            'module' => 'User Management',
            'description' => "Memperbarui data profil pengguna: {$user->name} ({$user->email}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Profil pengguna {$user->name} berhasil diperbarui!");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri demi keamanan sistem.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // If user has candidate record, clean up cascade/associated files if needed
        if ($user->candidate) {
            $user->candidate->delete();
        }

        $user->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'DELETE_USER',
            'module' => 'User Management',
            'description' => "Menghapus akun pengguna: {$userName} ({$userEmail}).",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna '{$userName}' ({$userEmail}) berhasil dihapus dari sistem.");
    }

    /**
     * Toggle blacklist status of a user.
     */
    public function toggleBlacklist(Request $request, User $user): RedirectResponse
    {
        // Prevent self-blacklist
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mem-blacklist akun Anda sendiri.');
        }

        if ($user->is_blacklisted) {
            // Un-blacklist
            $user->update([
                'is_blacklisted' => false,
                'blacklist_reason' => null,
                'blacklisted_at' => null,
                'is_active' => true,
            ]);

            if ($user->candidate) {
                $user->candidate->update(['is_active' => true]);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name ?? 'Admin',
                'action' => 'UNBLACKLIST_USER',
                'module' => 'User Management',
                'description' => "Mengeluarkan pengguna {$user->name} ({$user->email}) dari daftar hitam (blacklist).",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->back()
                ->with('success', "Status blacklist pengguna '{$user->name}' berhasil dicabut. Akun telah aktif kembali.");
        } else {
            // Blacklist
            $reason = trim($request->input('reason', 'Ditandai blacklist oleh administrator karena pelanggaran ketentuan sistem.'));
            if (empty($reason)) {
                $reason = 'Ditandai blacklist oleh administrator karena pelanggaran ketentuan sistem.';
            }

            $user->update([
                'is_blacklisted' => true,
                'blacklist_reason' => $reason,
                'blacklisted_at' => now(),
                'is_active' => false,
            ]);

            if ($user->candidate) {
                $user->candidate->update(['is_active' => false]);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name ?? 'Admin',
                'action' => 'BLACKLIST_USER',
                'module' => 'User Management',
                'description' => "Memasukkan pengguna {$user->name} ({$user->email}) ke dalam daftar hitam (blacklist). Alasan: {$reason}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->back()
                ->with('success', "Pengguna '{$user->name}' berhasil dimasukkan ke dalam daftar blacklist.");
        }
    }

    /**
     * Reset the user password by administrator.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => ['nullable', 'string', 'min:8'],
        ], [
            'new_password.min' => 'Kata sandi baru minimal terdiri dari 8 karakter.',
        ]);

        // Generate password if not provided
        $plainPassword = $validated['new_password'] ?: 'Recruit'.rand(1000, 9999).'!#';

        $user->update([
            'password' => Hash::make($plainPassword),
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'RESET_PASSWORD',
            'module' => 'User Management',
            'description' => "Mereset kata sandi akun pengguna: {$user->name} ({$user->email}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()
            ->with('password_reset_success', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $plainPassword,
            ])
            ->with('success', "Kata sandi untuk pengguna '{$user->name}' berhasil disetel ulang!");
    }
}
