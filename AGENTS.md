<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

# Student-Friendly Coding Instructions

## IMPORTANT — Coding Style

This project is being developed by a **student for a school final project and defense**.

All code must be written in a **simple, readable, and beginner/student-friendly way**.

Do NOT over-engineer the project.

The code should look like something a Laravel student could realistically understand, explain, maintain, and defend during a project presentation.

### Coding Rules

1. Use simple and straightforward Laravel code.
2. Follow the normal Laravel MVC structure (Model, View, Controller).
3. Keep controllers easy to read and focused on standard CRUD actions.
4. Do not create unnecessary design patterns or complicated architecture (avoid repositories, complex service layers, DTOs, custom pipelines).
5. Do not use advanced programming techniques unless they are actually necessary.
6. Avoid unnecessary abstractions, repositories, services, factories, or complex helper classes.
7. Keep database relationships simple and understandable (`hasMany`, `belongsTo`).
8. Use clear and descriptive variable names.
9. Add short, clear comments where they help explain what the code is doing.
10. Do not write extremely complicated one-line code.
11. Prefer simple `if` statements and standard Laravel methods.
12. Keep validation straightforward and easy to understand (standard `$request->validate([...])`).
13. Keep Blade templates organized and readable.
14. Use standard Laravel features instead of creating custom solutions when possible.
15. Do not add unnecessary packages or dependencies.
16. Do not add external APIs unless they are specifically required.
17. Keep the database structure simple and appropriate for a student project.
18. Make CRUD operations easy to understand.
19. Make authentication and role permissions easy to explain during defense.
20. Prioritize **readability and maintainability over advanced or clever code**.

## Defense-Friendly Requirement

Every major part of the system should be understandable enough that the student can explain:
* What the code does
* Why the code is needed
* Which database table it uses
* Which user role can access it
* How the feature connects to the rest of the system

If there are two ways to implement something, prefer the **simpler implementation that is easier for a student to understand and defend**.

## Do Not Overbuild

Do not add unnecessary features just to make the project appear more advanced. The goal is a **functional, clean, realistic, and reviewable Laravel student project**, not an enterprise-level application. Keep the implementation simple enough that the student can personally review the code and explain it to the instructor during the defense.
