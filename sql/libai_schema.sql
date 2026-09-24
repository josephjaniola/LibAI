-- LibAI initial database schema (placeholder)
CREATE DATABASE IF NOT EXISTS libai DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE libai;

-- Admins table (example)
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  fullname VARCHAR(200) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Librarians
CREATE TABLE IF NOT EXISTS librarians (
  id INT AUTO_INCREMENT PRIMARY KEY,
  librarian_id VARCHAR(50) NOT NULL UNIQUE,
  username VARCHAR(100) DEFAULT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  firstname VARCHAR(100),
  middlename VARCHAR(100),
  lastname VARCHAR(100),
  mobile VARCHAR(30),
  profile_picture VARCHAR(255) DEFAULT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(50) NOT NULL UNIQUE,
  firstname VARCHAR(100) NOT NULL,
  middlename VARCHAR(100),
  lastname VARCHAR(100) NOT NULL,
  course VARCHAR(100),
  year_level VARCHAR(20),
  email VARCHAR(150) DEFAULT NULL,
  mobile VARCHAR(30) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  profile_picture VARCHAR(255) DEFAULT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Faculty
CREATE TABLE IF NOT EXISTS faculty (
  id INT AUTO_INCREMENT PRIMARY KEY,
  faculty_id VARCHAR(50) NOT NULL UNIQUE,
  firstname VARCHAR(100) NOT NULL,
  middlename VARCHAR(100),
  lastname VARCHAR(100) NOT NULL,
  department VARCHAR(150),
  position VARCHAR(100),
  email VARCHAR(150) DEFAULT NULL,
  mobile VARCHAR(30) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  profile_picture VARCHAR(255) DEFAULT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  description TEXT DEFAULT NULL
);

-- Authors
CREATE TABLE IF NOT EXISTS authors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  bio TEXT DEFAULT NULL
);

-- Publishers
CREATE TABLE IF NOT EXISTS publishers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  address TEXT DEFAULT NULL,
  contact VARCHAR(150) DEFAULT NULL
);

-- Books
CREATE TABLE IF NOT EXISTS books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(500) NOT NULL,
  subtitle VARCHAR(500) DEFAULT NULL,
  isbn VARCHAR(50) DEFAULT NULL,
  accession_number VARCHAR(100) DEFAULT NULL,
  call_number VARCHAR(100) DEFAULT NULL,
  edition VARCHAR(100) DEFAULT NULL,
  volume VARCHAR(100) DEFAULT NULL,
  pages VARCHAR(50) DEFAULT NULL,
  category_id INT DEFAULT NULL,
  language VARCHAR(50) DEFAULT NULL,
  shelf_location VARCHAR(150) DEFAULT NULL,
  year_published YEAR DEFAULT NULL,
  description TEXT DEFAULT NULL,
  keywords VARCHAR(500) DEFAULT NULL,
  remarks TEXT DEFAULT NULL,
  cover_image VARCHAR(255) DEFAULT NULL,
  publisher_id INT DEFAULT NULL,
  date_received DATE DEFAULT NULL,
  rfid_uid VARCHAR(100) DEFAULT NULL UNIQUE,
  status ENUM('available','borrowed','reserved','overdue','lost','damaged','archived') DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_books_publisher FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL
);

-- Book <-> Author many-to-many
CREATE TABLE IF NOT EXISTS book_authors (
  book_id INT NOT NULL,
  author_id INT NOT NULL,
  PRIMARY KEY (book_id, author_id),
  CONSTRAINT fk_ba_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
  CONSTRAINT fk_ba_author FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
);

-- Borrow transactions
CREATE TABLE IF NOT EXISTS borrow_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  borrower_type ENUM('student','faculty') NOT NULL,
  borrower_ref_id INT NOT NULL,
  borrower_name VARCHAR(255) NOT NULL,
  borrower_email VARCHAR(150) DEFAULT NULL,
  borrower_phone VARCHAR(50) DEFAULT NULL,
  book_id INT NOT NULL,
  rfid_uid VARCHAR(100) DEFAULT NULL,
  borrow_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  due_date DATETIME NOT NULL,
  return_date DATETIME DEFAULT NULL,
  status ENUM('borrowed','returned','overdue','lost','damaged') DEFAULT 'borrowed',
  remarks TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_borrow_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT
);

-- Reservations
CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT NOT NULL,
  rfid_uid VARCHAR(100) DEFAULT NULL,
  borrower_type ENUM('student','faculty') NOT NULL,
  borrower_ref_id INT NOT NULL,
  reserved_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','approved','ready','cancelled') DEFAULT 'pending',
  expires_at DATETIME DEFAULT NULL,
  CONSTRAINT fk_res_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Inventory logs
CREATE TABLE IF NOT EXISTS inventory_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT DEFAULT NULL,
  rfid_uid VARCHAR(100) DEFAULT NULL,
  action ENUM('received','borrowed','returned','archived','lost','damaged','updated') NOT NULL,
  note TEXT DEFAULT NULL,
  actor_type ENUM('admin','librarian') DEFAULT NULL,
  actor_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_inv_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE SET NULL
);

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_type ENUM('admin','librarian','student','faculty') NOT NULL,
  user_ref_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  type VARCHAR(100) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Announcements
