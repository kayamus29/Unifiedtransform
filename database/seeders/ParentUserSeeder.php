<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentParentInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ParentUserSeeder extends Seeder
{
    public function run()
    {
        echo "Creating parent users from guardian emails...\n";

        // Get sample student to copy structure
        $sampleStudent = DB::table('users')->where('role', 'student')->first();

        if (!$sampleStudent) {
            echo "No students found to use as template!\n";
            return;
        }

        // Get all unique guardian emails
        $infos = StudentParentInfo::whereNotNull('guardian_email')
            ->where('guardian_email', '!=', '')
            ->get();

        $grouped = $infos->groupBy('guardian_email');

        echo "Found " . $grouped->count() . " unique guardian emails\n\n";

        $created = 0;
        $linked = 0;

        foreach ($grouped as $email => $records) {
            echo "Processing: $email (" . $records->count() . " children)\n";

            // Check if user already exists
            $existing = DB::table('users')->where('email', $email)->first();

            if ($existing) {
                echo "  User already exists (ID: {$existing->id})\n";
                $parentUserId = $existing->id;
            } else {
                // Get name from first record
                $parentName = $records->first()->father_name ?: ($records->first()->mother_name ?: 'Parent');
                $parts = explode(' ', trim($parentName), 2);

                // Copy all fields from sample student, then override
                $userData = (array) $sampleStudent;
                unset($userData['id']);
                unset($userData['created_at']);
                unset($userData['updated_at']);
                unset($userData['email_verified_at']);

                // Override with parent-specific data
                $userData['first_name'] = $parts[0];
                $userData['last_name'] = $parts[1] ?? 'Guardian';
                $userData['email'] = $email;
                $userData['password'] = Hash::make('password');
                $userData['role'] = 'parent';
                $userData['phone'] = $records->first()->guardian_phone ?? $userData['phone'] ?? 'N/A';
                $userData['address'] = $records->first()->parent_address ?? $userData['address'] ?? 'N/A';
                $userData['created_at'] = now();
                $userData['updated_at'] = now();

                // Insert
                $parentUserId = DB::table('users')->insertGetId($userData);

                // Assign role via Spatie
                DB::table('model_has_roles')->insert([
                    'role_id' => DB::table('roles')->where('name', 'Parent')->value('id'),
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $parentUserId
                ]);

                echo "  Created user (ID: $parentUserId)\n";
                $created++;
            }

            // Link children
            foreach ($records as $record) {
                if ($record->parent_user_id !== $parentUserId) {
                    DB::table('student_parent_infos')
                        ->where('id', $record->id)
                        ->update(['parent_user_id' => $parentUserId]);
                    $linked++;
                }
            }

            echo "  Linked " . $records->count() . " children\n\n";
        }

        echo "\n========================================\n";
        echo "SUCCESS!\n";
        echo "========================================\n";
        echo "Parents created: $created\n";
        echo "Students linked: $linked\n";
        echo "========================================\n";
    }
}
