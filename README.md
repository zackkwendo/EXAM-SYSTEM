# NYOTA ICT Training Assessment System

A lightweight PHP + SQLite online assessment system for the four active NYOTA ICT trainees:
- Alfred
- Benson
- Pudens
- Elizabeth

The fifth trainee slot is intentionally unused.

## Included
- Trainer/admin dashboard
- Four trainee accounts
- Five sequential modules
- Pass mark of 60%
- Module locking/unlocking
- Retakes after failure
- 25-minute module timer
- Automatic marking
- Grade calculation
- Results history
- Starter built-in question bank based on the supplied five-month NYOTA ICT outline

## Requirements
- PHP 8.0+ with PDO SQLite enabled
- Apache/Nginx or PHP built-in server

## Installation
1. Copy the folder to a PHP-enabled server.
2. Ensure the folder is writable so SQLite can create `nyota.sqlite`.
3. Open `setup.php` once.
4. Log in at `index.php`.
5. Delete or protect `setup.php` after installation.
6. Change default passwords before giving accounts to trainees.

### Default accounts
Trainer:
- username: admin
- password: admin123

Alfred:
- username: alfred
- password: alfred123

Benson:
- username: benson
- password: benson123

Pudens:
- username: pudens
- password: pudens123

Elizabeth:
- username: elizabeth
- password: elizabeth123

## Important
The current build is a complete working starter system, but the built-in bank contains 10 auto-marked questions per module. The architecture supports expansion with application, critical-thinking and manually marked practical questions before the live assessment.
