# Course Management System

A responsive web application built with PHP and MySQL to manage university course records. This project demonstrates CRUD (Create, Read, Update, Delete) operations.

## Features

- Add new courses
- View course records
- Edit course details
- Delete courses
- MySQL database connectivity
- Responsive web interface

## Technologies Used

- PHP
- MySQL
- HTML5 and CSS3
- XAMPP

## Database Setup

Database name: `university`

Table name: `Course`

| Column | Description |
|---|---|
| `id` | Primary key, auto-increment |
| `course` | Course name |
| `credit` | Number of credits |

If the database and table do not already exist, run:

```sql
CREATE DATABASE IF NOT EXISTS university;
USE university;

CREATE TABLE IF NOT EXISTS Course (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course VARCHAR(255) NOT NULL,
    credit INT NOT NULL
);
```

**Important:** Back up your database before making changes. Do not recreate an existing table if it contains course records you want to keep.

## Installation with XAMPP

1. Install XAMPP.
2. Start Apache and MySQL.
3. Place the project in:

   `C:\xampp\htdocs\Course_MS\`

4. Open [phpMyAdmin](http://localhost/phpmyadmin).
5. Create the `university` database if it does not exist.
6. Check the database settings in `db.php`.

For a default XAMPP installation with no MySQL root password:

```php
$conn = new mysqli("localhost", "root", "", "university");
```

If you have configured a MySQL password, use your actual password instead.

7. Open the application at:

   http://localhost/Course_MS/

## Project Files

- `db.php` — Database connection
- `index.php` — Main page for adding and viewing courses
- `edit.php` — Edit course details
- `delete.php` — Delete
