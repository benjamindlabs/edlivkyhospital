-- EdlivkyHospital Database Setup
-- Run this script to create the database and all required tables

CREATE DATABASE IF NOT EXISTS edlivkyhospital;
USE edlivkyhospital;

-- Users table for authentication (future use)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'nurse', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Patients table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    blood_type ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'),
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Staff table
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('doctor', 'nurse', 'admin', 'technician', 'support') NOT NULL,
    department VARCHAR(50),
    license_number VARCHAR(50),
    hire_date DATE,
    status ENUM('active', 'on_leave', 'inactive') DEFAULT 'active',
    phone VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    staff_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT,
    status ENUM('scheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);

-- Medical Records table
CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    staff_id INT NOT NULL,
    visit_date DATE NOT NULL,
    diagnosis TEXT,
    treatment_plan TEXT,
    prescriptions TEXT,
    follow_up_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);

-- Beds table
CREATE TABLE IF NOT EXISTS beds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bed_number VARCHAR(10) UNIQUE NOT NULL,
    ward VARCHAR(50) NOT NULL,
    bed_type ENUM('general', 'private', 'icu', 'emergency') DEFAULT 'general',
    status ENUM('available', 'occupied', 'maintenance', 'reserved') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bed Assignments table
CREATE TABLE IF NOT EXISTS bed_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    bed_id INT NOT NULL,
    admission_date DATETIME NOT NULL,
    discharge_date DATETIME NULL,
    reason TEXT,
    status ENUM('active', 'discharged') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (bed_id) REFERENCES beds(id) ON DELETE CASCADE
);

-- Insert sample beds
INSERT INTO beds (bed_number, ward, bed_type, status) VALUES
('A101', 'General Ward A', 'general', 'available'),
('A102', 'General Ward A', 'general', 'available'),
('A103', 'General Ward A', 'general', 'occupied'),
('B201', 'General Ward B', 'general', 'available'),
('B202', 'General Ward B', 'general', 'available'),
('ICU01', 'ICU', 'icu', 'available'),
('ICU02', 'ICU', 'icu', 'occupied'),
('ICU03', 'ICU', 'icu', 'available'),
('P301', 'Private Ward', 'private', 'available'),
('P302', 'Private Ward', 'private', 'available'),
('E001', 'Emergency', 'emergency', 'available'),
('E002', 'Emergency', 'emergency', 'available');

-- Insert sample staff
INSERT INTO staff (full_name, role, department, license_number, hire_date, status, phone, email) VALUES
('Dr. John Smith', 'doctor', 'Cardiology', 'MD12345', '2020-01-15', 'active', '+1234567890', 'john.smith@edlivky.com'),
('Dr. Sarah Johnson', 'doctor', 'Emergency', 'MD12346', '2019-03-20', 'active', '+1234567891', 'sarah.johnson@edlivky.com'),
('Nurse Mary Wilson', 'nurse', 'General', 'RN12345', '2021-06-10', 'active', '+1234567892', 'mary.wilson@edlivky.com'),
('Nurse David Brown', 'nurse', 'ICU', 'RN12346', '2020-09-05', 'active', '+1234567893', 'david.brown@edlivky.com'),
('Admin Lisa Davis', 'admin', 'Administration', NULL, '2018-12-01', 'active', '+1234567894', 'lisa.davis@edlivky.com');

-- Insert sample patients
INSERT INTO patients (first_name, last_name, date_of_birth, gender, phone, email, address, blood_type, emergency_contact_name, emergency_contact_phone) VALUES
('Alice', 'Johnson', '1985-05-15', 'female', '+1234567895', 'alice.johnson@email.com', '123 Main St, City, State', 'A+', 'Bob Johnson', '+1234567896'),
('Michael', 'Davis', '1978-12-03', 'male', '+1234567897', 'michael.davis@email.com', '456 Oak Ave, City, State', 'O+', 'Susan Davis', '+1234567898'),
('Emma', 'Wilson', '1992-08-22', 'female', '+1234567899', 'emma.wilson@email.com', '789 Pine Rd, City, State', 'B+', 'James Wilson', '+1234567900');

-- Insert sample appointments
INSERT INTO appointments (patient_id, staff_id, appointment_date, appointment_time, reason, status) VALUES
(1, 1, CURDATE(), '10:00:00', 'Regular checkup', 'scheduled'),
(2, 2, CURDATE(), '14:30:00', 'Emergency consultation', 'scheduled'),
(3, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'Follow-up visit', 'scheduled');
