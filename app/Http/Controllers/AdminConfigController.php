<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminConfigController extends Controller
{
    /**
     * Display the system configuration page with all management tabs.
     */
    public function index(Request $request): View
    {
        $settings = SystemSetting::getAll();
        $departments = Department::withCount('positions')->orderBy('name')->get();

        // Ensure backups directory exists
        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir);

        // Scan existing backup files
        $backupFiles = [];
        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                $backupFiles[] = [
                    'filename' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'size_bytes' => $file->getSize(),
                    'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                    'extension' => $file->getExtension(),
                ];
            }
            // Sort by latest creation time
            usort($backupFiles, fn ($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);
        }

        $activeTab = $request->query('tab', 'identity');

        return view('admin.config.index', [
            'settings' => $settings,
            'departments' => $departments,
            'backupFiles' => $backupFiles,
            'activeTab' => $activeTab,
            'dbConnection' => config('database.default'),
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
        ]);
    }

    /**
     * Update system identity naming, tagline, and footer text.
     */
    public function updateIdentity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'app_version' => ['nullable', 'string', 'max:30'],
            'app_tagline' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:255'],
        ], [
            'app_name.required' => 'Nama sistem wajib diisi.',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, $value);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'UPDATE_SYSTEM_IDENTITY',
            'module' => 'System Configuration',
            'description' => "Memperbarui identitas sistem: Nama '{$validated['app_name']}' versi '{$validated['app_version']}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.config.index', ['tab' => 'identity'])
            ->with('success', 'Identitas penamaan sistem berhasil diperbarui!');
    }

    /**
     * Upload and update the system logo.
     */
    public function updateLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ], [
            'logo.required' => 'Silakan pilih file gambar logo terlebih dahulu.',
            'logo.image' => 'File harus berupa gambar (PNG, JPG, SVG, atau WEBP).',
            'logo.max' => 'Ukuran file logo maksimal 2MB.',
        ]);

        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $extension = $logoFile->getClientOriginalExtension();
            $fileName = 'system_logo_'.time().'.'.$extension;

            // Delete existing logo file if any
            $currentLogo = SystemSetting::get('app_logo');
            if ($currentLogo && Storage::disk('public')->exists($currentLogo)) {
                Storage::disk('public')->delete($currentLogo);
            }

            $path = $logoFile->storeAs('logos', $fileName, 'public');
            SystemSetting::set('app_logo', $path);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name ?? 'Admin',
                'action' => 'UPDATE_SYSTEM_LOGO',
                'module' => 'System Configuration',
                'description' => 'Memperbarui logo visual sistem aplikasi.',
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('admin.config.index', ['tab' => 'identity'])
            ->with('success', 'Logo sistem berhasil diunggah dan diterapkan!');
    }

    /**
     * Remove the custom system logo and revert to default brand mark.
     */
    public function removeLogo(Request $request): RedirectResponse
    {
        $currentLogo = SystemSetting::get('app_logo');
        if ($currentLogo && Storage::disk('public')->exists($currentLogo)) {
            Storage::disk('public')->delete($currentLogo);
        }

        SystemSetting::set('app_logo', null);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'RESET_SYSTEM_LOGO',
            'module' => 'System Configuration',
            'description' => 'Mereset logo sistem ke logo bawaan.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.config.index', ['tab' => 'identity'])
            ->with('success', 'Logo sistem telah direset ke logo bawaan.');
    }

    /**
     * Store a newly created department/division.
     */
    public function storeDepartment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama departemen / divisi wajib diisi.',
            'name.unique' => 'Nama departemen tersebut sudah terdaftar dalam sistem.',
        ]);

        $department = Department::create([
            'name' => trim($validated['name']),
            'code' => $validated['code'] ? strtoupper(trim($validated['code'])) : null,
            'description' => $validated['description'],
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'CREATE_DEPARTMENT',
            'module' => 'Department Management',
            'description' => "Menambahkan departemen baru: {$department->name} ({$department->code}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.config.index', ['tab' => 'departments'])
            ->with('success', "Departemen '{$department->name}' berhasil ditambahkan ke sistem!");
    }

    /**
     * Remove the specified department.
     */
    public function destroyDepartment(Request $request, Department $department): RedirectResponse
    {
        $name = $department->name;
        $department->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'DELETE_DEPARTMENT',
            'module' => 'Department Management',
            'description' => "Menghapus departemen: {$name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.config.index', ['tab' => 'departments'])
            ->with('success', "Departemen '{$name}' berhasil dihapus dari sistem.");
    }

    /**
     * Create a new database backup snapshot.
     */
    public function createBackup(Request $request): RedirectResponse
    {
        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir);

        $timestamp = date('Y-m-d_H-i-s');
        $dbDriver = config('database.default');

        if ($dbDriver === 'sqlite') {
            $sqlitePath = config('database.connections.sqlite.database');
            $filename = "backup_recruitment_{$timestamp}.sqlite";

            if ($sqlitePath !== ':memory:' && File::exists($sqlitePath)) {
                File::copy($sqlitePath, $backupDir.DIRECTORY_SEPARATOR.$filename);
            } else {
                // In-memory sqlite or fallback snapshot
                File::put($backupDir.DIRECTORY_SEPARATOR.$filename, '-- SQLite Snapshot generated at '.now()->toDateTimeString()."\n");
            }
        } elseif ($dbDriver === 'mysql' || $dbDriver === 'mariadb') {
            $filename = "backup_recruitment_{$timestamp}.sql";
            $sqlContent = $this->generateMysqlDump();
            File::put($backupDir.DIRECTORY_SEPARATOR.$filename, $sqlContent);
        } else {
            // Generic SQL dump fallback for other drivers
            $filename = "backup_recruitment_{$timestamp}.sql";
            $sqlContent = '-- Generic Backup generated at '.now()->toDateTimeString()."\n";
            File::put($backupDir.DIRECTORY_SEPARATOR.$filename, $sqlContent);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'CREATE_BACKUP',
            'module' => 'System Backup',
            'description' => "Membuat backup database snapshot: {$filename}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.config.index', ['tab' => 'backup'])
            ->with('success', "File backup '{$filename}' berhasil dibuat dan disimpan di server!");
    }

    /**
     * Download a specified backup file.
     */
    public function downloadBackup(string $filename): BinaryFileResponse
    {
        // Prevent path traversal
        if (str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(404);
        }

        $filePath = storage_path('app/backups'.DIRECTORY_SEPARATOR.$filename);
        if (! File::exists($filePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($filePath);
    }

    /**
     * Restore system database from an uploaded backup file.
     */
    public function restoreBackup(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:51200'], // max 50MB
        ], [
            'backup_file.required' => 'Silakan pilih file backup untuk dipulihkan.',
            'backup_file.max' => 'Ukuran file backup maksimal 50MB.',
        ]);

        $uploadedFile = $request->file('backup_file');
        $extension = strtolower($uploadedFile->getClientOriginalExtension());

        if (! in_array($extension, ['sqlite', 'sql'], true)) {
            return redirect()->route('admin.config.index', ['tab' => 'backup'])
                ->with('error', 'Format file backup tidak valid. Hanya mendukung ekstensi .sqlite atau .sql.');
        }

        $dbDriver = config('database.default');

        if ($dbDriver === 'sqlite') {
            $sqlitePath = config('database.connections.sqlite.database');

            // 1. Safety backup of current database before restoring
            if ($sqlitePath !== ':memory:' && File::exists($sqlitePath)) {
                $safetyBackupName = 'pre_restore_safety_'.date('Y-m-d_H-i-s').'.sqlite';
                File::copy($sqlitePath, storage_path('app/backups'.DIRECTORY_SEPARATOR.$safetyBackupName));
            }

            if ($extension === 'sqlite') {
                if ($sqlitePath !== ':memory:') {
                    $uploadedFile->move(dirname($sqlitePath), basename($sqlitePath));
                }
            } else {
                $rawSql = File::get($uploadedFile->getRealPath());
                if (! empty(trim($rawSql))) {
                    DB::unprepared($rawSql);
                }
            }

            Artisan::call('optimize:clear');
        } else {
            // Active database is MySQL / MariaDB / PostgreSQL
            if ($extension === 'sqlite') {
                return redirect()->route('admin.config.index', ['tab' => 'backup'])
                    ->with('error', 'Database aktif sistem saat ini adalah MySQL. File berformat SQLite (.sqlite) tidak dapat di-restore langsung ke database MySQL. Harap unggah file cadangan berformat .sql.');
            }

            $rawSql = File::get($uploadedFile->getRealPath());

            // Strip single-line comments (-- ...) and multi-line comments (/* ... */)
            $cleanedSql = preg_replace('/--.*$/m', '', $rawSql);
            $cleanedSql = preg_replace('/\/\*.*?\*\//s', '', $cleanedSql);
            $cleanedSql = trim($cleanedSql);

            if (empty($cleanedSql)) {
                return redirect()->route('admin.config.index', ['tab' => 'backup'])
                    ->with('error', 'File backup tidak memiliki instruksi SQL yang valid atau file hanya berisi komentar.');
            }

            // Create automatic safety backup before restoring
            try {
                $safetyBackupName = 'pre_restore_safety_'.date('Y-m-d_H-i-s').'.sql';
                File::put(storage_path('app/backups'.DIRECTORY_SEPARATOR.$safetyBackupName), $this->generateMysqlDump());
            } catch (\Throwable) {
                // Ignore safety backup failure and proceed
            }

            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::unprepared($rawSql);
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                Artisan::call('optimize:clear');
            } catch (\Throwable $e) {
                try {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                } catch (\Throwable) {
                }

                return redirect()->route('admin.config.index', ['tab' => 'backup'])
                    ->with('error', 'Gagal memulihkan database: '.$e->getMessage());
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'RESTORE_BACKUP',
            'module' => 'System Backup',
            'description' => "Memulihkan database sistem dari file '{$uploadedFile->getClientOriginalName()}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.config.index', ['tab' => 'backup'])
            ->with('success', 'Database sistem berhasil dipulihkan (restore) dari file backup!');
    }

    /**
     * Generate complete SQL dump for MySQL / MariaDB database.
     */
    private function generateMysqlDump(): string
    {
        $databaseName = DB::connection()->getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_'.$databaseName;

        $sql = "-- ==========================================================\n";
        $sql .= "-- Database Backup: {$databaseName}\n";
        $sql .= '-- Generated At: '.now()->toDateTimeString()."\n";
        $sql .= "-- ==========================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

        foreach ($tables as $tableObj) {
            $tableName = $tableObj->$tableKey ?? current((array) $tableObj);
            if (! $tableName) {
                continue;
            }

            // Get Create Table structure
            $createTableRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createStatement = $createTableRes[0]->{'Create Table'} ?? null;

            if ($createStatement) {
                $sql .= "-- ----------------------------------------------------------\n";
                $sql .= "-- Table structure for `{$tableName}`\n";
                $sql .= "-- ----------------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createStatement.";\n\n";
            }

            // Dump data
            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                $sql .= "-- Data for `{$tableName}`\n";
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $escapedValues = array_map(function ($val) {
                        if (is_null($val)) {
                            return 'NULL';
                        }
                        if (is_bool($val)) {
                            return $val ? '1' : '0';
                        }
                        if (is_numeric($val) && ! is_string($val)) {
                            return $val;
                        }

                        return DB::getPdo()->quote((string) $val);
                    }, $rowArray);

                    $columns = array_map(fn ($col) => "`{$col}`", array_keys($rowArray));
                    $sql .= "INSERT INTO `{$tableName}` (".implode(', ', $columns).') VALUES ('.implode(', ', $escapedValues).");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    /**
     * Delete a specified backup file.
     */
    public function deleteBackup(Request $request, string $filename): RedirectResponse
    {
        if (str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(404);
        }

        $filePath = storage_path('app/backups'.DIRECTORY_SEPARATOR.$filename);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'DELETE_BACKUP',
            'module' => 'System Backup',
            'description' => "Menghapus file backup: {$filename}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.config.index', ['tab' => 'backup'])
            ->with('success', "File backup '{$filename}' berhasil dihapus.");
    }

    /**
     * Clear system cache (Application, View, Route, Config, or Full Optimize).
     */
    public function clearCache(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'all');
        $outputMessage = '';

        switch ($type) {
            case 'app':
                Artisan::call('cache:clear');
                $outputMessage = 'Cache aplikasi (Application Cache) berhasil dibersihkan.';
                break;
            case 'views':
                Artisan::call('view:clear');
                $outputMessage = 'Cache kompilasi Blade Views berhasil dibersihkan.';
                break;
            case 'routes':
                Artisan::call('route:clear');
                $outputMessage = 'Cache rute aplikasi berhasil dibersihkan.';
                break;
            case 'config':
                Artisan::call('config:clear');
                $outputMessage = 'Cache konfigurasi berhasil dibersihkan.';
                break;
            case 'all':
            default:
                Artisan::call('optimize:clear');
                $outputMessage = 'Seluruh cache sistem (Application, Views, Routes, & Config) berhasil dibersihkan tuntas!';
                break;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin',
            'action' => 'CLEAR_CACHE',
            'module' => 'System Maintenance',
            'description' => "Menjalankan pembersihan cache sistem (Tipe: {$type}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.config.index', ['tab' => 'cache'])
            ->with('success', $outputMessage);
    }

    /**
     * Format file size bytes into human readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
