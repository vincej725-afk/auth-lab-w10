<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Runs before every other method.
     * true = allow, false = deny, null = "no opinion, run the normal method".
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    // ?User means guests are allowed to reach this check
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        // Active courses are public; inactive ones only for their owner
        return $course->is_active || ($user && $user->id === $course->user_id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('instructor', 'admin');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->user_id;
    }

    public function delete(User $user, Course $course): bool
    {
        // Owners may delete only courses with no enrolled students
        return $user->id === $course->user_id
            && $course->students()->doesntExist();
    }
}
