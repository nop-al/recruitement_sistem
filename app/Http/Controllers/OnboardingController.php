<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Onboarding;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    /**
     * Display a listing of onboarding candidates for Admin/HR.
     */
    public function index(Request $request): View
    {
        $query = Onboarding::with(['candidate.user', 'position', 'offering']);

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('candidate', function ($cq) use ($search) {
                    $cq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('position', function ($pq) use ($search) {
                    $pq->where('title', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status') && array_key_exists($request->query('status'), Onboarding::STATUSES)) {
            $query->where('status', $request->query('status'));
        }

        $onboardings = $query->latest()->paginate(10)->withQueryString();
        $positions = Position::orderBy('title')->get();

        return view('onboardings.index', [
            'onboardings' => $onboardings,
            'positions' => $positions,
            'statuses' => Onboarding::STATUSES,
            'selectedStatus' => $request->query('status'),
            'search' => $request->query('search'),
        ]);
    }

    /**
     * Display the specified onboarding checklist and verification panel.
     */
    public function show(Onboarding $onboarding): View
    {
        $onboarding->load(['candidate.user', 'candidate.profile', 'position', 'offering', 'application']);

        return view('onboardings.show', [
            'onboarding' => $onboarding,
        ]);
    }

    /**
     * Toggle verification status for a specific checklist document or data.
     */
    public function verifyItem(Request $request, Onboarding $onboarding): RedirectResponse
    {
        $validated = $request->validate([
            'item' => ['required', 'string', 'in:ktp,npwp,bpjs,bank,ijazah,pas_foto,contract,emergency'],
            'verified' => ['required', 'boolean'],
        ]);

        $itemFieldMap = [
            'ktp' => 'ktp_verified',
            'npwp' => 'npwp_verified',
            'bpjs' => 'bpjs_verified',
            'bank' => 'bank_verified',
            'ijazah' => 'ijazah_verified',
            'pas_foto' => 'pas_foto_verified',
            'contract' => 'contract_signed',
            'emergency' => 'emergency_verified',
        ];

        $targetField = $itemFieldMap[$validated['item']];
        $onboarding->{$targetField} = (bool) $validated['verified'];
        $onboarding->updateCalculatedPercentage();
        $onboarding->save();

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.onboardings.show", $onboarding->id)
            ->with('success', 'Status verifikasi berkas berhasil diperbarui.');
    }

    /**
     * Update overall onboarding status (e.g. mark as Completed -> Hired).
     */
    public function updateStatus(Request $request, Onboarding $onboarding): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Onboarding::STATUSES))],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $onboarding->status = $validated['status'];
        if (! empty($validated['notes'])) {
            $onboarding->notes = $validated['notes'];
        }

        if ($validated['status'] === Onboarding::STATUS_COMPLETED) {
            $onboarding->completed_at = Carbon::now();
            $onboarding->checklist_percentage = 100;

            // Transition application stage to hired
            if ($onboarding->application) {
                $onboarding->application->update([
                    'stage' => 'hired',
                    'status' => 'passed',
                ]);
            }
        }

        $onboarding->save();

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.onboardings.show", $onboarding->id)
            ->with('success', 'Status proses onboarding berhasil diperbarui.');
    }

    // =========================================================================
    // CANDIDATE ONBOARDING PORTAL METHODS
    // =========================================================================

    /**
     * Display candidate's onboarding checklist and document submission portal.
     */
    public function candidateIndex(): View
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        $onboarding = Onboarding::where('candidate_id', $candidate->id)
            ->with(['position', 'offering', 'application'])
            ->latest()
            ->first();

        return view('kadidat.onboarding.index', [
            'onboarding' => $onboarding,
            'candidate' => $candidate,
        ]);
    }

    /**
     * Candidate submits or updates their 8 onboarding checklist items.
     */
    public function candidateSubmit(Request $request, Onboarding $onboarding): RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        if ($onboarding->candidate_id !== $candidate->id) {
            abort(403, 'Akses proses onboarding ini tidak diizinkan.');
        }

        $profile = $candidate->profile;

        $validated = $request->validate([
            'ktp_number' => [
                'nullable',
                'string',
                'digits:16',
                Rule::unique('candidate_profiles', 'nik')->ignore($profile?->id),
            ],
            'ktp_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'npwp_number' => ['nullable', 'string', 'max:30'],
            'npwp_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'bpjs_number' => ['nullable', 'string', 'max:30'],
            'bpjs_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],
            'bank_book_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'ijazah_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'pas_foto_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'contract_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
        ], [
            'ktp_number.digits' => 'Nomor KTP (NIK) harus berjumlah 16 digit angka.',
            'ktp_number.unique' => 'Nomor NIK/KTP ini sudah terdaftar pada akun lain. 1 NIK hanya dapat digunakan untuk 1 akun.',
        ]);

        $folder = "onboarding/{$candidate->id}";

        // Upload files if provided
        $fileFields = ['ktp_file', 'npwp_file', 'bpjs_file', 'bank_book_file', 'ijazah_file', 'pas_foto_file', 'contract_file'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store($folder, 'local');
            }
        }

        $onboarding->fill($validated);
        $onboarding->updateCalculatedPercentage();
        $onboarding->save();

        if (! empty($validated['ktp_number']) && $profile) {
            $profile->update(['nik' => trim($validated['ktp_number'])]);
        }

        return redirect()->route('kadidat.onboarding.index')
            ->with('success', 'Data dan berkas onboarding berhasil diunggah dan disimpan.');
    }
}
