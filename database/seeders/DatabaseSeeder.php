<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateProfile;
use App\Models\CandidateSkill;
use App\Models\CandidateTestResult;
use App\Models\InterviewAssessment;
use App\Models\Offering;
use App\Models\Onboarding;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $roleAdmin = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Akses penuh seluruh sistem, pengaturan pengguna, role, dan audit log.',
        ]);

        $roleHr = Role::create([
            'name' => 'hr',
            'display_name' => 'HR & Recruitment Officer',
            'description' => 'Mengelola lowongan kerja, screening pelamar, penjadwalan interview, penawaran kerja (offering), dan onboarding.',
        ]);

        $roleInterviewer = Role::create([
            'name' => 'interviewer',
            'display_name' => 'User & Technical Interviewer',
            'description' => 'Melakukan wawancara pengguna (user interview), menilai kompetensi kandidat, dan memberikan rekomendasi kelulusan.',
        ]);

        $roleKandidat = Role::create([
            'name' => 'kandidat',
            'display_name' => 'Kandidat / Pelamar',
            'description' => 'Melamar lowongan kerja, melengkapi data profil/CV, memantau status seleksi, dan mengikuti tes/interview.',
        ]);

        // Seed Spatie RBAC Permissions and assign to roles
        $this->call(PermissionSeeder::class);

        // 2. Seed Users
        $defaultPassword = Hash::make('password');

        $userAdmin = User::create([
            'role_id' => $roleAdmin->id,
            'name' => 'Nauval Amarullah (Super Admin)',
            'email' => 'admin@recruitment.test',
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $userHr = User::create([
            'role_id' => $roleHr->id,
            'name' => 'Sarah Wijaya, M.Psi (Talent Acquisition)',
            'email' => 'hr@recruitment.test',
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $userInterviewer = User::create([
            'role_id' => $roleInterviewer->id,
            'name' => 'Bambang Sudarmono (VP of Engineering)',
            'email' => 'interviewer@recruitment.test',
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $userKandidat = User::create([
            'role_id' => $roleKandidat->id,
            'name' => 'Ahmad Fauzi',
            'email' => 'kandidat@recruitment.test',
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        // Additional Team Users for realistic listing
        User::create([
            'role_id' => $roleHr->id,
            'name' => 'Maya Kartika (Recruitment Specialist)',
            'email' => 'maya.hr@recruitment.test',
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        User::create([
            'role_id' => $roleInterviewer->id,
            'name' => 'Dimas Wicaksono (Lead Product Designer)',
            'email' => 'dimas.lead@recruitment.test',
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        User::create([
            'role_id' => $roleAdmin->id,
            'name' => 'Randi Pratama (Security & IT Ops)',
            'email' => 'randi.it@recruitment.test',
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        // 3. Seed Positions (Lowongan Kerja)
        $pos1 = Position::create([
            'title' => 'Senior Full-Stack Developer (Laravel + Vue/React)',
            'department' => 'Technology & Engineering',
            'location' => 'Jakarta Selatan (Hybrid 3x WFO)',
            'type' => 'Full-time',
            'experience_level' => 'Senior Level (4+ Tahun)',
            'salary_min' => 18000000,
            'salary_max' => 26000000,
            'description' => 'Bertanggung jawab atas arsitektur backend Laravel, perancangan API performa tinggi, dan integrasi frontend modern.',
            'requirements' => 'Keahlian mendalam dalam PHP 8+, Laravel 11/12, PostgreSQL/MySQL, Redis, RESTful API, dan CI/CD.',
            'status' => 'active',
            'deadline' => Carbon::now()->addDays(20),
            'quota' => 2,
        ]);

        $pos2 = Position::create([
            'title' => 'Product Design Lead (UI/UX)',
            'department' => 'Product & Design',
            'location' => 'Jakarta Pusat (Hybrid)',
            'type' => 'Full-time',
            'experience_level' => 'Lead Level (5+ Tahun)',
            'salary_min' => 17000000,
            'salary_max' => 24000000,
            'description' => 'Memimpin desain produk end-to-end, merancang design system berskala besar, dan membimbing tim desainer.',
            'requirements' => 'Pengalaman memimpin tim desain produk, mahir Figma, Design Tokens, User Research, dan prototyping interaktif.',
            'status' => 'active',
            'deadline' => Carbon::now()->addDays(14),
            'quota' => 1,
        ]);

        $pos3 = Position::create([
            'title' => 'People & Talent Acquisition Specialist',
            'department' => 'Human Resources',
            'location' => 'Jakarta Selatan (On-site)',
            'type' => 'Full-time',
            'experience_level' => 'Mid Level (2-4 Tahun)',
            'salary_min' => 9000000,
            'salary_max' => 14000000,
            'description' => 'Mengelola siklus rekrutmen menyeluruh (end-to-end talent sourcing), employer branding, dan onboarding karyawan baru.',
            'requirements' => 'Latar belakang Psikologi/Manajemen SDM, mahir wawancara berbasis kompetensi (BEI), dan negosiasi penawaran kerja.',
            'status' => 'active',
            'deadline' => Carbon::now()->addDays(10),
            'quota' => 1,
        ]);

        $pos4 = Position::create([
            'title' => 'Data & Business Intelligence Analyst',
            'department' => 'Data & Analytics',
            'location' => 'Remote (Seluruh Indonesia)',
            'type' => 'Full-time',
            'experience_level' => 'Mid Level (2+ Tahun)',
            'salary_min' => 12000000,
            'salary_max' => 17000000,
            'description' => 'Membangun pipeline visualisasi data bisnis, dashboard metrik performa perusahaan, dan pemodelan prediktif.',
            'requirements' => 'Mahir SQL kompleks, Python/R, Tableau/Metabase/PowerBI, dan komunikasi wawasan analitik kepada stakeholder.',
            'status' => 'active',
            'deadline' => Carbon::now()->addDays(25),
            'quota' => 2,
        ]);

        $pos5 = Position::create([
            'title' => 'Frontend React / Next.js Engineer',
            'department' => 'Technology & Engineering',
            'location' => 'Bandung (Hybrid)',
            'type' => 'Full-time',
            'experience_level' => 'Entry to Mid Level (1-3 Tahun)',
            'salary_min' => 9500000,
            'salary_max' => 14500000,
            'description' => 'Mengembangkan antarmuka web yang responsif, cepat, ramah aksesibilitas, dan terintegrasi dengan GraphQL/REST.',
            'requirements' => 'Menguasai JavaScript/TypeScript, React 18+, Tailwind CSS, State Management (Zustand/Redux), dan Web Vitals.',
            'status' => 'active',
            'deadline' => Carbon::now()->addDays(18),
            'quota' => 3,
        ]);

        $pos6 = Position::create([
            'title' => 'Engineering Manager (Internal Promotion)',
            'department' => 'Technology & Engineering',
            'location' => 'Jakarta Selatan (Hybrid)',
            'type' => 'Full-time',
            'experience_level' => 'Manager Level (5+ Tahun)',
            'salary_min' => 25000000,
            'salary_max' => 35000000,
            'description' => 'Lowongan rekrutmen internal untuk memimpin tim rekayasa perangkat lunak, arsitektur sistem, dan pengembangan talenta.',
            'requirements' => 'Karyawan internal dengan rekam jejak kepemimpinan teknis minimal 2 tahun di perusahaan.',
            'status' => 'active',
            'is_internal' => true,
            'deadline' => Carbon::now()->addDays(30),
            'quota' => 1,
        ]);

        // 4. Seed Candidates & Profiles
        // Candidate 1: Ahmad Fauzi (linked to user candidate)
        $cand1 = Candidate::create([
            'user_id' => $userKandidat->id,
            'first_name' => 'Ahmad',
            'last_name' => 'Fauzi',
            'email' => 'kandidat@recruitment.test',
            'phone_number' => '081287654321',
            'is_active' => true,
        ]);

        CandidateProfile::create([
            'candidate_id' => $cand1->id,
            'gender' => 'male',
            'date_of_birth' => '1997-04-12',
            'place_of_birth' => 'Jakarta',
            'nik' => '3171011204970005',
            'address' => 'Jl. Tebet Barat Dalam Raya No. 42',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12810',
            'linkedin_url' => 'https://linkedin.com/in/ahmad-fauzi-dev',
            'portfolio_url' => 'https://ahmadfauzi.dev',
            'summary' => 'Full-stack software engineer dengan pengalaman 4 tahun membangun aplikasi web berbasis Laravel, Vue.js, dan cloud infrastructure.',
            'photo' => null,
        ]);

        CandidateEducation::create([
            'candidate_id' => $cand1->id,
            'institution_name' => 'Universitas Indonesia',
            'degree' => 'Sarjana (S1)',
            'field_of_study' => 'Ilmu Komputer',
            'gpa' => 3.78,
            'start_date' => '2016-08-01',
            'end_date' => '2020-07-20',
            'is_current' => false,
        ]);

        CandidateExperience::create([
            'candidate_id' => $cand1->id,
            'company_name' => 'PT Solusi Teknologi Nusantara',
            'position' => 'Full-Stack Developer',
            'description' => 'Merancang RESTful API Laravel berkinerja tinggi yang menangani 200k request harian dan memodernisasi frontend ke Vue 3.',
            'start_date' => '2021-02-01',
            'end_date' => null,
            'is_current' => true,
        ]);

        CandidateSkill::create([
            'candidate_id' => $cand1->id,
            'skill_name' => 'PHP & Laravel Framework',
            'proficiency_level' => 'expert',
            'years_of_experience' => 4,
        ]);

        CandidateSkill::create([
            'candidate_id' => $cand1->id,
            'skill_name' => 'Vue.js & Tailwind CSS',
            'proficiency_level' => 'advanced',
            'years_of_experience' => 3,
        ]);

        CandidateSkill::create([
            'candidate_id' => $cand1->id,
            'skill_name' => 'PostgreSQL & Database Optimization',
            'proficiency_level' => 'advanced',
            'years_of_experience' => 4,
        ]);

        // Candidate 2: Siti Rahmania (Product Design Lead)
        $cand2 = Candidate::create([
            'user_id' => null,
            'first_name' => 'Siti',
            'last_name' => 'Rahmania',
            'email' => 'siti.rahmania@example.com',
            'phone_number' => '081398765432',
            'is_active' => true,
        ]);

        CandidateProfile::create([
            'candidate_id' => $cand2->id,
            'gender' => 'female',
            'date_of_birth' => '1995-08-25',
            'place_of_birth' => 'Bandung',
            'nik' => '3273012508950002',
            'address' => 'Jl. Dago Asri No. 15',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40135',
            'linkedin_url' => 'https://linkedin.com/in/sitirahmania-design',
            'portfolio_url' => 'https://dribbble.com/sitirahmania',
            'summary' => 'Senior UI/UX Designer & Design Strategist berpengalaman memimpin tim produk fintech dan SaaS di Asia Tenggara.',
        ]);

        // Candidate 3: Kevin Tanuwijaya (Data Analyst)
        $cand3 = Candidate::create([
            'user_id' => null,
            'first_name' => 'Kevin',
            'last_name' => 'Tanuwijaya',
            'email' => 'kevin.tan@example.com',
            'phone_number' => '081901234567',
            'is_active' => true,
        ]);

        CandidateProfile::create([
            'candidate_id' => $cand3->id,
            'gender' => 'male',
            'date_of_birth' => '1998-11-03',
            'place_of_birth' => 'Surabaya',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'summary' => 'Data Analyst antusias dengan spesialisasi pembersihan data, visualisasi interaktif di Tableau, dan SQL Query tuning.',
        ]);

        // Candidate 4: Dewi Anggraeni (HR Specialist)
        $cand4 = Candidate::create([
            'user_id' => null,
            'first_name' => 'Dewi',
            'last_name' => 'Anggraeni',
            'email' => 'dewi.anggraeni@example.com',
            'phone_number' => '081234567890',
            'is_active' => true,
        ]);

        CandidateProfile::create([
            'candidate_id' => $cand4->id,
            'gender' => 'female',
            'date_of_birth' => '1996-01-18',
            'place_of_birth' => 'Yogyakarta',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'summary' => 'Talent Acquisition & Employee Engagement Officer dengan track record merekrut lebih dari 80 talenta teknologi dalam 2 tahun.',
        ]);

        // Candidate 5: Rizky Ramadhan (Frontend React)
        $cand5 = Candidate::create([
            'user_id' => null,
            'first_name' => 'Rizky',
            'last_name' => 'Ramadhan',
            'email' => 'rizky.ramadhan@example.com',
            'phone_number' => '087788990011',
            'is_active' => true,
        ]);

        CandidateProfile::create([
            'candidate_id' => $cand5->id,
            'gender' => 'male',
            'date_of_birth' => '2000-03-15',
            'place_of_birth' => 'Tangerang',
            'city' => 'Tangerang',
            'province' => 'Banten',
            'summary' => 'Frontend developer dengan keahlian React, TypeScript, Tailwind CSS, dan integrasi Next.js App Router.',
        ]);

        // 5. Seed Applications across recruitment pipeline stages
        // Cand 1 (Ahmad Fauzi) -> Stage: Interview (Pos 1: Senior Full-Stack)
        $app1 = CandidateApplication::create([
            'candidate_id' => $cand1->id,
            'position_id' => $pos1->id,
            'stage' => 'interview',
            'status' => 'in_review',
            'match_score' => 94,
            'resume_url' => 'uploads/resumes/ahmad_fauzi_cv.pdf',
            'cover_letter' => 'Saya sangat antusias melamar peran Senior Full-Stack Developer karena pengalaman saya selaras dengan stack teknologi perusahaan Anda.',
            'hr_notes' => 'Lolos technical test dengan skor 92/100. Disarankan untuk wawancara teknis mendalam dengan VP of Engineering.',
            'applied_at' => Carbon::now()->subDays(6),
        ]);

        // Cand 1 second application -> Stage: Screening (Pos 5: Frontend React)
        CandidateApplication::create([
            'candidate_id' => $cand1->id,
            'position_id' => $pos5->id,
            'stage' => 'screening',
            'status' => 'pending',
            'match_score' => 88,
            'applied_at' => Carbon::now()->subDays(2),
        ]);

        // Cand 2 (Siti Rahmania) -> Stage: Offering (Pos 2: Product Design Lead)
        $app2 = CandidateApplication::create([
            'candidate_id' => $cand2->id,
            'position_id' => $pos2->id,
            'stage' => 'offering',
            'status' => 'passed',
            'match_score' => 96,
            'hr_notes' => 'Kandidat sangat unggul dalam wawancara portfolio dan cultural fit. Penawaran offering letter telah dikirimkan.',
            'applied_at' => Carbon::now()->subDays(15),
        ]);

        // Cand 3 (Kevin Tan) -> Stage: Assessment (Pos 4: Data Analyst)
        $app3 = CandidateApplication::create([
            'candidate_id' => $cand3->id,
            'position_id' => $pos4->id,
            'stage' => 'assessment',
            'status' => 'in_review',
            'match_score' => 86,
            'hr_notes' => 'Lolos screening berkas & CV. Mengikuti Ujian Psikotes & Logika Online.',
            'applied_at' => Carbon::now()->subDays(4),
        ]);

        // Cand 4 (Dewi Anggraeni) -> Stage: Onboarding (Pos 3: HR Specialist)
        $app4 = CandidateApplication::create([
            'candidate_id' => $cand4->id,
            'position_id' => $pos3->id,
            'stage' => 'onboarding',
            'status' => 'passed',
            'match_score' => 92,
            'hr_notes' => 'Offering diterima! Menunggu tanggal masuk kerja resmi.',
            'applied_at' => Carbon::now()->subDays(25),
        ]);

        // Cand 5 (Rizky Ramadhan) -> Stage: Screening (Pos 5: Frontend React)
        CandidateApplication::create([
            'candidate_id' => $cand5->id,
            'position_id' => $pos5->id,
            'stage' => 'screening',
            'status' => 'pending',
            'match_score' => 82,
            'hr_notes' => 'Berkas baru masuk. Menunggu verifikasi portofolio GitHub.',
            'applied_at' => Carbon::now()->subHours(12),
        ]);

        // 6. Seed Interview Assessments
        // Interview 1: Upcoming interview today/tomorrow for Ahmad Fauzi
        InterviewAssessment::create([
            'candidate_id' => $cand1->id,
            'position_id' => $pos1->id,
            'interviewer_id' => $userInterviewer->id,
            'interview_type' => 'User & Technical Architecture Interview',
            'scheduled_at' => Carbon::now()->addHours(2),
            'room_link' => 'https://meet.google.com/rec-tech-2026',
            'status' => 'scheduled',
            'notes' => 'Fokus wawancara: Arsitektur microservices, concurrency handling, database indexing, dan studi kasus sistem skala tinggi.',
        ]);

        // Interview 2: Next day interview
        InterviewAssessment::create([
            'candidate_id' => $cand3->id,
            'position_id' => $pos4->id,
            'interviewer_id' => $userInterviewer->id,
            'interview_type' => 'Technical Data Case Interview',
            'scheduled_at' => Carbon::now()->addDays(1)->setHour(14)->setMinute(0),
            'room_link' => 'https://meet.google.com/data-eval-case',
            'status' => 'scheduled',
            'notes' => 'Presentasi analisa retention rate pengguna menggunakan dataset sampel.',
        ]);

        // Interview 3: Past completed interview with scores for Siti Rahmania
        InterviewAssessment::create([
            'candidate_id' => $cand2->id,
            'position_id' => $pos2->id,
            'interviewer_id' => $userInterviewer->id,
            'interview_type' => 'Portfolio & Design Leadership Defense',
            'scheduled_at' => Carbon::now()->subDays(4),
            'room_link' => 'https://meet.google.com/pdl-interview',
            'technical_score' => 95,
            'communication_score' => 94,
            'culture_score' => 98,
            'total_score' => 96,
            'notes' => 'Siti menunjukkan visi kepemimpinan produk yang sangat matang dan portofolio design system yang sangat terstruktur. Sangat direkomendasikan.',
            'recommendation' => 'pass',
            'status' => 'completed',
        ]);

        // 6b. Seed Online Assessments & Question Bank
        $test1 = Assessment::create([
            'title' => 'Technical Test Senior Backend (PHP & Laravel)',
            'test_type' => 'Technical Test',
            'position_id' => $pos1->id,
            'description' => 'Tes kompetensi teknis mengenai arsitektur Laravel, Eloquent ORM, dependency injection, dan best practices backend.',
            'duration' => 45,
            'passing_score' => 75,
            'is_active' => true,
        ]);

        $q1 = AssessmentQuestion::create([
            'assessment_id' => $test1->id,
            'question_text' => 'Fitur manakah di Laravel yang digunakan untuk mengelola relasi Many-to-Many pada database?',
            'points' => 25,
            'explanation' => 'belongsToMany mendefinisikan relasi many-to-many dengan tabel perantara (pivot table).',
        ]);
        AssessmentAnswer::create(['assessment_question_id' => $q1->id, 'answer_text' => 'hasOne', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $q1->id, 'answer_text' => 'hasMany', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $q1->id, 'answer_text' => 'belongsToMany', 'is_correct' => true]);
        AssessmentAnswer::create(['assessment_question_id' => $q1->id, 'answer_text' => 'morphMany', 'is_correct' => false]);

        $q2 = AssessmentQuestion::create([
            'assessment_id' => $test1->id,
            'question_text' => 'Apa fungsi utama dari middleware auth di dalam routing Laravel?',
            'points' => 25,
            'explanation' => 'Middleware auth memastikan hanya pengguna yang sudah terotentikasi yang dapat mengakses rute.',
        ]);
        AssessmentAnswer::create(['assessment_question_id' => $q2->id, 'answer_text' => 'Memastikan pengguna telah terautentikasi sebelum mengakses rute', 'is_correct' => true]);
        AssessmentAnswer::create(['assessment_question_id' => $q2->id, 'answer_text' => 'Melakukan pembersihan cache aplikasi secara berkala', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $q2->id, 'answer_text' => 'Mengatur koneksi database sekunder', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $q2->id, 'answer_text' => 'Menghapus token CSRF setelah form disubmit', 'is_correct' => false]);

        $q3 = AssessmentQuestion::create([
            'assessment_id' => $test1->id,
            'question_text' => 'Command Artisan manakah yang digunakan untuk membuat berkas migrasi database baru?',
            'points' => 25,
            'explanation' => 'make:migration adalah perintah artisan bawaan untuk generate skeleton migration file.',
        ]);
        AssessmentAnswer::create(['assessment_question_id' => $q3->id, 'answer_text' => 'php artisan create:migration', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $q3->id, 'answer_text' => 'php artisan make:migration', 'is_correct' => true]);
        AssessmentAnswer::create(['assessment_question_id' => $q3->id, 'answer_text' => 'php artisan db:migrate', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $q3->id, 'answer_text' => 'php artisan table:new', 'is_correct' => false]);

        $q4 = AssessmentQuestion::create([
            'assessment_id' => $test1->id,
            'question_text' => 'Bagaimana cara mencegah N+1 query problem saat mengambil data relasi di Eloquent?',
            'points' => 25,
            'explanation' => 'Eager loading menggunakan with() memuat relasi sekaligus tanpa query berulang.',
        ]);
        AssessmentAnswer::create(['assessment_question_id' => $q4->id, 'answer_text' => 'Menggunakan Eager Loading dengan method with()', 'is_correct' => true]);
        AssessmentAnswer::create(['assessment_question_id' => $q4->id, 'answer_text' => 'Menambahkan perintah sleep() pada loop', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $q4->id, 'answer_text' => 'Menghapus foreign key pada tabel relasi', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $q4->id, 'answer_text' => 'Menonaktifkan strict mode di config database', 'is_correct' => false]);

        // Logic Test
        $test2 = Assessment::create([
            'title' => 'Tes Logika Numerik & Penalaran Analitis',
            'test_type' => 'Logic Test',
            'position_id' => null,
            'description' => 'Tes penalaran kuantitatif dan deduksi logika untuk seluruh posisi kandidat pelamar.',
            'duration' => 30,
            'passing_score' => 70,
            'is_active' => true,
        ]);

        $lq1 = AssessmentQuestion::create([
            'assessment_id' => $test2->id,
            'question_text' => 'Jika sebuah modul dikerjakan oleh 6 developer dalam 10 hari, berapa hari yang dibutuhkan jika dikerjakan oleh 15 developer dengan kecepatan yang sama?',
            'points' => 50,
            'explanation' => '(6 * 10) / 15 = 4 hari.',
        ]);
        AssessmentAnswer::create(['assessment_question_id' => $lq1->id, 'answer_text' => '4 Hari', 'is_correct' => true]);
        AssessmentAnswer::create(['assessment_question_id' => $lq1->id, 'answer_text' => '5 Hari', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $lq1->id, 'answer_text' => '6 Hari', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $lq1->id, 'answer_text' => '8 Hari', 'is_correct' => false]);

        $lq2 = AssessmentQuestion::create([
            'assessment_id' => $test2->id,
            'question_text' => 'Lanjutkan pola deret angka berikut: 3, 6, 12, 24, 48, ... ?',
            'points' => 50,
            'explanation' => 'Setiap angka dikalikan 2: 48 * 2 = 96.',
        ]);
        AssessmentAnswer::create(['assessment_question_id' => $lq2->id, 'answer_text' => '72', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $lq2->id, 'answer_text' => '84', 'is_correct' => false]);
        AssessmentAnswer::create(['assessment_question_id' => $lq2->id, 'answer_text' => '96', 'is_correct' => true]);
        AssessmentAnswer::create(['assessment_question_id' => $lq2->id, 'answer_text' => '108', 'is_correct' => false]);

        // Seed Sample Test Result for Cand 1 (Ahmad Fauzi) on Logic Test
        CandidateTestResult::create([
            'candidate_id' => $cand1->id,
            'candidate_application_id' => $app1->id ?? null,
            'assessment_id' => $test2->id,
            'score' => 100,
            'result' => 'passed',
            'duration_spent' => 720,
            'total_questions' => 2,
            'correct_answers' => 2,
            'started_at' => Carbon::now()->subDays(5),
            'completed_at' => Carbon::now()->subDays(5)->addMinutes(12),
            'notes' => 'Lolos passing score (70%). Nilai sempurna 100%.',
        ]);

        // Seed Sample Test Result for Cand 3 (Kevin Tan) on Technical Test
        CandidateTestResult::create([
            'candidate_id' => $cand3->id,
            'candidate_application_id' => $app3->id ?? null,
            'assessment_id' => $test1->id,
            'score' => 85,
            'result' => 'passed',
            'duration_spent' => 950,
            'total_questions' => 2,
            'correct_answers' => 2,
            'started_at' => Carbon::now()->subDays(3),
            'completed_at' => Carbon::now()->subDays(3)->addMinutes(16),
            'notes' => 'Nilai asesmen memuaskan (85/100). Berhak lanjut ke wawancara teknis.',
        ]);

        // 7. Seed Offerings
        // Offering 1: Cand 2 (Siti Rahmania - Accepted)
        Offering::create([
            'candidate_id' => $cand2->id,
            'candidate_application_id' => $app2->id ?? null,
            'position_id' => $pos2->id,
            'department' => 'Product & Design',
            'salary' => 22500000,
            'salary_offered' => 22500000,
            'employment_type' => 'Full-time',
            'join_date' => Carbon::now()->addDays(14),
            'start_date' => Carbon::now()->addDays(14),
            'status' => 'accepted',
            'benefits' => "- BPJS Kesehatan & Ketenagakerjaan\n- Asuransi Rawat Inap & Rawat Jalan Swasta\n- Tunjangan Transportasi & Internet\n- MacBook Pro M3 Pro\n- Bonus Tahunan Berdasarkan Kinerja",
            'notes' => 'Offering letter resmi telah ditandatangani oleh kandidat secara elektronik.',
            'sent_at' => Carbon::now()->subDays(3),
            'responded_at' => Carbon::now()->subDays(2),
        ]);

        // Offering 2: Cand 1 (Ahmad Fauzi - Sent, ready to be reviewed in portal)
        Offering::create([
            'candidate_id' => $cand1->id,
            'candidate_application_id' => $app1->id ?? null,
            'position_id' => $pos1->id,
            'department' => 'Technology & Engineering',
            'salary' => 26000000,
            'salary_offered' => 26000000,
            'employment_type' => 'Full-time',
            'join_date' => Carbon::now()->addDays(21),
            'start_date' => Carbon::now()->addDays(21),
            'status' => 'sent',
            'benefits' => "- BPJS Kesehatan & Ketenagakerjaan\n- Asuransi Kesehatan Keluarga Swasta\n- Tunjangan Komunikasi & Internet WFH\n- Laptop Perusahaan MacBook Pro 16 inch\n- Bonus Kinerja Tahunan & Opsi Saham Karyawan",
            'notes' => 'Penawaran ini berlaku selama 7 hari kalender. Harap konfirmasi penerimaan melalui portal kandidat.',
            'sent_at' => Carbon::now()->subHours(6),
        ]);

        // 8. Seed Onboardings
        Onboarding::create([
            'candidate_id' => $cand4->id,
            'candidate_application_id' => $app4->id ?? null,
            'position_id' => $pos3->id,
            'start_date' => Carbon::now()->addDays(7),
            'join_date' => Carbon::now()->addDays(7),
            'status' => 'in_progress',
            'checklist_percentage' => 75,
            'ktp_number' => '3174051203980002',
            'ktp_verified' => true,
            'npwp_number' => '81.234.567.8-012.000',
            'npwp_verified' => true,
            'bpjs_number' => '0001234567891',
            'bpjs_verified' => true,
            'bank_name' => 'BCA',
            'bank_account_number' => '5420198271',
            'bank_account_holder' => 'Dewi Anggraeni',
            'bank_verified' => true,
            'pas_foto_verified' => true,
            'emergency_contact_name' => 'Budi Anggoro',
            'emergency_contact_relation' => 'Suami',
            'emergency_contact_phone' => '081398765432',
            'emergency_verified' => true,
            'notes' => 'Pengiriman laptop kantor selesai, registrasi email korporat sedang diproses tim IT.',
        ]);

        // 9. Seed Activity Logs
        ActivityLog::create([
            'user_id' => $userAdmin->id,
            'user_name' => $userAdmin->name,
            'action' => 'UPDATE_ROLE_PERMISSION',
            'module' => 'User Management',
            'description' => 'Memperbarui hak akses role Interviewer untuk modul input penilaian.',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subMinutes(15),
        ]);

        ActivityLog::create([
            'user_id' => $userHr->id,
            'user_name' => $userHr->name,
            'action' => 'SCHEDULE_INTERVIEW',
            'module' => 'Recruitment Pipeline',
            'description' => 'Menjadwalkan wawancara teknis kandidat Ahmad Fauzi untuk posisi Senior Full-Stack Developer.',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subHours(1),
        ]);

        ActivityLog::create([
            'user_id' => $userHr->id,
            'user_name' => $userHr->name,
            'action' => 'OFFERING_ACCEPTED',
            'module' => 'Offering & Compensation',
            'description' => 'Kandidat Siti Rahmania menerima penawaran kerja posisi Product Design Lead.',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subHours(3),
        ]);

        ActivityLog::create([
            'user_id' => $userInterviewer->id,
            'user_name' => $userInterviewer->name,
            'action' => 'SUBMIT_ASSESSMENT',
            'module' => 'Interview Assessment',
            'description' => 'Mengirimkan hasil penilaian wawancara portofolio untuk kandidat Siti Rahmania (Skor: 96/100).',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subDays(4),
        ]);

        ActivityLog::create([
            'user_id' => $userAdmin->id,
            'user_name' => $userAdmin->name,
            'action' => 'CREATE_POSITION',
            'module' => 'Job Vacancy',
            'description' => 'Mempublikasikan lowongan kerja baru: Senior Full-Stack Developer (Laravel + Vue/React).',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subDays(6),
        ]);
    }
}
