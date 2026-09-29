<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password request form.
     */
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the forgot password request — generate token and send email.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        // Always show a success message to prevent email enumeration
        if (! $user) {
            return back()->with(
                'status',
                'Jika email tersebut terdaftar, kami telah mengirimkan tautan reset kata sandi. Silakan cek kotak masuk Anda.'
            );
        }

        // Delete old token if exists
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Send reset email
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        try {
            Mail::send('auth.emails.reset-password', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'expiresInMinutes' => 60,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Reset Kata Sandi — SisRec Rekrutmen');
            });
        } catch (\Exception $e) {
            // Log the error but continue — in production configure mail properly
            logger()->error('Failed to send reset password email: '.$e->getMessage());
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'PASSWORD_RESET_REQUEST',
            'module' => 'Authentication',
            'description' => "Permintaan reset kata sandi untuk akun: {$user->email}.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with(
            'status',
            'Jika email tersebut terdaftar, kami telah mengirimkan tautan reset kata sandi. Silakan cek kotak masuk Anda.'
        );
    }

    /**
     * Show the password reset form (with token).
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Handle the password reset form submission.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->input('email'))
            ->first();

        if (! $record || ! Hash::check($request->input('token'), $record->token)) {
            return back()->withErrors(['token' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa.'])->withInput();
        }

        // Check token expiry (60 minutes)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->input('email'))->delete();

            return back()->withErrors(['token' => 'Tautan reset kata sandi sudah kedaluwarsa. Silakan minta tautan baru.'])->withInput();
        }

        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Akun dengan email tersebut tidak ditemukan.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->input('email'))->delete();

        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'PASSWORD_RESET_SUCCESS',
            'module' => 'Authentication',
            'description' => "Kata sandi berhasil direset untuk akun: {$user->email}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil direset! Silakan masuk dengan kata sandi baru Anda.');
    }
}
