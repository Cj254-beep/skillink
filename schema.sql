CREATE DATABASE IF NOT EXISTS skilllink CHARACTER SET utf8mb4;
USE skilllink;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('student','employer') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE student_profiles (
  user_id INT PRIMARY KEY,
  course VARCHAR(150) NOT NULL,
  year_of_study TINYINT NOT NULL,
  skills TEXT NOT NULL,
  cv_path VARCHAR(255) NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE openings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employer_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  required_skills TEXT NOT NULL,
  positions INT NOT NULL DEFAULT 1,
  closing_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  opening_id INT NOT NULL,
  student_id INT NOT NULL,
  status ENUM('applied','shortlisted','rejected','accepted') NOT NULL DEFAULT 'applied',
  applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY one_application (opening_id, student_id),
  FOREIGN KEY (opening_id) REFERENCES openings(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);
