# ![Projo Icon](assets/images/favicon.ico) Projo — Solo Project Manager

**Projo** is a modern, self-hosted project management system built for solo developers and small teams. It offers everything you need to stay organized and productive — including project and task management, a visual Kanban board, Gantt charts, issue tracking, notes, imports/exports, and more — all wrapped in a clean, responsive UI.

---

## 🚀 Features

- 🔐 **User Authentication** — Secure login/logout with hashed passwords and change password option.
- 📁 **Project Management** — Create, update, delete, and track project timelines with Gantt views.
- ✅ **Task Management** — Manage tasks with priorities, due dates, and progress statuses.
- 📊 **Kanban Board** — Drag-and-drop task interface with AJAX-based live updates and visual status feedback.
- 🗓️ **Gantt Chart** — Visualize task and project timelines, complete with a today marker and flexible time views.
- 📝 **Notes** — Jot down notes, search/filter them, and tag them to specific projects.
- 🐞 **Issue Tracker** — Log issues by severity and status; convert them directly into actionable tasks.
- 📤 **Data Export** — Download projects, tasks, notes, and issues as CSV or JSON for backup or external use.
- 📥 **Data Import** — Upload structured JSON or CSV files to populate your project/task database.
- 💾 **Database Backup** — Generate and download a full `.sql` backup of your database.
- ♻️ **Reset Application** — Quickly clear all project data and return to default state.
- 🌙 **Dark Mode** — Optional dark mode for a more comfortable visual experience.
- 📚 **Activity Logs** — Track user events like data exports and password changes.

---

## ⚙️ Installation

### Requirements

- PHP-compatible web server (e.g. [XAMPP](https://www.apachefriends.org/))
- MySQL or MariaDB
- Modern browser (Chrome, Firefox, Edge)

### Setup

1. **Clone or Download Projo**  
   Place the `projo` folder into your server’s root directory (e.g. `htdocs` for XAMPP).

2. **Import the Database**  
   - Open phpMyAdmin or use the MySQL CLI.
   - Import the included `projo.sql` file — this sets up all required tables.

3. **Edit Database Credentials**  
   In `includes/db.php`, update your connection details:

   ```php
   $host = 'localhost';
   $db   = 'solo_pm';
   $user = 'root';
   $pass = '';
   ```

4. **Launch Projo**  
   Visit `http://localhost/projo/` in your browser.

5. **Login**  
   Default credentials:  
   - **Username:** `admin`  
   - **Password:** `password123`

---

## 🧭 How to Use

### 🔐 Authentication
- Login required to access any feature
- Change your password anytime from the Settings page

### 🖥 Dashboard
- Overview of all your project metrics, task statuses, upcoming deadlines, and data export tools

### 📁 Projects
- Add/edit/delete projects
- Track timelines in a Gantt chart with filtering options

### ✅ Tasks
- Add tasks with start/end dates, priority levels, and linked projects
- Drag and drop to reorder or change status in Kanban view

### 🗓️ Kanban & Gantt
- Visualize tasks using a flexible Kanban board and Gantt chart
- Easily update status or dependencies using interactive interfaces

### 📝 Notes
- Create quick notes and tag them by project
- Filter/search through note entries

### 🐞 Issues
- Create issues by severity (Low, Medium, High)
- Assign to projects, track status, and convert issues to tasks with one click

### 📤 Export & 📥 Import
- Export selected data types to CSV or JSON
- Import project/task data from external sources
- View logs of all data-related activities

### ⚙️ Settings
- Change password, export/import data, reset database, and view activity logs — all in one place

---

## 🛡 Security

- Passwords securely hashed with `password_hash()`
- Session-based authentication to restrict access
- CSRF protection for key actions (e.g. password change, import)
- Robust error handling for database operations

---

## 🧰 Troubleshooting

| Problem                  | Solution                                                                 |
|--------------------------|--------------------------------------------------------------------------|
| Database connection fail | Check `includes/db.php` credentials and ensure MySQL is running          |
| Login fails              | Confirm user table exists and try default credentials                   |
| Data not importing       | Check CSV/JSON file format and ensure columns match database schema      |
| Export fails             | Ensure export handler (`export.php`) is accessible and correctly written |

---

## 📄 License

This project is open-source under the [MIT License](LICENSE).

---

## 📬 Contact

Developed by **Arkadipta Kundu**  
✉️ [arkadipta.dev@gmail.com](mailto:arkadipta.dev@gmail.com)
