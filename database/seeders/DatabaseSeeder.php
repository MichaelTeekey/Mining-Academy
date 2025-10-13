<?php

namespace Database\Seeders;

use App\Models\{
    User, Organization, Course, CourseVersion, CourseRun,
    Module, Lesson, MediaFile, Enrollment, Payment
};
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    

public function run(): void
{
    // Create Organization
    $org = Organization::factory()->create(['name' => 'Mining Academy']);

    // Create Admin
    $admin = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@miningacademy.com',
       
        'organization_id' => $org->id,
    ]);

    // Create Instructor
    $instructor = User::factory()->create([
        'name' => 'Instructor John',
        'email' => 'instructor@miningacademy.com',
        'account_type' => 'instructor',
        'organization_id' => $org->id,
    ]);

    // Create Students
    $students = User::factory(10)->create([
        'organization_id' => $org->id,
        'account_type' => 'student',
    ]);

    // Create Courses
    $courses = Course::factory(3)->create([
        'instructor_id' => $instructor->id,
    ]);

    $courses->each(function ($course) use ($students) {
        $version = CourseVersion::factory()->create(['course_id' => $course->id]);
        $run = CourseRun::factory()->create(['course_id' => $course->id]);

        // Modules & Lessons
        Module::factory(3)->create(['course_version_id' => $version->id])
            ->each(function ($module) {
                Lesson::factory(4)->create(['module_id' => $module->id])
                    ->each(fn($lesson) => MediaFile::factory(2)->create(['lesson_id' => $lesson->id]));
            });

        // Enroll some students
        $students->random(5)->each(function ($student) use ($run) {
            $enroll = Enrollment::factory()->create([
                'user_id' => $student->id,
                'course_run_id' => $run->id,
            ]);
            Payment::factory()->create([
                'user_id' => $student->id,
                'course_run_id' => $run->id,
                'amount' => 49.99,
                'status' => 'completed',
            ]);
        });
    });
}


}
