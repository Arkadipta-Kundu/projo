-- Create the database
CREATE DATABASE IF NOT EXISTS solo_pm;
USE solo_pm;

-- Create the `users` table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (username, password) 
VALUES ('admin', '$2y$10$eImiTXuWVxfM37uY4JANjQe5QxqU3eGz1j8a9z5eF9E6k9Q9K9E6K'); -- Password: password123

-- Create the `projects` table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the `tasks` table
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATE,
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    status ENUM('To Do', 'In Progress', 'Done') DEFAULT 'To Do',
    project_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
);

-- Create the `notes` table
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    project_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
);

-- Create the `issues` table
CREATE TABLE issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('Low', 'Medium', 'High') DEFAULT 'Low',
    status ENUM('Open', 'Resolved') DEFAULT 'Open',
    project_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
);