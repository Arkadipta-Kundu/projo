<header class="bg-white text-gray-800 shadow py-4">
  <div class="container mx-auto flex items-center relative px-4">
    <!-- Mobile Hamburger -->
    <button id="mobile-menu-toggle" class="md:hidden flex items-center focus:outline-none">
      <svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <!-- Logo -->
    <a href="/index.php"
       class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 md:static md:transform-none md:ml-0 flex-shrink-0">
      <img src="/assets/images/logo.png" alt="Projo Logo" class="h-10" />
    </a>

    <!-- Desktop Nav (centered) -->
    <nav class="hidden md:flex flex-1 justify-center space-x-8">
      <a href="/pages/dashboard.php"
         class="py-2 hover:text-blue-700 <?= ($_SERVER['REQUEST_URI']==='/pages/dashboard.php') ? 'text-blue-700 border-b-2 border-blue-700' : '' ?>">
        Dashboard
      </a>
      <a href="/pages/projects.php"
         class="py-2 hover:text-blue-700 <?= ($_SERVER['REQUEST_URI']==='/pages/projects.php') ? 'text-blue-700 border-b-2 border-blue-700' : '' ?>">
        Projects
      </a>
      <a href="/pages/tasks.php"
         class="py-2 hover:text-blue-700 <?= ($_SERVER['REQUEST_URI']==='/pages/tasks.php') ? 'text-blue-700 border-b-2 border-blue-700' : '' ?>">
        Tasks
      </a>
      <a href="/pages/kanban.php"
         class="py-2 hover:text-blue-700 <?= ($_SERVER['REQUEST_URI']==='/pages/kanban.php') ? 'text-blue-700 border-b-2 border-blue-700' : '' ?>">
        Kanban
      </a>
      <a href="/pages/gantt.php"
         class="py-2 hover:text-blue-700 <?= ($_SERVER['REQUEST_URI']==='/pages/gantt.php') ? 'text-blue-700 border-b-2 border-blue-700' : '' ?>">
        Gantt
      </a>
      <a href="/pages/notes.php"
         class="py-2 hover:text-blue-700 <?= ($_SERVER['REQUEST_URI']==='/pages/notes.php') ? 'text-blue-700 border-b-2 border-blue-700' : '' ?>">
        Notes
      </a>
      <a href="/pages/issues.php"
         class="py-2 hover:text-blue-700 <?= ($_SERVER['REQUEST_URI']==='/pages/issues.php') ? 'text-blue-700 border-b-2 border-blue-700' : '' ?>">
        Issues
      </a>
      <a href="/pages/settings.php"
         class="py-2 hover:text-blue-700 <?= ($_SERVER['REQUEST_URI']==='/pages/settings.php') ? 'text-blue-700 border-b-2 border-blue-700' : '' ?>">
        Settings
      </a>
    </nav>

    <!-- Search (desktop only) -->
    <form method="GET" action="/search.php"
          class="hidden md:flex items-center space-x-2 ml-auto">
      <input type="text" name="q" placeholder="Search..."
             class="px-4 py-2 rounded bg-gray-100 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      <button type="submit" class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600">
        <i class="fas fa-search"></i>
      </button>
    </form>
  </div>

 <!-- Mobile Overlay & Sidebar -->
<div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden"></div>
<nav id="mobile-menu"
     class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg z-50 transform -translate-x-full transition-transform duration-200 ease-in-out md:hidden flex flex-col pt-8 overflow-y-auto">
  <div class="flex items-center justify-between px-4 mb-4">
    <div></div>
    <button id="mobile-menu-close" class="text-gray-500 hover:text-blue-700 text-2xl">&times;</button>
  </div>

 <form method="GET" action="/search.php" class="flex items-center justify-center px-4 py-3">
  <div class="flex items-center space-x-2">
    <input type="text" name="q" placeholder="Search..."
           class="w-36 px-3 py-1.5 rounded bg-gray-100 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
    <button type="submit"
            class="w-9 h-9 flex items-center justify-center bg-blue-500 rounded text-white hover:bg-blue-600">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
      </svg>
    </button>
  </div>
</form>



  <!-- Mobile Links -->
  <div class="flex flex-col space-y-2 px-4">
    <?php
    $links = [
      '/pages/dashboard.php' => 'Dashboard',
      '/pages/projects.php'  => 'Projects',
      '/pages/tasks.php'     => 'Tasks',
      '/pages/kanban.php'    => 'Kanban',
      '/pages/gantt.php'     => 'Gantt',
      '/pages/notes.php'     => 'Notes',
      '/pages/issues.php'    => 'Issues',
      '/pages/settings.php'  => 'Settings',
    ];
    foreach ($links as $uri => $label): ?>
      <a href="<?= $uri ?>"
         class="py-2 border-b <?= ($_SERVER['REQUEST_URI']===$uri) ? 'text-blue-700 font-semibold' : 'text-gray-800 hover:text-blue-700' ?>">
        <?= $label ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>


  <script>
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const menu       = document.getElementById('mobile-menu');
    const menuClose  = document.getElementById('mobile-menu-close');
    const overlay    = document.getElementById('mobile-menu-overlay');

    function openMenu() {
      menu.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    }

    function closeMenu() {
      menu.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    }

    menuToggle.addEventListener('click', openMenu);
    menuClose.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);
  </script>
</header>
