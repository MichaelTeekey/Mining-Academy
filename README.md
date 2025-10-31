## 🧭 **System Overview**

Your system is a **modular online learning platform** (similar to Coursera, Udemy, or Moodle) — but with deeper structure for course versioning, media management, wallet-based payments, and multi-organization support.

It’s built to manage:

* Courses and their versions (content evolution)
* Learning runs/sessions
* User enrollments and payments
* Lesson and media content
* Wallet-based financial operations
* Organizations that manage instructors and students
* Video processing (transcoding and renditions)

---

## 🧩 **Core Functional Domains**

### 🧠 1. **Course Management**

* **Course** is the root entity.

  * Each course belongs to one **instructor** (a `User`).
  * A course can have multiple **versions** (for iterative improvements).
  * A course can also have multiple **runs** (different start/end dates — like class sessions).

**Purpose:**
Allows instructors to create and improve their courses over time, while still keeping older versions active for ongoing runs.

---

### 📚 2. **Course Content Hierarchy**

Each course version has **modules**, and each module has **lessons**.
Lessons can include **media files** such as PDFs, videos, or slides.

Structure:

```
Course → CourseVersion → Module → Lesson → MediaFile
```

**Purpose:**
Keeps course content organized and version-controlled.
If an instructor updates lessons or adds new media, it affects only the current version.

---

### 🏫 3. **Course Runs & Enrollments**

* A **CourseRun** represents a scheduled delivery of a course version (e.g. "January 2025 Cohort").
* Users **enroll** in specific runs through the `Enrollment` model.
* Each enrollment has a `status` (e.g. pending, active, completed).

**Purpose:**
Supports time-bound learning — so multiple cohorts can study the same course independently.

---

### 💳 4. **Payments & Wallets**

* Students can **pay** for enrollments using `Payment`.
* Each payment is linked to both a **User** and a **CourseRun**.
* Users also have a **Wallet**, storing a currency balance.
* `WalletTransaction` logs all wallet activities (deposits, deductions, refunds).

**Purpose:**
Allows flexible payment systems — direct payments or wallet-based transactions.

---

### 🧑‍🏫 5. **User & Organization Structure**

* Each **User** may belong to an **Organization**.
* Organizations can have many users (students, instructors, admins).
* Users have an `account_type` (likely “student”, “instructor”, or “admin”).

**Purpose:**
Enables multi-tenant or B2B scenarios — for example, a company can have multiple users under its umbrella taking courses.

---

### 🎥 6. **Video Processing & Media Management**

* Each **Lesson** can have multiple **MediaFiles**, which may include videos.
* Each **Video** is linked to a **MediaFile** and has multiple **VideoRenditions** (different resolutions).
* A **TranscodingJob** tracks background video processing tasks and errors.

**Purpose:**
Supports scalable video management — automatically generating multiple playback versions for performance and compatibility.

---

## 🔄 **Relationships Summary**

| From          | Relationship | To                      |
| ------------- | ------------ | ----------------------- |
| User          | belongsTo    | Organization            |
| Organization  | hasMany      | Users                   |
| User          | hasMany      | Courses (as Instructor) |
| Course        | hasMany      | CourseVersions          |
| CourseVersion | hasMany      | Modules                 |
| Module        | hasMany      | Lessons                 |
| Lesson        | hasMany      | MediaFiles              |
| Lesson        | belongsTo    | Module                  |
| MediaFile     | belongsTo    | Lesson                  |
| MediaFile     | hasOne       | Video                   |
| Video         | hasMany      | VideoRenditions         |
| Video         | hasMany      | TranscodingJobs         |
| Course        | hasMany      | CourseRuns              |
| CourseRun     | hasMany      | Enrollments             |
| Enrollment    | belongsTo    | User, CourseRun         |
| User          | hasOne       | Wallet                  |
| Wallet        | hasMany      | WalletTransactions      |
| User          | hasMany      | Payments                |
| CourseRun     | hasMany      | Payments                |

---

## 💡 **In summary — what your system does**

> 🔹 It manages courses, versions, and learning sessions.
> 🔹 It supports structured lesson content with videos and media.
> 🔹 It allows students to enroll, pay, and track progress.
> 🔹 It handles payments through both direct transactions and wallet systems.
> 🔹 It organizes users into companies or institutions.
> 🔹 It includes a backend system for video processing and storage.

Essentially, this is a **full-featured online education platform backend** — modular, scalable, and ready to integrate with a front-end (Vue, React, or Livewire).

---
