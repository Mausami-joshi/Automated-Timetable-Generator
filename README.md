# Automated Timetable Generator

An automated timetable generator built with Laravel that simplifies scheduling for schools and universities by automatically allocating subjects, courses, and teachers into appropriate timeslots without conflicts.

## Features
- **Admin Dashboard**: Manage entities like classrooms, courses, subjects, and teachers.
- **Automated Scheduling**: Automatically allocate subjects and teachers to timeslots based on availability.
- **Faculty Availability**: Teachers can specify preferred timeslots to avoid conflicts.
- **Classroom Management**: Allocate specific labs and resources exactly where needed.

## Technologies Used
- **Backend:** Laravel (PHP)
- **Database:** MySQL
- **Frontend:** Blade Templates, HTML5/CSS3 (managed with Vite)

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Mausami-joshi/Automated-Timetable-Generator.git
   cd Automated-Timetable-Generator
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup:**
   Copy `.env.example` to `.env` and configure your database setup (credentials and DB name).
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run migrations and seed the database:**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the development server:**
   ```bash
   php artisan serve
   ```
   In a separate terminal, to compile frontend assets:
   ```bash
   npm run dev
   ```

## Milestone Criteria
- **MS1**: Repository initialization and adding a meaningful, structured README.
