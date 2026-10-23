-- ============================================================
--  SarathiAcademy Database Script
--  Import via phpMyAdmin or: mysql -u root -p < sarathi_academy.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS sarathi_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sarathi_academy;

-- -------------------------------------------------------
-- USERS TABLE  (admin / staff / student)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    email       VARCHAR(120) NOT NULL UNIQUE,
    phone       VARCHAR(20),
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','staff','student') NOT NULL DEFAULT 'student',
    designation VARCHAR(100),          -- e.g. Faculty-Physics, Coordinator
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default Admin  (password: Admin@1234)
INSERT INTO users (name, email, phone, password, role, designation) VALUES
('Super Admin','admin@SarathiAcademy.com','9139959698',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','Administrator');

-- Sample Staff  (password: Staff@1234)
INSERT INTO users (name, email, phone, password, role, designation) VALUES
('Rahul Patil','rahul@SarathiAcademy.com','9000000001',
 '$2y$10$TKh8H1.PFfSqXC5WBbMGNO7Z.tXQm7q.QsBdNnTg7EfHjXXPWNS8O','staff','Faculty-Physics'),
('Priya Sharma','priya@SarathiAcademy.com','9000000002',
 '$2y$10$TKh8H1.PFfSqXC5WBbMGNO7Z.tXQm7q.QsBdNnTg7EfHjXXPWNS8O','staff','Faculty-Chemistry');

-- -------------------------------------------------------
-- COURSES
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS courses (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,         -- e.g. 11th Science
    code          VARCHAR(20)  NOT NULL UNIQUE,  -- e.g. 11TH
    description   TEXT,
    duration      VARCHAR(80),                   -- e.g. 1 Year
    fees          DECIMAL(10,2) DEFAULT 0.00,
    coordinator_id INT,
    is_active     TINYINT(1) DEFAULT 1,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coordinator_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO courses (name, code, description, duration, fees) VALUES
('11th Science','11TH','Complete 11th Science curriculum covering Physics, Chemistry, Maths & Biology.','1 Year',35000.00),
('12th Science','12TH','Complete 12th Science curriculum with board exam focus.','1 Year',38000.00),
('JEE Preparation','JEE','Joint Entrance Exam coaching – Physics, Chemistry & Mathematics.','2 Years',55000.00),
('NEET Preparation','NEET','National Eligibility cum Entrance Test – Physics, Chemistry & Biology.','2 Years',55000.00),
('MHT-CET','CET','Maharashtra CET preparation for Engineering & Pharmacy aspirants.','1 Year',28000.00);

-- -------------------------------------------------------
-- BATCHES
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS batches (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    course_id   INT NOT NULL,
    batch_name  VARCHAR(100) NOT NULL,
    start_date  DATE,
    end_date    DATE,
    timing      VARCHAR(80),
    capacity    INT DEFAULT 40,
    is_active   TINYINT(1) DEFAULT 1,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

INSERT INTO batches (course_id, batch_name, start_date, end_date, timing, capacity) VALUES
(1,'11th Batch A – 2025-26','2025-06-01','2026-03-31','07:00 AM – 09:00 AM',40),
(1,'11th Batch B – 2025-26','2025-06-01','2026-03-31','04:00 PM – 06:00 PM',40),
(2,'12th Batch A – 2025-26','2025-06-01','2026-03-31','07:00 AM – 09:00 AM',40),
(3,'JEE 2026 Batch','2025-06-15','2026-04-30','09:00 AM – 12:00 PM',35),
(4,'NEET 2026 Batch','2025-06-15','2026-04-30','12:30 PM – 03:30 PM',35),
(5,'CET 2026 Batch','2025-07-01','2026-03-31','04:00 PM – 06:00 PM',40);

-- -------------------------------------------------------
-- SUBJECTS per Course
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS subjects (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name      VARCHAR(80) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

INSERT INTO subjects (course_id, name) VALUES
(1,'Physics'),(1,'Chemistry'),(1,'Mathematics'),(1,'Biology'),
(2,'Physics'),(2,'Chemistry'),(2,'Mathematics'),(2,'Biology'),
(3,'Physics'),(3,'Chemistry'),(3,'Mathematics'),
(4,'Physics'),(4,'Chemistry'),(4,'Biology'),
(5,'Physics'),(5,'Chemistry'),(5,'Mathematics');

-- -------------------------------------------------------
-- FACULTY – BATCH – SUBJECT assignments
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS faculty_assignments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    batch_id   INT NOT NULL,
    subject_id INT NOT NULL,
    faculty_id INT NOT NULL,
    UNIQUE KEY uq_assign (batch_id, subject_id),
    FOREIGN KEY (batch_id)   REFERENCES batches(id)   ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id)  ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES users(id)     ON DELETE CASCADE
);

-- -------------------------------------------------------
-- ADMISSIONS
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS admissions (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    batch_id        INT NOT NULL,
    roll_no         VARCHAR(30),
    parent_name     VARCHAR(120),
    parent_phone    VARCHAR(20),
    address         TEXT,
    dob             DATE,
    prev_school     VARCHAR(150),
    prev_percentage DECIMAL(5,2),
    photo           VARCHAR(255),
    id_proof        VARCHAR(255),
    status          ENUM('pending','approved','rejected') DEFAULT 'pending',
    notes           TEXT,
    approved_by     INT,
    approved_at     DATETIME,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (batch_id)    REFERENCES batches(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- FEES
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS fees (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    admission_id INT NOT NULL,
    amount       DECIMAL(10,2) NOT NULL,
    paid_date    DATE NOT NULL,
    mode         ENUM('cash','online','cheque','dd') DEFAULT 'cash',
    receipt_no   VARCHAR(50),
    remarks      TEXT,
    collected_by INT,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admission_id)  REFERENCES admissions(id) ON DELETE CASCADE,
    FOREIGN KEY (collected_by)  REFERENCES users(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- ATTENDANCE
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS attendance (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    batch_id     INT NOT NULL,
    student_id   INT NOT NULL,
    subject_id   INT,
    attend_date  DATE NOT NULL,
    status       ENUM('present','absent','late') DEFAULT 'present',
    marked_by    INT,
    UNIQUE KEY uq_att (batch_id, student_id, subject_id, attend_date),
    FOREIGN KEY (batch_id)   REFERENCES batches(id)   ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id)     ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id)  ON DELETE SET NULL,
    FOREIGN KEY (marked_by)  REFERENCES users(id)     ON DELETE SET NULL
);

-- -------------------------------------------------------
-- GALLERY
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS gallery (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150),
    image_path  VARCHAR(255) NOT NULL,
    category    VARCHAR(80) DEFAULT 'General',
    uploaded_by INT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- ENQUIRIES
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS enquiries (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    email      VARCHAR(120),
    phone      VARCHAR(20),
    course     VARCHAR(80),
    message    TEXT,
    status     ENUM('new','contacted','closed') DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- TESTIMONIALS
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS testimonials (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(120),
    content    TEXT,
    score      VARCHAR(50),
    course     VARCHAR(80),
    is_active  TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO testimonials (student_name, content, score, course) VALUES
('Aditya Kulkarni','SarathiAcademy changed my life! The faculty support and study material were exceptional.','JEE AIR 1842','JEE'),
('Sneha Deshmukh','I cracked NEET in my first attempt thanks to the dedicated coaching here.','NEET 640/720','NEET'),
('Rohan Jadhav','The batch timings and personal attention helped me score 92% in boards.','92% in 12th','12th Science');

-- Done!
SELECT 'SarathiAcademy database created successfully!' AS Status;
