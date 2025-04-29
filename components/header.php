<header class="bg-white text-gray-800 py-4">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Replace the text with the logo -->
        <a href="/projo/index.php">
            <img src="/projo/assets/images/logo.png" alt="Projo Logo" class="h-10">
        </a>
        <nav class="navbar space-x-4">
            <a href="/projo/index.php">Home</a>
            <a href="/projo/pages/dashboard.php">Dashboard</a>
            <a href="/projo/pages/projects.php">Projects</a>
            <a href="/projo/pages/tasks.php">Tasks</a>
            <a href="/projo/pages/kanban.php">Kanban</a>
            <a href="/projo/pages/notes.php">Notes</a>
            <a href="/projo/pages/issues.php">Issues</a>
            <a href="/projo/logout.php" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
        </nav>
        <form method="GET" action="/projo/search.php" class="ml-4 flex items-center space-x-2">
            <input type="text" name="q" placeholder="Search..." class="px-4 py-2 rounded bg-gray-100 text-gray-800 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</header>