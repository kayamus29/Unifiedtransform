<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        // Create all permissions and a user with those permissions
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->givePermissionTo(Permission::all());
    }

    /**
     * @test
     * @dataProvider adminRoutesProvider
     */
    public function admin_can_access_key_pages($route)
    {
        $response = $this->actingAs($this->user)->get($route);

        $response->assertStatus(200);
    }

    public function adminRoutesProvider()
    {
        return [
            'Home Page' => ['/home'],
            'Attendances Page' => ['/attendances'],
            'Classes Page' => ['/classes'],
            'Add Teacher Page' => ['/teachers/add'],
            'Teacher List Page' => ['/teachers/view/list'],
            'Add Student Page' => ['/students/add'],
            'Student List Page' => ['/students/view/list'],
            'Create Marks Page' => ['/marks/create'],
            'View Marks Page' => ['/marks/results'],
            'View Exams Page' => ['/exams/view'],
            'Create Exam Page' => ['/exams/create'],
            'Create Grading System Page' => ['/exams/grade/create'],
            'View Grading System Page' => ['/exams/grade/view'],
            'Promotions Page' => ['/promotions/index'],
            'Promote Students Page' => ['/promotions/promote'],
            'Academic Settings Page' => ['/academics/settings'],
            'Calendar Page' => ['/calendar-event'],
            'Create Routine Page' => ['/routine/create'],
            'Create Syllabus Page' => ['/syllabus/create'],
            'View Syllabi Page' => ['/syllabus/index'],
            'Create Notice Page' => ['/notice/create'],
            'View Teacher Courses' => ['/courses/teacher/index'],
            'Edit Password Page' => ['/password/edit'],
        ];
    }
}