CREATE TABLE IF NOT EXISTS announcements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  created_by INT DEFAULT NULL,
  starts_at DATETIME DEFAULT NULL,
  ends_at DATETIME DEFAULT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activity logs
CREATE TABLE IF NOT EXISTS activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_type VARCHAR(50) DEFAULT NULL,
  user_ref_id INT DEFAULT NULL,
  action VARCHAR(255) NOT NULL,
  detail TEXT DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- System settings
CREATE TABLE IF NOT EXISTS system_settings (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` TEXT DEFAULT NULL,
  description TEXT DEFAULT NULL
);

-- Favorites
CREATE TABLE IF NOT EXISTS favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_type ENUM('student','faculty') NOT NULL,
  user_ref_id INT NOT NULL,
  book_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_fav_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- OTP codes for phone/email sign-in
CREATE TABLE IF NOT EXISTS otp_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_type ENUM('admin','librarian','student','faculty') NOT NULL,
  user_ref_id INT NOT NULL,
  identifier VARCHAR(255) NOT NULL,
  code_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL,
  attempts INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_otp_user (user_type, user_ref_id),
  INDEX idx_otp_identifier (identifier)
);

ALTER TABLE admins
  ADD COLUMN IF NOT EXISTS auth_provider VARCHAR(30) NOT NULL DEFAULT 'local',
  ADD COLUMN IF NOT EXISTS provider_user_id VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS verified_email VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS verified_phone VARCHAR(30) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS auth_name VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS last_login_at DATETIME DEFAULT NULL;

ALTER TABLE librarians
  ADD COLUMN IF NOT EXISTS auth_provider VARCHAR(30) NOT NULL DEFAULT 'local',
  ADD COLUMN IF NOT EXISTS provider_user_id VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS verified_email VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS verified_phone VARCHAR(30) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS auth_name VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS last_login_at DATETIME DEFAULT NULL;

ALTER TABLE students
  ADD COLUMN IF NOT EXISTS auth_provider VARCHAR(30) NOT NULL DEFAULT 'local',
  ADD COLUMN IF NOT EXISTS provider_user_id VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS verified_email VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS verified_phone VARCHAR(30) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS auth_name VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS last_login_at DATETIME DEFAULT NULL;

ALTER TABLE faculty
  ADD COLUMN IF NOT EXISTS auth_provider VARCHAR(30) NOT NULL DEFAULT 'local',
  ADD COLUMN IF NOT EXISTS provider_user_id VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS verified_email VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS verified_phone VARCHAR(30) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS auth_name VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS last_login_at DATETIME DEFAULT NULL;

-- Indexes to speed common queries
CREATE INDEX idx_books_rfid ON books(rfid_uid);
CREATE INDEX idx_borrow_ref ON borrow_transactions(borrower_type, borrower_ref_id);
CREATE INDEX idx_res_ref ON reservations(borrower_type, borrower_ref_id);

-- Example admin seed (change password after import)
-- INSERT INTO admins (username, email, password, fullname) VALUES ('admin', 'admin@example.com', '<password_hash_here>', 'System Administrator');

-- Seed data (examples). Replace password placeholder with a generated hash from tools/generate_password_hash.php
INSERT INTO categories (name, description) VALUES ('Computer Science','CS books'), ('Mathematics','Math books'), ('Literature','Fiction and literature');
INSERT INTO publishers (name) VALUES ('Pearson'), ('O Reilly'), ('Springer');
INSERT INTO authors (name) VALUES ('Author One'), ('Author Two');

-- Admin, librarian, student and faculty seeds for testing
INSERT INTO admins (username, email, password, fullname) VALUES
  ('admin', 'admin@cec.edu.ph', '$2y$10$XVxsp9k.vxQ5T2f0BKbki.in0m2zoIkS7dbyBQefq./ElMmixE2P2', 'System Administrator');

INSERT INTO librarians (librarian_id, username, email, password, firstname, lastname, mobile) VALUES
  ('LIB001', 'librarian', 'librarian@cec.edu.ph', '$2y$10$y3guFrRlcokBLtZ.CNmVvuD3Q5q5nG6KU8EZtNxwpY5jE5zeeUJFq', 'Librarian', 'User', '09171234567');

INSERT INTO students (student_id, firstname, middlename, lastname, course, year_level, email, mobile, password) VALUES
  ('S1001', 'John', 'A.', 'Doe', 'BS Computer Science', '2', 'john.doe@student.edu.ph', '09171234568', '$2y$10$EgJnrScF2qvaUzejVBwx/.VgY.LcsCEmw4vFXw4tY9tYZWMTNHCfa');

INSERT INTO faculty (faculty_id, firstname, middlename, lastname, department, position, email, mobile, password) VALUES
  ('F1001', 'Maria', 'L.', 'Santos', 'Information Technology', 'Instructor', 'maria.santos@faculty.edu.ph', '09171234569', '$2y$10$NuA6GtUMvjLWtP5VQMlaY.VdH2xcjzFjZb2j1U2G/b0/WyHbyap6m');


