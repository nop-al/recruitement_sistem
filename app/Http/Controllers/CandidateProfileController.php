<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateProfile;
use App\Models\CandidateSkill;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CandidateProfileController extends Controller
{
    /**
     * Helper to get or create the candidate record for the authenticated user.
     */
    private function getCandidate(): Candidate
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->first();

        if (! $candidate) {
            $nameParts = explode(' ', trim($user->name), 2);
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'first_name' => $nameParts[0] ?? 'Kandidat',
                'last_name' => $nameParts[1] ?? '',
                'email' => $user->email,
                'phone_number' => '081200000000',
                'is_active' => true,
            ]);

            CandidateProfile::create([
                'candidate_id' => $candidate->id,
            ]);
        }

        return $candidate;
    }

    /**
     * Show the profile edit and CV upload form.
     */
    public function edit(): View
    {
        $candidate = $this->getCandidate();
        $candidate->load(['profile', 'education', 'experiences', 'skills']);

        return view('kadidat.profile', [
            'candidate' => $candidate,
            'profile' => $candidate->profile ?? new CandidateProfile(['candidate_id' => $candidate->id]),
        ]);
    }

    /**
     * Update the candidate's personal profile, biodata, and upload CV/photo.
     */
    public function update(Request $request): RedirectResponse
    {
        $candidate = $this->getCandidate();
        $profile = CandidateProfile::firstOrNew(['candidate_id' => $candidate->id]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:30'],
            'gender' => ['nullable', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'nik' => [
                'required',
                'string',
                'digits:16',
                Rule::unique('candidate_profiles', 'nik')->ignore($profile->id ?? null),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // max 10MB
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // max 2MB
        ], [
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi. 1 akun kandidat terhubung dengan 1 NIK.',
            'nik.digits' => 'Nomor Induk Kependudukan (NIK) harus berjumlah 16 digit angka sesuai KTP.',
            'nik.unique' => 'NIK ini sudah terdaftar pada akun lain. 1 kandidat hanya dapat memiliki 1 akun dengan 1 NIK.',
        ]);

        // 1. Update Candidate base record
        $candidate->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? '',
            'phone_number' => $validated['phone_number'],
        ]);

        // 2. Prepare Profile fields
        $profile->gender = $validated['gender'] ?? $profile->gender;
        $profile->date_of_birth = $validated['date_of_birth'] ?? $profile->date_of_birth;
        $profile->place_of_birth = $validated['place_of_birth'] ?? $profile->place_of_birth;
        $profile->nik = ! empty($validated['nik']) ? trim($validated['nik']) : null;
        $profile->address = $validated['address'] ?? $profile->address;
        $profile->city = $validated['city'] ?? $profile->city;
        $profile->province = $validated['province'] ?? $profile->province;
        $profile->postal_code = $validated['postal_code'] ?? $profile->postal_code;
        $profile->linkedin_url = $validated['linkedin_url'] ?? $profile->linkedin_url;
        $profile->portfolio_url = $validated['portfolio_url'] ?? $profile->portfolio_url;
        $profile->summary = $validated['summary'] ?? $profile->summary;

        // 3. Handle CV / Resume File Upload
        if ($request->hasFile('resume')) {
            $resumeFile = $request->file('resume');
            $extension = $resumeFile->getClientOriginalExtension();
            $resumeFileName = 'cv_'.$candidate->id.'_'.time().'.'.$extension;
            $resumePath = $resumeFile->storeAs('resumes', $resumeFileName, 'local');
            $profile->resume_path = $resumePath;

            // Also synchronize with any active applications missing a CV
            $candidate->applications()->whereNull('resume_url')->update([
                'resume_url' => $resumePath,
            ]);
        }

        // 4. Handle Profile Photo Upload
        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $extension = $photoFile->getClientOriginalExtension();
            $photoFileName = 'photo_'.$candidate->id.'_'.time().'.'.$extension;
            $photoPath = $photoFile->storeAs('photos', $photoFileName, 'public');
            $profile->photo = $photoPath;
        }

        $profile->save();

        return redirect()->route('kadidat.profile.edit')
            ->with('success', 'Data profil & dokumen CV berhasil disimpan dan diperbarui!');
    }

    /**
     * Store new education record.
     */
    public function storeEducation(Request $request): RedirectResponse
    {
        $candidate = $this->getCandidate();

        $validated = $request->validate([
            'institution_name' => ['required', 'string', 'max:255'],
            'degree' => ['required', 'string', 'max:100'],
            'field_of_study' => ['required', 'string', 'max:100'],
            'gpa' => ['nullable', 'numeric', 'between:0,4.00'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        CandidateEducation::create([
            'candidate_id' => $candidate->id,
            'institution_name' => $validated['institution_name'],
            'degree' => $validated['degree'],
            'field_of_study' => $validated['field_of_study'],
            'gpa' => $validated['gpa'] ?? null,
            'start_date' => $validated['start_date'] ?? now()->subYears(4)->format('Y-m-d'),
            'end_date' => empty($validated['is_current']) ? ($validated['end_date'] ?? null) : null,
            'is_current' => $request->boolean('is_current'),
        ]);

        return redirect()->route('kadidat.profile.edit')
            ->with('success', 'Riwayat pendidikan berhasil ditambahkan!');
    }

    /**
     * Delete education record.
     */
    public function destroyEducation(CandidateEducation $education): RedirectResponse
    {
        $candidate = $this->getCandidate();

        if ($education->candidate_id === $candidate->id) {
            $education->delete();

            return redirect()->route('kadidat.profile.edit')
                ->with('success', 'Riwayat pendidikan berhasil dihapus.');
        }

        return redirect()->route('kadidat.profile.edit')
            ->with('error', 'Anda tidak memiliki hak untuk menghapus data ini.');
    }

    /**
     * Store new experience record.
     */
    public function storeExperience(Request $request): RedirectResponse
    {
        $candidate = $this->getCandidate();

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        CandidateExperience::create([
            'candidate_id' => $candidate->id,
            'company_name' => $validated['company_name'],
            'position' => $validated['position'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? now()->subYears(2)->format('Y-m-d'),
            'end_date' => empty($validated['is_current']) ? ($validated['end_date'] ?? null) : null,
            'is_current' => $request->boolean('is_current'),
        ]);

        return redirect()->route('kadidat.profile.edit')
            ->with('success', 'Riwayat pengalaman kerja berhasil ditambahkan!');
    }

    /**
     * Delete experience record.
     */
    public function destroyExperience(CandidateExperience $experience): RedirectResponse
    {
        $candidate = $this->getCandidate();

        if ($experience->candidate_id === $candidate->id) {
            $experience->delete();

            return redirect()->route('kadidat.profile.edit')
                ->with('success', 'Riwayat pengalaman kerja berhasil dihapus.');
        }

        return redirect()->route('kadidat.profile.edit')
            ->with('error', 'Anda tidak memiliki hak untuk menghapus data ini.');
    }

    /**
     * Store new skill record.
     */
    public function storeSkill(Request $request): RedirectResponse
    {
        $candidate = $this->getCandidate();

        $validated = $request->validate([
            'skill_name' => ['required', 'string', 'max:100'],
            'proficiency_level' => ['required', 'in:beginner,intermediate,advanced,expert'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        CandidateSkill::create([
            'candidate_id' => $candidate->id,
            'skill_name' => $validated['skill_name'],
            'proficiency_level' => $validated['proficiency_level'],
            'years_of_experience' => $validated['years_of_experience'],
        ]);

        return redirect()->route('kadidat.profile.edit')
            ->with('success', 'Keahlian baru berhasil ditambahkan!');
    }

    /**
     * Delete skill record.
     */
    public function destroySkill(CandidateSkill $skill): RedirectResponse
    {
        $candidate = $this->getCandidate();

        if ($skill->candidate_id === $candidate->id) {
            $skill->delete();

            return redirect()->route('kadidat.profile.edit')
                ->with('success', 'Keahlian berhasil dihapus.');
        }

        return redirect()->route('kadidat.profile.edit')
            ->with('error', 'Anda tidak memiliki hak untuk menghapus data ini.');
    }
}
