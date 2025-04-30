# ![Projo Icon](assets/images/favicon.ico) Projo — Solo Project Manager

**Projo** is a lightweight, self-hosted project management tool built for individuals and small teams. It helps you manage projects, tasks, notes, and issues efficiently with a clean UI and essential productivity features — including a visual Kanban board and data export options.

---

## 🚀 Features

- 🔐 **User Authentication** — Secure login/logout system to protect access.
- 📁 **Project Management** — Create, update, delete, and track projects.
- ✅ **Task Management** — Organize tasks with priorities, statuses, and project assignments.
- 📊 **Kanban Board** — Drag-and-drop tasks across “To Do”, “In Progress”, and “Done”.
- 📝 **Notes** — Add quick notes and optionally tag them to specific projects.
- 🐞 **Issue Tracking** — Log issues with severity/status and convert them to actionable tasks.
- 📤 **Data Export** — Export data as CSV or JSON for backup or integration.

---

## ⚙️ Installation

### Requirements

- PHP-compatible web server (e.g. [XAMPP](https://www.apachefriends.org/))
- MySQL or MariaDB
- Modern web browser (Chrome, Firefox, etc.)

### Setup Steps

1. **Download/Clone the Repository**  
   Place the `projo` folder inside your web server’s root directory (e.g. `C:/xampp/htdocs/`).

2. **Import the Database**

   - Open phpMyAdmin or use the MySQL CLI.
   - Import the included `projo.sql` file. It creates the following tables:
     - `users`, `projects`, `tasks`, `notes`, `issues`

3. **Configure Database Credentials**  
    Open `includes/db.php` and update your connection settings:

   ```php
   $host = 'localhost';
   $db   = 'solo_pm';
   $user = 'root';
   $pass = '';
   ```

4. **Start Your Server**  
   Launch your web server and visit:  
   `http://localhost/projo/`

5. **Login**  
   Use the default credentials:
   - **Username**: `admin`
   - **Password**: `password123`

---

## 🧭 How to Use

### 1. Login

Navigate to `login.php`, enter your credentials, and you’ll be redirected to the dashboard.

### 2. Dashboard

- View project/task counts and pending items
- See upcoming deadlines
- Export data directly from the dashboard

### 3. Projects

- Add a new project (title, description, deadline)
- Edit or delete projects via the action buttons

### 4. Tasks

- Create tasks with due dates, priorities, and statuses
- Assign tasks to projects
- Mark tasks complete using checkboxes
- Edit/delete tasks anytime

### 5. Kanban Board

- Drag-and-drop tasks between columns
- Automatically updates task statuses visually

### 6. Notes

- Add simple notes for ideas or planning
- Optionally tag notes to projects
- Edit and delete notes

### 7. Issues

- Log bugs or problems with severity levels (Low, Medium, High)
- Assign issues to projects if needed
- Convert issues to tasks in one click
- Full edit/delete support

### 8. Export Data

- Export projects, tasks, notes, or issues
- Select CSV or JSON format
- Use the “Export” section on the dashboard

### 9. Logout

- Use the Logout button in the navigation to securely end your session

---

## 🔐 Security Highlights

- Passwords stored securely with `password_hash()`
- Authenticated session-based access
- Simple access control per login session

---

## 🛠️ Troubleshooting

| Issue                     | Solution                                                    |
| ------------------------- | ----------------------------------------------------------- |
| Database connection error | Check your `db.php` credentials and ensure MySQL is running |
| Login not working         | Verify the `users` table exists and try default credentials |
| Export not downloading    | Ensure `export.php` is correctly implemented and accessible |

---

## 🛣️ Roadmap / Future Ideas

- User roles and permissions (admin, editor, viewer)
- Email notifications for task deadlines or issue updates
- File attachments for tasks or issues
- Calendar view for tasks

---

## 📄 License

This project is open-source and free to use under the [MIT License](LICENSE).

---

## 📬 Contact

For questions or feedback, reach out:  
**Arkadipta Kundu** — [arkadipta.dev@gmail.com](mailto:arkadipta.dev@gmail.com)

