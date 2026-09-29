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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            if ($user->is_blacklisted) {
                ActivityLog::record(
                    'LOGIN_BLOCKED_BLACKLIST',
                    'Authentication',
                    "Percobaan masuk akun yang di-blacklist: {$user->email}.",
                    $user,
                    $request->ip()
                );

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $reason = $user->blacklist_reason ? " Alasan: {$user->blacklist_reason}." : '';

                return back()->withErrors([
                    'email' => "Akun Anda telah dimasukkan ke dalam daftar hitam (blacklist) oleh Administrator.{$reason} Silakan hubungi tim HR/Admin.",
                ]);
            }

            if (! $user->is_active) {
                ActivityLog::record(
                    'LOGIN_BLOCKED',
                    'Authentication',
                    "Percobaan masuk akun nonaktif: {$user->email}.",
                    $user,
                    $request->ip()
                );

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda saat ini dinonaktifkan. Silakan hubungi administrator.',
                ]);
            }

            ActivityLog::record(
                'LOGIN_SUCCESS',
                'Authentication',
                "Pengguna {$user->name} ({$user->email}) berhasil masuk ke sistem.",
                $user,
                $request->ip()
            );

            return $this->redirectBasedOnRole($user)
                ->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        ActivityLog::record(
            'LOGIN_FAILED',
            'Authentication',
            "Percobaan masuk gagal untuk email: {$credentials['email']}.",
            null,
            $request->ip()
        );

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Show the candidate registration form.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Handle candidate registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:candidates,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'nik' => ['required', 'string', 'digits:16', 'unique:candidate_profiles,nik'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'first_name.required' => 'Nama depan wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'phone_number.required' => 'Nomor telepon/WhatsApp wajib diisi.',
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi. 1 akun hanya dapat didaftarkan untuk 1 NIK.',
            'nik.digits' => 'Nomor Induk Kependudukan (NIK) harus berjumlah 16 digit angka sesuai KTP.',
            'nik.unique' => 'NIK ini sudah terdaftar pada akun lain. 1 kandidat hanya dapat memiliki 1 akun dengan 1 NIK.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $fullName = trim($validated['first_name'].' '.($validated['last_name'] ?? ''));

        DB::transaction(function () use ($validated, $fullName, &$user) {
            // Get or create candidate role
            $roleKandidat = Role::firstOrCreate(
                ['name' => 'kandidat'],
                [
                    'display_name' => 'Kandidat / Pelamar',
                    'description' => 'Akses portal karir dan pengisian formulir lamaran.',
                ]
            );

            // Create User account
            $user = User::create([
                'role_id' => $roleKandidat->id,
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            // Create Candidate profile
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'is_active' => true,
            ]);

            // Initial candidate profile
            $nikVal = ! empty($validated['nik']) ? trim($validated['nik']) : null;
            CandidateProfile::create([
                'candidate_id' => $candidate->id,
                'nik' => $nikVal,
                'summary' => 'Pelamar baru terdaftar di Sistem Recruitment.',
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        if ($user) {
            ActivityLog::record(
                'REGISTER',
                'Authentication',
                "Registrasi kandidat baru: {$user->name} ({$user->email}).",
                $user,
                $request->ip()
            );
        }

        return redirect()->route('kadidat.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di Portal Karir, '.$fullName.'.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $loggedOutUser = Auth::user();

        if ($loggedOutUser) {
            ActivityLog::record(
                'LOGOUT',
                'Authentication',
                "Pengguna {$loggedOutUser->name} ({$loggedOutUser->email}) keluar dari sistem.",
                $loggedOutUser,
                $request->ip()
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Redirect to the appropriate dashboard based on user role.
     */
    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        $roleName = $user->role?->name;

        return match ($roleName) {
            'admin' => redirect()->route('admin.dashboard'),
            'hr' => redirect()->route('hr.dashboard'),
            'interviewer' => redirect()->route('interview.dashboard'),
            'kandidat' => redirect()->route('kadidat.dashboard'),
            default => redirect()->route('hr.dashboard'),
        };
    }
}
