# ![Projo Icon](assets/images/favicon.ico) Projo — Solo Project Manager

**Projo** is a modern, self-hosted project management system tailored for solo developers and small teams. It helps you manage tasks, projects, notes, and issues with a clean UI and a growing set of powerful features — including a Kanban board, Gantt chart, Calendar view, timers, issue tracking, import/export tools, and more.

---

## 🚀 Features

- 🔐 **User Authentication** — Secure login/logout system with hashed passwords and change-password option.
- 📁 **Project Management** — Create, edit, delete, and track project timelines via Gantt view and color tags.
- ✅ **Task Management** — Add tasks with priority, due dates, and real-time status tracking.
- 📊 **Kanban Board** — Drag-and-drop interface with improved task status updates and visual placeholders.
- 🗓️ **Gantt Chart** — Visualize project schedules with a today marker and flexible zoom options.
- 📆 **Calendar View** — See all task deadlines in a monthly view, color-coded by priority (High/Medium/Low).
- ⏱️ **Task Timers** — Track time spent on individual tasks with start, stop, and reset functionality.
- 📝 **Notes** — Create, search, and link notes to specific projects.
- 🐞 **Issue Tracker** — Log issues by severity and status, and convert issues to tasks instantly.
- 📤 **Data Export** — Export projects, tasks, notes, and issues to CSV or JSON.
- 📥 **Data Import** — Upload structured data in JSON or CSV format for quick population.
- 💾 **Database Backup** — Download your full MySQL `.sql` backup instantly.
- ♻️ **Reset Application** — Clear all project data and restore default state when needed.
- 🌙 **Dark Mode** — Switch to dark mode for a more comfortable visual experience.
- 📚 **Activity Logs** — Track user activity like exports, resets, logins, and password changes.
- 📚 **Error Handling Page** — Better error handling for errors like db connection fails or illigal sql and similer by .

---

## ⚙️ Installation

### Requirements

- A PHP-compatible web server (e.g. [XAMPP](https://www.apachefriends.org/))
- MySQL or MariaDB
- A modern web browser (Chrome, Firefox, Edge)

### Setup Instructions

1. **Download or Clone Projo**  
   - Download the `.zip` from the [latest release](https://github.com/YourUsername/Projo/releases) or clone the repo.
   - Place the extracted `projo/` folder in your server’s root directory:
     - Windows: `C:\xampp\htdocs\projo`
     - Linux: `/opt/lampp/htdocs/projo`

2. **Start Apache and MySQL** via XAMPP Control Panel.

3. **Run the Auto Setup Script**  
   - Open your browser and visit:  
     `http://localhost/projo/setup.php`  
   - This creates the database, tables, and inserts default data.

4. **Manual Setup (If Needed)**  
   - Open `http://localhost/phpmyadmin/`
   - Create a new database named `projo`
   - Import `projo.sql` from the project root
   - Update your credentials in `includes/db.php`:
     ```php
     $host = 'localhost';
     $db   = 'projo';
     $user = 'root';
     $pass = '';
     ```

---

## 🔐 Default Login

```txt
Username: admin
Password: password123
```

> You can change this in **Settings → Change Password**

---

## 🧭 How to Use Projo

### 🔐 Login & Settings
- Login is required for access.
- Change password, reset app, or manage backups from the **Settings** page.

### 🖥 Dashboard
- View total projects, tasks, completed, overdue, pending, and issues.
- See upcoming tasks (due today/tomorrow) and total time spent.

### 📁 Projects
- Add/edit/delete projects with color tagging.
- Track timelines in the Gantt chart.

### ✅ Tasks
- Add tasks with priority, due date, and project assignment.
- Use timers to log time spent.
- Drag-and-drop between statuses in Kanban view.

### 📊 Kanban Board
- Reorder and update tasks visually.
- Visual feedback and placeholders for empty columns.

### 🗓️ Gantt Chart
- Manage project timelines with drag-to-reschedule (future feature-ready).
- Day/week/month view support.

### 📆 Calendar View
- Monthly view of all tasks by due date.
- Tasks color-coded by priority for easy scanning.

### ⏱ Task Timer
- Each task has a built-in timer (Start/Stop/Reset).
- Time is auto-saved and totaled in dashboard stats.

### 📝 Notes
- Create searchable notes, linked to specific projects if needed.

### 🐞 Issues
- Report and manage bugs or issues.
- Assign severity and status; convert to tasks in one click.

### 📤 Export / 📥 Import
- Export your data as CSV/JSON.
- Import structured files to quickly seed the database.

---

## 🛡 Security

- Passwords are hashed using `password_hash()`
- Session-based authentication
- CSRF tokens for secure form submissions
- Error handling to prevent exposure of sensitive data

---

## 🧰 Troubleshooting

| Problem                  | Solution                                                                 |
|--------------------------|--------------------------------------------------------------------------|
| Apache/MySQL won't start | Close other apps using port 80/3306 or change ports in XAMPP settings   |
| Database error           | Check `db.php` and verify MySQL is running and credentials are correct  |
| Login fails              | Try default credentials or create a new user via SQL                    |
| Export/import not working| Ensure files are readable and server folder is writable                 |
| `setup.php` not working  | Import `projo.sql` manually via phpMyAdmin                              |

---

## 📄 License

Licensed under the [MIT License](LICENSE)

---

## ⚠️ Disclaimer

Projo is actively maintained.  
- For **latest features**, clone or download the ZIP from the main branch.  
- For **stable builds**, use the ZIP from [GitHub Releases](https://github.com/YourUsername/Projo/releases).

---

## 📬 Contact

Developed by **Arkadipta Kundu**  
📧 [arkadipta.dev@gmail.com](mailto:arkadipta.dev@gmail.com)  
🌐 [github.com/Arkadipta-Kundu](https://github.com/Arkadipta-Kundu)
