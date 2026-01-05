<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SchoolSession;
use App\Models\Semester;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\FeeHead;
use App\Models\ClassFee;
use App\Models\StudentPayment;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create Academic Session
        $session = SchoolSession::firstOrCreate([
            'session_name' => '2025-2026'
        ]);

        // 2. Create Semesters (Terms)
        $term1 = Semester::firstOrCreate(['semester_name' => 'First Term', 'session_id' => $session->id], [
            'start_date' => Carbon::now()->startOfYear(),
            'end_date' => Carbon::now()->startOfYear()->addMonths(3)
        ]);
        $term2 = Semester::firstOrCreate(['semester_name' => 'Second Term', 'session_id' => $session->id], [
            'start_date' => Carbon::now()->startOfYear()->addMonths(4),
            'end_date' => Carbon::now()->startOfYear()->addMonths(7)
        ]);
        $term3 = Semester::firstOrCreate(['semester_name' => 'Third Term', 'session_id' => $session->id], [
            'start_date' => Carbon::now()->startOfYear()->addMonths(8),
            'end_date' => Carbon::now()->endOfYear()
        ]);

        // 3. Create Classes
        $classes = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $createdClasses = [];

        foreach ($classes as $className) {
            $class = SchoolClass::firstOrCreate(
                ['class_name' => $className, 'session_id' => $session->id]
            );
            $createdClasses[] = $class;

            // Create Sections
            Section::firstOrCreate(
                ['section_name' => 'A', 'class_id' => $class->id],
                ['room_no' => $class->id . '01', 'session_id' => $session->id]
            );
            Section::firstOrCreate(
                ['section_name' => 'B', 'class_id' => $class->id],
                ['room_no' => $class->id . '02', 'session_id' => $session->id]
            );
        }

        // 4. Create Fee Heads
        $feeHeads = [
            'Tuition Fee' => 50000,
            'Development Levy' => 5000,
            'ICT Fee' => 2000,
            'Medical Fee' => 1000,
            'PTA Levy' => 1500,
        ];

        $createdFeeHeads = [];
        foreach ($feeHeads as $name => $defaultAmount) {
            $createdFeeHeads[$name] = FeeHead::firstOrCreate(['name' => $name]);
        }

        // 5. Assign Fees to Classes (ClassFee)
        foreach ($createdClasses as $class) {
            foreach ($feeHeads as $name => $amount) {
                // Vary amount slightly per class for realism
                $classAmount = $amount + ($class->id * 100);

                ClassFee::firstOrCreate([
                    'class_id' => $class->id,
                    'fee_head_id' => $createdFeeHeads[$name]->id,
                ], [
                    'amount' => $classAmount,
                    'description' => 'Termly ' . $name
                ]);
            }
        }

        // 6. Create Students (Users) and Payments
        // Using a transaction for speed
        DB::transaction(function () use ($createdClasses, $session, $term1, $createdFeeHeads) {
            $faker = \Faker\Factory::create();

            foreach ($createdClasses as $class) {
                for ($i = 0; $i < 10; $i++) { // 10 students per class
                    $student = User::create([
                        'first_name' => $faker->firstName,
                        'last_name' => $faker->lastName,
                        'email' => $faker->unique()->safeEmail,
                        'password' => Hash::make('password'),
                        'role' => 'student',
                        // Add other required fields based on User model if any (e.g., gender, etc.)
                        'gender' => $faker->randomElement(['male', 'female']),
                        'nationality' => 'Nigerian',
                        'phone' => $faker->phoneNumber,
                        'address' => $faker->address,
                        'address2' => $faker->streetAddress,
                        'city' => 'Ikeja',
                        'zip' => $faker->postcode,
                        'birthday' => $faker->date(),
                        'blood_type' => $faker->randomElement(['A+', 'O+', 'B+']),
                        'religion' => $faker->randomElement(['Christianity', 'Islam']),
                    ]);

                    // Assign student to class (if there's a pivot table or direct column)
                    // Assuming User model has class_id based on standard school apps, or there's a promotion table.
                    // Checking existing schema for students would be good, but for now assuming 'student' role logic handles it or we need a StudentRecord. 
                    // NOTE: UnifiedTransform often uses a separate table for enrollment or updates User. 
                    // Let's assume User has no class_id and rely on 'Promotions' or similar if needed.
                    // BUT for Payment, we link Student + Class directly.

                    // 7. Generate Payments
                    // Random payment status: Paid, Partial, unpaid
                    $status = $faker->randomElement(['full', 'partial', 'none']);

                    if ($status !== 'none') {
                        $totalFees = ClassFee::where('class_id', $class->id)->sum('amount');
                        $amountPaid = ($status == 'full') ? $totalFees : $faker->numberBetween(5000, $totalFees - 1000);

                        StudentPayment::create([
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'school_session_id' => $session->id,
                            'semester_id' => $term1->id,
                            'amount_paid' => $amountPaid,
                            'transaction_date' => $faker->dateTimeBetween('-1 month', 'now'),
                            'reference_no' => 'PAY-' . strtoupper($faker->bothify('??####')),
                        ]);
                    }
                }
            }
        });

        // 8. Create Expenses
        $expenseCategories = ['Stationery', 'Fuel', 'Cleaning Supplies', 'Repairs', 'Internet Subscription'];
        $faker = \Faker\Factory::create();
        for ($j = 0; $j < 15; $j++) {
            Expense::create([
                'title' => $faker->randomElement($expenseCategories),
                'amount' => $faker->numberBetween(2000, 50000),
                'expense_date' => $faker->dateTimeBetween('-1 month', 'now'),
                'description' => $faker->sentence,
            ]);
        }
    }
}
