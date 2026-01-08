<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentParentInfo;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SyncParentsCommand extends Command
{
    protected $signature = 'schools:sync-parents';
    protected $description = 'Syncs parents from StudentParentInfo to User table based on guardian_email and links them.';

    private $stats = [
        'total_emails' => 0,
        'parents_created' => 0,
        'parents_reused' => 0,
        'students_linked' => 0,
        'errors' => []
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting Parent Sync...');
        $this->info('========================================');

        // 1. Normalize all guardian emails first
        $this->normalizeEmails();

        // 2. Get all StudentParentInfo records where guardian_email is set
        $infos = StudentParentInfo::whereNotNull('guardian_email')
            ->where('guardian_email', '!=', '')
            ->get();

        $grouped = $infos->groupBy('guardian_email');
        $this->stats['total_emails'] = $grouped->count();

        $this->info("Found {$this->stats['total_emails']} unique guardian emails.");
        $this->newLine();

        foreach ($grouped as $email => $records) {
            $childCount = $records->count();
            $this->line("Processing: $email ($childCount children)");

            try {
                DB::beginTransaction();

                // 2. Find or Create User (Idempotent)
                $parentUser = User::where('email', $email)->first();

                if (!$parentUser) {
                    // Create new Parent User
                    $parentName = $records->first()->father_name ?: ($records->first()->mother_name ?: 'Parent');

                    // Split name if possible
                    $parts = explode(' ', trim($parentName), 2);
                    $firstName = $parts[0];
                    $lastName = $parts[1] ?? 'Guardian';

                    $parentUser = User::create([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'password' => Hash::make('password'), // Default password
                        'role' => 'parent', // Legacy role column (lowercase to match existing)
                        // Required fields with sensible defaults
                        'gender' => 'Other',
                        'nationality' => 'N/A',
                        'phone' => $records->first()->guardian_phone ?? 'N/A',
                        'address' => $records->first()->parent_address ?? 'N/A',
                        'city' => 'N/A',
                        'zip' => 'N/A',
                    ]);

                    // Assign Spatie role
                    if (!$parentUser->hasRole('Parent')) {
                        $parentUser->assignRole('Parent');
                    }

                    $this->stats['parents_created']++;
                    $this->info("  ✓ Created new parent user");
                } else {
                    // Ensure role is set
                    if (!$parentUser->hasRole('Parent')) {
                        $parentUser->assignRole('Parent');
                    }
                    $this->stats['parents_reused']++;
                    $this->info("  ✓ Reused existing parent user");
                }

                // 3. Link all children records to this parent_user_id (Idempotent)
                foreach ($records as $record) {
                    if ($record->parent_user_id !== $parentUser->id) {
                        $record->parent_user_id = $parentUser->id;
                        $record->save();
                        $this->stats['students_linked']++;
                    }
                }

                DB::commit();
                $this->line("  ✓ Linked $childCount student(s)");

            } catch (\Exception $e) {
                DB::rollBack();
                $errorMsg = "Failed: {$e->getMessage()}";
                $this->error("  ✗ $errorMsg");
                $this->stats['errors'][] = [
                    'email' => $email,
                    'error' => $e->getMessage()
                ];
            }

            $this->newLine();
        }

        $this->displaySummary();

        return $this->stats['errors'] ? 1 : 0;
    }

    private function normalizeEmails()
    {
        $this->info('Normalizing guardian emails...');

        $infos = StudentParentInfo::whereNotNull('guardian_email')
            ->where('guardian_email', '!=', '')
            ->get();

        $normalized = 0;
        foreach ($infos as $info) {
            $original = $info->guardian_email;
            $cleaned = strtolower(trim($original));

            if ($original !== $cleaned) {
                $info->guardian_email = $cleaned;
                $info->save();
                $normalized++;
            }
        }

        $this->info("Normalized $normalized email(s)");
        $this->newLine();
    }

    private function displaySummary()
    {
        $this->info('========================================');
        $this->info('SYNC SUMMARY');
        $this->info('========================================');
        $this->line("Total unique emails: {$this->stats['total_emails']}");
        $this->line("Parents created: {$this->stats['parents_created']}");
        $this->line("Parents reused: {$this->stats['parents_reused']}");
        $this->line("Students linked: {$this->stats['students_linked']}");

        if (count($this->stats['errors']) > 0) {
            $this->error("Errors: " . count($this->stats['errors']));
            $this->newLine();
            $this->error('Failed Records:');
            foreach ($this->stats['errors'] as $error) {
                $this->error("  - {$error['email']}: {$error['error']}");
            }
        } else {
            $this->info("Errors: 0");
            $this->newLine();
            $this->info('✓ All parents synced successfully!');
        }

        $this->info('========================================');
    }
}
