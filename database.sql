-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS ucms;
USE ucms;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
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

-- Insert departments
INSERT INTO departments (name) VALUES
('Biotechnology'),
('Chemistry'),
('Computer Science'),
('Electronic Engineering'),
('Environmental Sciences'),
('Mathematical Sciences'),
('Physics'),
('Software Engineering'),
('Islamic Studies'),
('Business Administration'),
('Anthropology'),
('Behavioral Sciences'),
('Communication and Media Studies'),
('Computer Arts'),
('Defense and Diplomatic Studies'),
('Economics'),
('English'),
('Fine Arts'),
('Gender Studies'),
('Sociology'),
('Urdu'),
('Law'),
('Public Administration'),
('Education');

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

-- Create complaint_status table
CREATE TABLE IF NOT EXISTS complaint_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Create complaints table
DROP TABLE IF EXISTS complaints;
CREATE TABLE IF NOT EXISTS complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    department_id INT NOT NULL,
    category_id INT NOT NULL,
    subcategory_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'resolved', 'rejected') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    resolution_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE CASCADE
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

-- Create complaint_responses table
CREATE TABLE IF NOT EXISTS complaint_responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create faculty_complaints table
CREATE TABLE IF NOT EXISTS faculty_complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_id INT NOT NULL,
    faculty_id INT NOT NULL,
    status ENUM('pending', 'in_progress', 'resolved') NOT NULL DEFAULT 'pending',
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id),
    FOREIGN KEY (faculty_id) REFERENCES users(id)
);

-- Insert default admin user
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@ucms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert default complaint statuses
INSERT INTO complaint_status (name, description) VALUES
('Pending', 'Complaint is waiting for review'),
('In Progress', 'Complaint is being processed'),
('Resolved', 'Complaint has been resolved'),
('Rejected', 'Complaint has been rejected');

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

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert main categories
INSERT INTO categories (name, description) VALUES
('Academic Issues', 'Complaints related to classes, faculty, grading, and coursework.'),
('Administrative Issues', 'Problems with general university administration and office functioning.'),
('IT & Technical Issues', 'Complaints involving digital infrastructure and software systems.'),
('Campus Facilities', 'Concerns about physical infrastructure and facility maintenance.'),
('Harassment & Misconduct', 'Sensitive category to report any personal safety or dignity concerns.'),
('Security & Safety', 'Concerns about personal safety and security infrastructure.'),
('Extracurricular & Societies', 'Issues regarding student societies and extracurricular opportunities.'),
('Environmental & Cleanliness', 'Complaints about environmental conditions and hygiene.'),
('Suggestions/Feedback', 'Non-complaint submissions meant to offer constructive feedback or improvement ideas.'),
('Other / General Complaints', 'Any complaint not falling into the defined categories.');

-- Subcategories table
CREATE TABLE subcategories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Insert subcategories
INSERT INTO subcategories (category_id, name, description) VALUES
-- Academic Issues subcategories
(1, 'Course Content Issues', 'Outdated syllabus, missing resources.'),
(1, 'Faculty Behavior', 'Rude behavior, unfair treatment, lack of punctuality.'),
(1, 'Exam & Result Issues', 'Delayed result, incorrect grades, rechecking requests.'),
(1, 'Class Scheduling', 'Clash in timetable, rescheduling without notice.'),
(1, 'Attendance Issues', 'Unjust attendance marking, missing records.'),
(1, 'Thesis/Project Supervision', 'Unavailable supervisor, lack of guidance.'),

-- Administrative Issues subcategories
(2, 'Registrar Office', 'Delay in issuing transcripts, degree, or enrollment verification.'),
(2, 'Accounts/Finance Office', 'Fee discrepancies, wrong fine imposition.'),
(2, 'Library', 'Book unavailability, rude staff, system errors.'),
(2, 'Hostel Management', 'Room allocation issues, maintenance, food quality.'),
(2, 'Transportation Services', 'Late buses, rude driver/staff, overloading.'),

-- IT & Technical Issues subcategories
(3, 'University Portal/Login Issues', 'Inaccessibility, login errors, course registration errors.'),
(3, 'Wi-Fi Connectivity', 'Poor signals, login failure.'),
(3, 'Lab Computers/Equipment', 'Non-functional PCs, broken equipment.'),
(3, 'Email/Official Accounts', 'Unable to access institutional email or LMS.'),

-- Campus Facilities subcategories
(4, 'Classroom Environment', 'Broken chairs, non-functional fans/AC, cleanliness.'),
(4, 'Restrooms', 'Unhygienic conditions, broken fixtures.'),
(4, 'Cafeteria/Food Court', 'Food hygiene, overpricing, rude staff.'),
(4, 'Water Facilities', 'No clean drinking water, non-working coolers.'),
(4, 'Sports Complex', 'Equipment damage, limited access, unavailability of coaches.'),

-- Harassment & Misconduct subcategories
(5, 'Sexual Harassment', 'By peers, staff, or faculty (strict confidentiality).'),
(5, 'Bullying', 'Peer-to-peer bullying, ragging.'),
(5, 'Discrimination', 'Gender, religion, ethnicity-based bias.'),
(5, 'Verbal or Physical Abuse', 'In classroom, hostel, or anywhere on campus.'),

-- Security & Safety subcategories
(6, 'Lack of Security Staff', 'At gates or hostel.'),
(6, 'Security Check Issues', 'Misconduct by guards, unnecessary delays.'),
(6, 'Emergency Response', 'Delayed or no response to incidents.'),
(6, 'Lost & Found', 'Report of missing belongings or found items.'),

-- Extracurricular & Societies subcategories
(7, 'Event Management Issues', 'Poorly organized university events.'),
(7, 'Society Disputes', 'Favoritism, mismanagement.'),
(7, 'Club Registrations', 'Delay or non-recognition of clubs/societies.'),

-- Environmental & Cleanliness subcategories
(8, 'Waste Management', 'Overflowing bins, lack of cleaning staff.'),
(8, 'Green Spaces', 'Damaged plants, neglected gardens.'),
(8, 'Air/Noise Pollution', 'Nearby construction, loudspeakers in academic block.'),

-- Suggestions/Feedback subcategories
(9, 'Academic Improvements', 'New courses, teaching methods.'),
(9, 'Facility Upgrades', 'Smart classrooms, better furniture.'),
(9, 'Policy Suggestions', 'Changes in attendance, grading, or registration policies.'),

-- Other / General Complaints subcategories
(10, 'Uncategorized Issue', 'Free-text description by the student.'),
(10, 'Multiple Departments Involved', 'Cross-functional issues.'); 