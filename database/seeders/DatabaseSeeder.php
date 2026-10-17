<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo accounts - every password is: password
        User::factory()->create([
            'name' => 'Admin User', 'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $ana = User::factory()->create([
            'name' => 'Ana Instructor', 'email' => 'ana@example.com',
            'role' => 'instructor',
        ]);

        $ben = User::factory()->create([
            'name' => 'Ben Instructor', 'email' => 'ben@example.com',
            'role' => 'instructor',
        ]);

        $carla = User::factory()->create([
            'name' => 'Carla Student', 'email' => 'carla@example.com',
            'role' => 'student',
        ]);

        // Ana owns 3 courses (one inactive); Ben owns 3
        $anaCourses = Course::factory(2)->for($ana, 'owner')->create();
        Course::factory()->inactive()->for($ana, 'owner')->create();
        Course::factory(3)->for($ben, 'owner')->create();

        // Carla is enrolled in Ana's first course, so that course cannot be deleted
        $anaCourses->first()->students()->attach($carla);
    }
}
