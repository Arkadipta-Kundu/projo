# Projo

## Overview

The **Projo** is a lightweight project management tool designed for individuals or small teams. It allows you to manage projects, tasks, notes, and issues efficiently. It also includes a Kanban board for task organization and data export functionality for backups or external analysis.

---

## Features

- **User Authentication**: Secure login/logout system to protect your data.
- **Project Management**: Create, update, and delete projects.
- **Task Management**: Manage tasks with priorities, statuses, and project assignments.
- **Kanban Board**: Visualize tasks in a drag-and-drop interface.
- **Notes Section**: Jot down quick notes and optionally tag them to projects.
- **Issue Tracker**: Log and track issues with severity and status, and convert issues to tasks.
- **Data Export**: Export projects, tasks, notes, and issues in CSV or JSON formats.

---

## Installation Guide

### Prerequisites

1. **Web Server**: Install [XAMPP](https://www.apachefriends.org/) or any PHP-compatible web server.
2. **Database**: MySQL or MariaDB.
3. **Browser**: A modern web browser (e.g., Chrome, Firefox).

### Steps

1. **Clone or Download the Repository**:

   - Place the project folder (`projo`) in your web server's root directory (e.g., `c:/xampp/htdocs/`).

2. **Set Up the Database**:

   - Import the `projo.sql` file into your MySQL database. This file contains the required tables (`users`, `projects`, `tasks`, `notes`, `issues`).
   - Example tables:
     - `users`: Stores user credentials.
     - `projects`: Stores project details.
     - `tasks`: Stores task details.
     - `notes`: Stores notes.
     - `issues`: Stores issues.

3. **Configure Database Connection**:

   - Open `includes/db.php` and update the database credentials:
     ```php
     $host = 'localhost';
     $dbname = 'projo';
     $username = 'root';
     $password = '';
     ```

4. **Start the Server**:

   - Start your web server (e.g., XAMPP) and navigate to `http://localhost/projo/`.

5. **Login**:
   - Default admin credentials:
     - **Username**: `admin`
     - **Password**: `password123`

---

## Usage Guide

### **1. Login**

- Navigate to `http://localhost/projo/login.php`.
- Enter your username and password to access the dashboard.

### **2. Dashboard**

- The dashboard provides an overview of:
  - Total projects
  - Total tasks
  - Pending tasks
  - Upcoming tasks (due today or tomorrow)
- You can also export data (projects, tasks, notes, issues) in CSV or JSON formats.

### **3. Projects**

- Navigate to `Projects` from the navigation bar.
- **Add a Project**:
  - Fill in the project title, description, and deadline.
- **Edit/Delete a Project**:
  - Use the "Edit" or "Delete" buttons next to a project.

### **4. Tasks**

- Navigate to `Tasks` from the navigation bar.
- **Add a Task**:
  - Fill in the task title, description, due date, priority, status, and assign it to a project.
- **Edit/Delete a Task**:
  - Use the "Edit" or "Delete" buttons next to a task.

### **5. Kanban Board**

- Navigate to `Kanban` from the navigation bar.
- **Drag and Drop**:
  - Move tasks between columns (`To Do`, `In Progress`, `Done`).
- **Update Task Status**:
  - Dragging a task to a new column automatically updates its status.

### **6. Notes**

- Navigate to `Notes` from the navigation bar.
- **Add a Note**:
  - Write a note and optionally tag it to a project.
- **Edit/Delete a Note**:
  - Use the "Edit" or "Delete" buttons next to a note.

### **7. Issues**

- Navigate to `Issues` from the navigation bar.
- **Add an Issue**:
  - Fill in the issue title, description, severity, status, and optionally assign it to a project.
- **Convert to Task**:
  - Use the "Convert to Task" button to create a task from an issue.
- **Edit/Delete an Issue**:
  - Use the "Edit" or "Delete" buttons next to an issue.

### **8. Data Export**

- Navigate to the `Export Data` section on the dashboard.
- Select:
  - **What to Export**: Projects, Tasks, Notes, or Issues.
  - **Export Format**: CSV or JSON.
- Click the **Export** button to download the file.

### **9. Logout**

- Click the **Logout** link in the navigation bar to log out of the system.

---

## Security Notes

- **Authentication**: Only authenticated users can access the system.
- **Password Storage**: User passwords are hashed using `password_hash()` for security.
- **Session Management**: Sessions are used to maintain user authentication.

---

## Troubleshooting

1. **Database Connection Error**:

   - Ensure the database credentials in `includes/db.php` are correct.
   - Verify that the MySQL server is running.

2. **Login Issues**:

   - Ensure the `users` table exists and contains valid credentials.
   - Use the default admin credentials if no users are present.

3. **Export Issues**:
   - Ensure the `export.php` file is accessible and properly configured.

---

## Future Enhancements

- Add user roles (e.g., admin, editor).
- Implement email notifications for tasks and issues.
- Add file attachments for tasks and issues.

---

## License

This project is open-source and free to use.

---

## Contact

For support or feedback, please contact the developer at [arkadipta.dev@gmail.com].
# projo
# projo
