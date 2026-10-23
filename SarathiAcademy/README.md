# SarathiAcademy – Installation Guide
## Learn · Grow · Achieve

---

## ✅ Requirements
- XAMPP (Apache + MySQL + PHP 8.0+)
- Browser (Chrome / Firefox recommended)

---

## 🚀 Quick Setup (5 Steps)

### Step 1 – Copy Project
Copy the entire `SarathiAcademy` folder into:
```
C:\xampp\htdocs\SarathiAcademy\
```

### Step 2 – Import Database
1. Open **http://localhost/phpmyadmin**
2. Click **Import** tab
3. Choose file: `SarathiAcademy/sarathi_academy.sql`
4. Click **Go**

### Step 3 – Check Config (Optional)
Open `includes/config.php` and verify:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // Your MySQL username
define('DB_PASS', '');        // Your MySQL password (blank for XAMPP default)
define('DB_NAME', 'sarathi_academy');
define('SITE_URL', 'http://localhost/SarathiAcademy'); // No trailing slash
```

### Step 4 – Create Uploads Folder (if not present)
The `uploads/gallery/` folder should exist. If not:
- Create `uploads/` inside the project root
- Create `uploads/gallery/` inside it

### Step 5 – Open Website
- **Public Site:**  http://localhost/SarathiAcademy/
- **Admin Panel:** http://localhost/SarathiAcademy/admin/

---

## 🔐 Login Credentials

| Role  | Email                          | Password    |
|-------|--------------------------------|-------------|
| Admin | admin@SarathiAcademy.com       | Admin@1234  |
| Staff | rahul@SarathiAcademy.com       | Staff@1234  |
| Staff | priya@SarathiAcademy.com       | Staff@1234  |

> Students register themselves via: http://localhost/SarathiAcademy/register.php

---

## 📁 Project Structure

```
SarathiAcademy/
├── index.php              ← Homepage
├── about.php              ← About page
├── courses.php            ← Courses listing
├── gallery.php            ← Gallery
├── enquiry.php            ← Enquiry form
├── contact.php            ← Contact page
├── login.php              ← Login
├── register.php           ← Student registration
├── logout.php             ← Logout
│
├── includes/
│   ├── config.php         ← DB config (edit here)
│   ├── auth.php           ← Session & auth helpers
│   ├── header.php         ← Public header
│   └── footer.php         ← Public footer
│
├── admin/
│   ├── index.php          ← Admin dashboard
│   ├── includes/
│   │   ├── admin_header.php
│   │   └── admin_footer.php
│   └── pages/
│       ├── staff.php          ← Manage staff/faculty
│       ├── courses.php        ← Manage courses
│       ├── batches.php        ← Manage batches
│       ├── assignments.php    ← Faculty assignment
│       ├── admissions.php     ← Approve/reject admissions
│       ├── fees.php           ← Record fee payments
│       ├── attendance.php     ← Mark attendance
│       ├── attendance_report.php ← Attendance reports
│       ├── reports.php        ← Fees & admissions reports
│       ├── enquiries.php      ← View/manage enquiries
│       └── gallery_admin.php  ← Upload gallery images
│
├── student/
│   ├── dashboard.php      ← Student portal
│   └── apply.php          ← Admission application form
│
├── assets/
│   ├── css/
│   │   ├── style.css      ← Public site CSS (sky blue theme)
│   │   └── admin.css      ← Admin panel CSS
│   ├── js/
│   │   └── main.js        ← JavaScript
│   └── images/
│       └── logo.png       ← SarathiAcademy logo
│
├── uploads/
│   ├── gallery/           ← Gallery image uploads
│   └── .htaccess          ← Security: no PHP execution
│
└── sarathi_academy.sql    ← Database script
```

---

## 🎯 Feature Summary

### Public Website
- Home, About, Courses, Gallery, Enquiry, Contact
- Student Register & Login
- Social media links (Instagram, WhatsApp, Facebook, LinkedIn)

### Admin Panel (Admin Only)
- Manage Staff & Faculty
- Manage Courses (fees, duration, coordinator)
- Manage Batches (under courses)
- Assign Faculty per Batch per Subject
- Upload Gallery Images
- View & manage Enquiries

### Admin & Staff
- Approve / Reject Admissions
- Record Fee Payments (with receipt, mode)
- Mark Attendance (batch-wise, subject-wise)
- Attendance Reports with CSV Export
- Fees & Admissions Reports with CSV Export + Print

### Student Portal
- Register & Login
- Fill Admission Application Form
- View Admission Status (Pending/Approved/Rejected)
- View Fees Paid
- View Attendance Summary & Recent Records

---

## 📞 Contact Details
- **Address:** Vidyanagar, Karad, Maharashtra – 415124
- **Phone:** 9139959698 / 9172135355
- **Email:** admin@SarathiAcademy.com

---
*Built with PHP 8 + MySQL + XAMPP | Sky Blue Theme*
