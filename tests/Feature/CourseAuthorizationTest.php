<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $ana;
    protected User $ben;
    protected User $carla;
    protected Course $anaCourse1;
    protected Course $anaCourse2;
    protected Course $anaInactiveCourse;
    protected Course $benCourse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('email', 'admin@example.com')->first();
        $this->ana = User::where('email', 'ana@example.com')->first();
        $this->ben = User::where('email', 'ben@example.com')->first();
        $this->carla = User::where('email', 'carla@example.com')->first();

        $this->anaCourse1 = Course::where('user_id', $this->ana->id)->where('is_active', true)->whereHas('students')->first();
        $this->anaCourse2 = Course::where('user_id', $this->ana->id)->where('is_active', true)->whereDoesntHave('students')->first();
        $this->anaInactiveCourse = Course::where('user_id', $this->ana->id)->where('is_active', false)->first();

        $this->benCourse = Course::where('user_id', $this->ben->id)->first();
    }

    public function test_guest_access(): void
    {
        // GET /courses -> 200
        $this->get('/courses')->assertStatus(200);

        // GET active course -> 200
        $this->get("/courses/{$this->anaCourse1->id}")->assertStatus(200);

        // GET inactive course -> 403
        $this->get("/courses/{$this->anaInactiveCourse->id}")->assertStatus(403);

        // GET /courses/create -> 302 to login
        $this->get('/courses/create')->assertRedirect('/login');

        // GET /admin -> 302 to login
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_student_carla_restrictions(): void
    {
        // GET /courses/create -> 403
        $this->actingAs($this->carla)->get('/courses/create')->assertStatus(403);

        // GET /courses/{benCourse}/edit -> 403
        $this->actingAs($this->carla)->get("/courses/{$this->benCourse->id}/edit")->assertStatus(403);

        // GET /admin -> 403
        $this->actingAs($this->carla)->get('/admin')->assertStatus(403);

        // Open course 1 -> 200, no Edit or Delete buttons
        $response = $this->actingAs($this->carla)->get("/courses/{$this->anaCourse1->id}");
        $response->assertStatus(200);
        $response->assertDontSee('Edit');
        $response->assertDontSee('Delete');
    }

    public function test_instructor_ana_permissions(): void
    {
        // Create course -> 302 to show page, owner is Ana
        $response = $this->actingAs($this->ana)->post('/courses', [
            'code' => 'TST101',
            'title' => 'Test Course 101',
            'units' => 3,
            'is_active' => 1,
        ]);
        $response->assertStatus(302);
        $newCourse = Course::where('code', 'TST101')->first();
        $this->assertNotNull($newCourse);
        $this->assertEquals($this->ana->id, $newCourse->user_id);
        $response->assertRedirect("/courses/{$newCourse->id}");

        // Edit own course -> 200
        $this->actingAs($this->ana)->get("/courses/{$this->anaCourse1->id}/edit")->assertStatus(200);

        // View own inactive course -> 200
        $this->actingAs($this->ana)->get("/courses/{$this->anaInactiveCourse->id}")->assertStatus(200);

        // Edit Ben's course -> 403
        $this->actingAs($this->ana)->get("/courses/{$this->benCourse->id}/edit")->assertStatus(403);

        // Open course 1 -> shows Edit link and "Cannot delete: students are enrolled"
        $response = $this->actingAs($this->ana)->get("/courses/{$this->anaCourse1->id}");
        $response->assertStatus(200);
        $response->assertSee('Edit');
        $response->assertSee('Cannot delete: students are enrolled');

        // Delete course 2 (own, no students) -> 302 to /courses
        $this->actingAs($this->ana)->delete("/courses/{$this->anaCourse2->id}")->assertRedirect('/courses');
    }

    public function test_instructor_ben_cannot_view_anas_inactive_course(): void
    {
        $this->actingAs($this->ben)->get("/courses/{$this->anaInactiveCourse->id}")->assertStatus(403);
    }

    public function test_admin_full_access(): void
    {
        // Edit course 4 -> 200
        $this->actingAs($this->admin)->get("/courses/{$this->benCourse->id}/edit")->assertStatus(200);

        // Open /admin -> 200
        $this->actingAs($this->admin)->get('/admin')->assertStatus(200);

        // Delete course 1 (even with enrolled students, before() bypasses) -> 302
        $this->actingAs($this->admin)->delete("/courses/{$this->anaCourse1->id}")->assertRedirect('/courses');
    }

    public function test_new_registration_defaults_to_student(): void
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect('/dashboard');

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertEquals('student', $newUser->role);

        // Accessing /admin as student -> 403
        $this->actingAs($newUser)->get('/admin')->assertStatus(403);
    }
}
