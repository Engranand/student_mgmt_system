# 🎓 Student Management System

A web-based Student Management System built with PHP, MySQL, and Bootstrap 5. This project allows admins, teachers, and students to manage academic activities efficiently.

## 🌐 Live Demo ("Note: Free hosting is used, HTTPS not available")
[Click here to view](http://studentmgmt.infinityfreeapp.com/login.php)

## 👥 User Roles

| Role | Login | Access |
|------|-------|--------|
| Admin | Email | Manage students, teachers, fees |
| Teacher | Email | Add marks, attendance |
| Student | Roll Number | View marks, attendance, fees |

## 🔐 Demo Credentials

**Admin:**
- Email: `admin@college.com`
- Password: `password`

## ✨ Features

### Admin Panel
- ✅ Add / Edit / Delete Students
- ✅ Add / Delete Teachers
- ✅ Manage Fee Records
- ✅ Mark Fees as Paid

### Teacher Panel
- ✅ Mark Daily Attendance
- ✅ Add Subject Marks
- ✅ View All Students

### Student Panel
- ✅ View My Marks & CGPA
- ✅ View Attendance Percentage
- ✅ View Fee Status

## 🛠️ Tech Stack

| Technology | Usage |
|------------|-------|
| PHP 8.x | Backend Logic |
| MySQL | Database |
| Bootstrap 5 | Frontend UI |
| Font Awesome 6 | Icons |
| HTML/CSS/JS | Structure & Styling |

## 📁 Project Structure
```
student_mgmt_system/
├── config/
│   └── db.php              # Database connection
├── includes/
│   └── auth.php            # Authentication & session
├── admin/
│   ├── dashboard.php
│   ├── manage_students.php
│   ├── manage_teachers.php
│   ├── manage_fees.php
│   └── edit_student.php
├── teacher/
│   ├── dashboard.php
│   ├── add_marks.php
│   ├── mark_attendance.php
│   └── view_students.php
├── student/
│   ├── dashboard.php
│   ├── my_marks.php
│   ├── my_attendance.php
│   └── my_fees.php
├── assets/
│   ├── css/
│   │   ├── login.css
│   │   ├── admin.css
│   │   ├── teacher.css
│   │   └── student.css
│   └── js/
│       └── main.js
├── login.php
├── logout.php
└── index.php
```

## 🗄️ Database Schema
```sql
- users         # All users (admin, teacher, student)
- students      # Student details
- teachers      # Teacher details
- marks         # Subject marks
- attendance    # Daily attendance
- fees          # Fee records
```

## ⚙️ Local Setup (XAMPP)

1. Clone the repo:
```bash
git clone https://github.com/Engranand/student_mgmt_system.git
```

2. Copy to XAMPP:
```
C:\xampp\htdocs\student_mgmt_system
```

3. Import database:
- Open `localhost/phpmyadmin`
- Create database `student_mgmt_db`
- Import `.sql` file

4. Update `config/db.php`:
```php
$host     = 'localhost';
$dbname   = 'student_mgmt_db';
$username = 'root';
$password = '';
```

5. Run:
```
http://localhost/student_mgmt_system
```

## 📸 Screenshots

> Login Page, Admin Dashboard, Teacher Panel, Student Panel

## 👨‍💻 Developer

**Anand Raj
** — B.Tech Student  
GitHub: [@Engranand](https://github.com/Engranand)

## 📄 License

This project is for educational purposes only.
