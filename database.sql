-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS ucms_db;
USE ucms_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create complaint_categories table
CREATE TABLE IF NOT EXISTS complaint_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create complaint_subcategories table
CREATE TABLE IF NOT EXISTS complaint_subcategories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES complaint_categories(id)
);

-- Create complaints table
DROP TABLE IF EXISTS complaints;
CREATE TABLE IF NOT EXISTS complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    roll_number VARCHAR(50),
    department_id INT NOT NULL,
    category_id INT NOT NULL,
    subcategory_id INT,
    assigned_to INT,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    urgency_level ENUM('Low', 'Medium', 'High') DEFAULT 'Low',
    status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending',
    attachment_path VARCHAR(255),
    date_of_incident DATE,
    location_of_incident VARCHAR(255),
    anonymous_submission BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (category_id) REFERENCES complaint_categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES complaint_subcategories(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

-- Create messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id),
    FOREIGN KEY (sender_id) REFERENCES users(id)
);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('complaint', 'message', 'system') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default admin user
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@ucms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert some default departments
INSERT INTO departments (name) VALUES 
('Computer Science'),
('Electrical Engineering'),
('Mechanical Engineering'),
('Civil Engineering'),
('Business Administration');

-- Remove all previous complaint categories and subcategories
DELETE FROM complaint_subcategories;
DELETE FROM complaint_categories;

-- Insert only the specified complaint categories
INSERT INTO complaint_categories (id, name) VALUES
(1, 'Academic'),
(2, 'Hostel & Accommodation'),
(3, 'Examination & Evaluation'),
(4, 'IT & Technical Support'),
(5, 'Administration & Facilities'),
(6, 'Harassment or Misconduct'),
(7, 'Other');

-- Insert only the specified complaint subcategories
INSERT INTO complaint_subcategories (category_id, name) VALUES
(1, 'Course Content Issue'),
(1, 'Biased Grading'),
(1, 'Faculty Misconduct'),
(1, 'Class Rescheduling'),
(1, 'Lab Facilities'),
(2, 'Room Cleanliness'),
(2, 'Water/Electricity Issues'),
(2, 'Warden Behavior'),
(2, 'Mess Food Quality'),
(2, 'Safety Concerns'),
(3, 'Result Delay'),
(3, 'Wrong Marks Entry'),
(3, 'Unfair Viva'),
(3, 'Rechecking Requests'),
(4, 'LMS Issues'),
(4, 'Wi-Fi Connectivity'),
(4, 'Email Login Problem'),
(4, 'Software Access'),
(5, 'Library Services'),
(5, 'Transport Problems'),
(5, 'Cafeteria Hygiene'),
(5, 'Lost & Found'),
(5, 'Maintenance Delays'),
(6, 'Student Misbehavior'),
(6, 'Faculty Misconduct'),
(6, 'Anonymous Reporting Option'),
(6, 'Urgent Handling Required'),
(7, 'Suggestions'); 