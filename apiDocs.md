# Mining Academy - API Documentation

Base path: /api/v1

---

## Authentication

### 1. Register
- Endpoint: `POST /api/v1/register`  
- Auth: Public  
- Body (JSON):
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "account_type": "student",
  "organization_id": "0199cede-bfb4-73b6-ac1f-63576f60fbae"
}
```
- Description: Register a new user (student, instructor, or admin).

### 2. Login
- Endpoint: `POST /api/v1/login`  
- Auth: Public  
- Body (JSON):
```json
{
  "email": "john@example.com",
  "password": "password"
}
```
- Description: Logs in a user and returns an API token.

### 3. Logout
- Endpoint: `POST /api/v1/logout`  
- Auth: Bearer token (sanctum)  
- Body: none  
- Description: Logout current authenticated user.

### 4. Get Profile
- Endpoint: `GET /api/v1/me`  
- Auth: Bearer token (sanctum)  
- Body: none  
- Description: Fetch the currently authenticated user's profile.

---

## Courses

### 5. List Courses
- Endpoint: `GET /api/v1/courses`  
- Auth: Public  
- Query: optional filters/pagination  
- Description: Returns a paginated list of all courses.

### 6. Get Course
- Endpoint: `GET /api/v1/courses/{id}`  
- Auth: Public  
- Description: Returns details of a specific course (includes versions, runs, modules).

### 7. Create Course
- Endpoint: `POST /api/v1/courses`  
- Auth: Bearer token (Instructor)  
- Body (JSON):
```json
{
  "title": "Introduction to Mining",
  "description": "Course description",
  "price": 100.0,
  "is_free": false,
  "status": "published",
  "instructor_id": "0199cede-bfb4-73b6-ac1f-63576f60fbae"
}
```
- Description: Creates a new course.

### 8. Update Course
- Endpoint: `PUT /api/v1/courses/{id}`  
- Auth: Bearer token (Instructor)  
- Body: JSON with fields to update, e.g.:
```json
{
  "title": "Updated Course Title",
  "price": 150.0
}
```
- Description: Updates an existing course.

### 9. Delete Course
- Endpoint: `DELETE /api/v1/courses/{id}`  
- Auth: Bearer token (Instructor)  
- Description: Deletes a course.

---

## Course Versions

### 10. List Course Versions
- Endpoint: `GET /api/v1/course-versions`  
- Auth: Public  
- Query: optional `course_id`  
- Description: Returns all course versions, optionally filtered by course.

### 11. Get Course Version
- Endpoint: `GET /api/v1/course-versions/{id}`  
- Auth: Public  
- Description: Returns a specific course version with its modules.

### 12. Create Course Version
- Endpoint: `POST /api/v1/course-versions`  
- Auth: Bearer token (Instructor)  
- Body (JSON):
```json
{
  "course_id": "0199cede-bfb4-73b6-ac1f-63576f60fbae",
  "version_number": "v1.0",
  "snapshot": "Initial snapshot of the course"
}
```
- Description: Creates a new version of a course.

### 13. Update Course Version
- Endpoint: `PUT /api/v1/course-versions/{id}`  
- Auth: Bearer token (Instructor)  
- Body example:
```json
{
  "version_number": "v1.1",
  "snapshot": "Updated snapshot"
}
```
- Description: Updates a course version.

### 14. Delete Course Version
- Endpoint: `DELETE /api/v1/course-versions/{id}`  
- Auth: Bearer token (Instructor)  
- Description: Deletes a course version.

---

## Course Runs

### 15. List Course Runs
- Endpoint: `GET /api/v1/course-runs`  
- Auth: Public  
- Query: optional `course_id`  
- Description: Returns all course runs (cohorts), optionally filtered by course.

### 16. Get Course Run
- Endpoint: `GET /api/v1/course-runs/{id}`  
- Auth: Public  
- Description: Returns details of a specific course run with its course.

### 17. Create Course Run
- Endpoint: `POST /api/v1/course-runs`  
- Auth: Bearer token (Instructor)  
- Body (JSON):
```json
{
  "course_id": "0199cede-bfb4-73b6-ac1f-63576f60fbae",
  "name": "October Cohort",
  "start_date": "2025-10-25",
  "end_date": "2025-12-25"
}
```
- Description: Creates a new course run (cohort).

### 18. Update Course Run
- Endpoint: `PUT /api/v1/course-runs/{id}`  
- Auth: Bearer token (Instructor)  
- Body example:
```json
{
  "name": "November Cohort",
  "end_date": "2025-12-30"
}
```
- Description: Updates an existing course run.

### 19. Delete Course Run
- Endpoint: `DELETE /api/v1/course-runs/{id}`  
- Auth: Bearer token (Instructor)  
- Description: Deletes a course run.

---

## Modules

### 20. List Modules
- Endpoint: `GET /api/v1/modules`  
- Auth: Public  
- Query: optional `course_version_id`  
- Description: Returns modules (optionally filtered by course version).

### 21. Get Module
- Endpoint: `GET /api/v1/modules/{id}`  
- Auth: Public  
- Description: Returns a specific module with its lessons.

### 22. Create Module
- Endpoint: `POST /api/v1/modules`  
- Auth: Bearer token (Instructor)  
- Body (JSON):
```json
{
  "course_version_id": "0199cede-bfb4-73b6-ac1f-63576f60fbae",
  "title": "Module 1: Introduction",
  "order": 1
}
```
- Description: Creates a module for a course version.

### 23. Update Module
- Endpoint: `PUT /api/v1/modules/{id}`  
- Auth: Bearer token (Instructor)  
- Body example:
```json
{
  "title": "Module 1: Updated Introduction",
  "order": 2
}
```
- Description: Updates a module.

### 24. Delete Module
- Endpoint: `DELETE /api/v1/modules/{id}`  
- Auth: Bearer token (Instructor)  
- Description: Deletes a module.

---

## Lessons

### 25. List Lessons
- Endpoint: `GET /api/v1/lessons`  
- Auth: Public  
- Query: optional `module_id`  
- Description: Returns lessons or filter by module.

### 26. Get Lesson
- Endpoint: `GET /api/v1/lessons/{id}`  
- Auth: Public  
- Description: Returns a specific lesson with its media files.

### 27. Create Lesson
- Endpoint: `POST /api/v1/lessons`  
- Auth: Bearer token (Instructor)  
- Body (JSON):
```json
{
  "module_id": "0199dc4a-850e-7207-86a0-40464461cd4b",
  "title": "Lesson 1: Safety Guidelines",
  "content": "Lesson content here",
  "order": 1
}
```
- Description: Creates a lesson inside a module.

### 28. Update Lesson
- Endpoint: `PUT /api/v1/lessons/{id}`  
- Auth: Bearer token (Instructor)  
- Body example:
```json
{
  "title": "Lesson 1: Updated Safety Guidelines",
  "order": 2
}
```
- Description: Updates a lesson.

### 29. Delete Lesson
- Endpoint: `DELETE /api/v1/lessons/{id}`  
- Auth: Bearer token (Instructor)  
- Description: Deletes a lesson.

---

## Enrollments

### 30. Enroll in a Course Run
- Endpoint: `POST /api/v1/enroll`  
- Auth: Bearer token (Student)  
- Body (JSON):
```json
{
  "course_run_id": "0199cede-bfb4-73b6-ac1f-63576f60fbae"
}
```
- Description: Enrolls the authenticated user in a course run.

### 31. List My Enrolled Courses
- Endpoint: `GET /api/v1/my-courses`  
- Auth: Bearer token (Student)  
- Description: Returns all courses the authenticated user is enrolled in.

---

## Media, Videos & Transcoding

- Media files: `GET /api/v1/media-files` and `GET /api/v1/media-files/{id}` (public); create/update/delete require auth.
- Videos: `GET /api/v1/videos` and `GET /api/v1/videos/{id}` (public); create/update/delete require auth.
- Video renditions: `GET /api/v1/video-renditions` and `GET /api/v1/video-renditions/{id}` (public); create/update/delete require auth.
- Transcoding jobs: `GET /api/v1/transcoding-jobs` and `GET /api/v1/transcoding-jobs/{id}` (public); create/update/delete require auth.

---

## Payments
- Create payment: `POST /api/v1/pay` (auth required)  
- List payments: `GET /api/v1/payments` (auth required)

---

Notes
- Protected endpoints require `Authorization: Bearer <token>` (Laravel Sanctum).
- Use route names when helpful; refer to `routes/api.php` for exact routing and middleware.