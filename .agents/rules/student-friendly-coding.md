# Student-Friendly Coding Instructions

## IMPORTANT — Coding Style

This project is being developed by a **student for a school final project and defense**.

All code must be written in a **simple, readable, and beginner/student-friendly way**.

Do NOT over-engineer the project.

The code should look like something a Laravel student could realistically understand, explain, maintain, and defend during a project presentation.

### Coding Rules

1. **Use simple and straightforward Laravel code.**
2. **Follow the normal Laravel MVC structure** (Model, View, Controller).
3. **Keep controllers easy to read.** Keep methods focused on standard CRUD tasks.
4. **Do not create unnecessary design patterns or complicated architecture** (no unnecessary repositories, services, DTOs, custom pipelines, etc.).
5. **Do not use advanced programming techniques unless they are actually necessary.**
6. **Avoid unnecessary abstractions, repositories, services, factories, or complex helper classes.**
7. **Keep database relationships simple and understandable** (`hasMany`, `belongsTo`).
8. **Use clear and descriptive variable names** (e.g., `$students`, `$user`, `$course`).
9. **Add short comments where they help explain what the code is doing.**
10. **Do not write extremely complicated one-line code.**
11. **Prefer simple `if` statements and normal Laravel methods when possible.**
12. **Keep validation straightforward and easy to understand** (standard `$request->validate([...])`).
13. **Keep Blade templates organized and readable.**
14. **Use standard Laravel features instead of creating custom solutions when possible.**
15. **Do not add unnecessary packages or dependencies.**
16. **Do not add external APIs unless they are specifically required.**
17. **Keep the database structure simple and appropriate for a student project.**
18. **Make CRUD operations easy to understand.**
19. **Make authentication and role permissions easy to explain during defense.**
20. **Prioritize readability and maintainability over advanced or clever code.**

## Defense-Friendly Requirement

Every major part of the system should be understandable enough that the student can explain:

* What the code does
* Why the code is needed
* Which database table it uses
* Which user role can access it
* How the feature connects to the rest of the system

If there are two ways to implement something, prefer the **simpler implementation that is easier for a student to understand and defend**.

## Do Not Overbuild

Do not add unnecessary features just to make the project appear more advanced.

The goal is a **functional, clean, realistic, and reviewable Laravel student project**, not an enterprise-level application.

Keep the implementation simple enough that the student can personally review the code and explain it to the instructor during the defense.
