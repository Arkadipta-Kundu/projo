<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: pages/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Projo — No-BS Project Manager</title>
    <link rel="icon" type="image/x-icon" href="assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add this inside the <head> tag for extra mobile tweaks -->
    <style>
        /* Responsive tweaks for landing page */
        @media (max-width: 768px) {
            .container {
                padding: 1rem !important;
            }

            section.container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            .rounded-3xl,
            .rounded-xl {
                border-radius: 1rem !important;
            }

            .flex.flex-col.md\:flex-row {
                flex-direction: column !important;
                gap: 1.5rem !important;
            }

            .grid-cols-3,
            .md\:grid-cols-3 {
                grid-template-columns: 1fr !important;
            }

            .grid-cols-2,
            .md\:grid-cols-2 {
                grid-template-columns: 1fr !important;
            }

            .gap-10,
            .gap-8 {
                gap: 1.5rem !important;
            }

            .py-20 {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }

            .px-6 {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            .max-w-2xl,
            .max-w-3xl,
            .max-w-md {
                max-width: 100% !important;
            }

            .text-3xl,
            .text-4xl {
                font-size: 1.5rem !important;
            }

            .text-2xl {
                font-size: 1.2rem !important;
            }

            .text-xl {
                font-size: 1rem !important;
            }

            .py-4,
            .py-6,
            .py-8 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            .px-4,
            .px-8,
            .px-10 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .mb-4,
            .mb-6,
            .mb-8,
            .mb-10 {
                margin-bottom: 1rem !important;
            }

            .mt-10,
            .mt-8,
            .mt-4 {
                margin-top: 1rem !important;
            }

            .rounded-xl,
            .rounded-3xl {
                border-radius: 0.75rem !important;
            }

            .shadow-xl,
            .shadow-2xl {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
            }

            .flex.space-x-4 {
                flex-direction: column !important;
                gap: 0.75rem !important;
            }

            .w-full {
                width: 100% !important;
            }

            .overflow-x-auto table {
                min-width: 600px;
            }

            /* Hide decorative blobs on mobile */
            .absolute.-top-24,
            .absolute.-bottom-24 {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen flex flex-col font-sans">

    <!-- Hero Section (No Header, No Floating Logo) -->
    <main class="flex-1">
        <section class="container mx-auto px-6 py-20 flex flex-col items-center text-center relative overflow-hidden">
            <!-- Decorative background shapes -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-100 rounded-full opacity-40 blur-2xl z-0"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-200 rounded-full opacity-30 blur-2xl z-0"></div>
            <div class="relative z-10 w-full flex flex-col items-center">
                <span
                    class="inline-block bg-gradient-to-r from-blue-400 to-blue-600 text-white px-6 py-2 rounded-full text-lg font-semibold shadow mb-4 animate-fade-in">Welcome
                    to</span>
                <img src="assets/images/logo.png" alt="Projo Logo"
                    class="h-8 md:h-16 mb-4 animate-slide-down drop-shadow-lg">
                <p class="mt-2 text-2xl md:text-3xl text-gray-700 max-w-2xl mx-auto font-medium animate-fade-in-slow">
                    <span class="font-bold text-blue-700 text-3xl block mb-2">Built for devs who ship.</span>
                    Projo gives you everything you need to manage your work — no clutter, no confusion.<br>
                    <span class="text-blue-600 font-semibold">Simple, fast, and private project management for solo devs
                        and small teams.</span>
                </p>
                <div
                    class="flex flex-col md:flex-row items-center justify-center gap-6 mt-10 mb-8 animate-fade-in-slow">
                    <a href="login.php"
                        class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-10 py-4 rounded-xl font-bold shadow-xl hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition text-xl">Sign
                        In</a>
                    <a href="register.php"
                        class="bg-white border-2 border-blue-600 text-blue-700 px-10 py-4 rounded-xl font-bold shadow hover:bg-blue-50 hover:scale-105 transition text-xl">Create
                        a Free Account</a>
                </div>
                <div class="flex items-center justify-center gap-4 text-gray-500 text-lg mb-8 animate-fade-in">
                    <span>🔒 Privacy-Focused</span>
                    <span>•</span>
                    <span>🌙 Dark Mode Ready</span>
                    <span>•</span>
                    <span>💾 Free & Open Source</span>
                </div>
                <div class="w-full flex justify-center animate-fade-in-slow">
                    <img src="assets/images/landing-preview.png" alt="Projo App Preview"
                        class="rounded-3xl shadow-2xl border-4 border-blue-100 max-w-2xl w-full hover:shadow-blue-200 transition duration-300">
                </div>
            </div>
        </section>

        <!-- Feature Highlights Section -->
        <section class="bg-white py-20">
            <div class="container mx-auto px-6">
                <h2 class="text-4xl font-bold text-blue-700 mb-6 text-center">Finally, a Project Manager That Doesn’t
                    Get in Your Way</h2>
                <p class="text-lg text-gray-600 mb-10 text-center max-w-2xl mx-auto">
                    Stop wrestling with tools made for corporations. Projo is made for builders — clean, simple, and
                    powerful enough to keep your work on track.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="bg-blue-50 rounded-xl p-8 shadow hover:shadow-lg transition">
                        <div class="text-5xl mb-4">🛠️</div>
                        <h3 class="text-2xl font-semibold mb-2 text-blue-800">Built for Real Work</h3>
                        <p class="text-gray-600">Manage tasks and projects without the fluff. Deadlines, priorities,
                            tags — that’s it.</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-8 shadow hover:shadow-lg transition">
                        <div class="text-5xl mb-4">👀</div>
                        <h3 class="text-2xl font-semibold mb-2 text-blue-800">See It All Clearly</h3>
                        <p class="text-gray-600">Kanban, Gantt, and calendar views make it easy to understand progress
                            at a glance.</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-8 shadow hover:shadow-lg transition">
                        <div class="text-5xl mb-4">🔒</div>
                        <h3 class="text-2xl font-semibold mb-2 text-blue-800">No Data Games</h3>
                        <p class="text-gray-600">No tracking, no subscriptions. Just your projects — private and yours.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Detailed Features Section -->
        <section class="container mx-auto px-6 py-20">
            <h2 class="text-3xl font-bold text-blue-700 mb-4 text-center">Everything You Need. Nothing You Don’t.</h2>
            <p class="text-lg text-gray-600 mb-10 text-center max-w-2xl mx-auto">
                Projo is streamlined, but powerful. Every feature here helps you get real work done — whether you're
                solo or working with a small team.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div>
                    <ul class="space-y-5 text-lg text-gray-700">
                        <li>✅ <strong>Task Management, Notes, Issue Tracking</strong></li>
                        <li>📅 <strong>Calendar View, Kanban, Gantt Chart</strong></li>
                        <li>⏱️ <strong>Built-in Timer, Time Logs</strong></li>
                        <li>💬 <strong>Chat with Solo Devs & Team Members</strong></li>
                        <li>💾 <strong>Export & Backup (CSV/JSON, MySQL)</strong></li>
                        <li>🛡️ <strong>Privacy, Auth, and Dark Mode</strong></li>
                    </ul>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/landing-preview.png" alt="Projo Preview"
                        class="rounded-xl shadow-xl border-2 border-blue-100 mb-6 w-full max-w-md">
                    <div class="flex space-x-4">
                        <a href="login.php"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold shadow hover:bg-blue-700 transition text-lg">Get
                            Started</a>
                        <a href="https://github.com/Arkadipta-Kundu/Projo" target="_blank"
                            class="bg-gray-100 border border-blue-600 text-blue-700 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition text-lg">GitHub</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comparison Section -->
        <section class="container mx-auto px-6 py-20">
            <h2 class="text-3xl font-bold text-blue-700 mb-8 text-center">Why Projo Over Other Tools?</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-xl shadow mb-6 text-center">
                    <thead>
                        <tr class="bg-blue-50">
                            <th class="py-3 px-4 font-semibold">Feature</th>
                            <th class="py-3 px-4 font-semibold">Projo</th>
                            <th class="py-3 px-4 font-semibold">Other Project Managers</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr>
                            <td class="py-2 px-4">Made for devs & small teams</td>
                            <td class="py-2 px-4">✅</td>
                            <td class="py-2 px-4">❌ Often made for enterprise use</td>
                        </tr>
                        <tr class="bg-blue-50">
                            <td class="py-2 px-4">Simple, clean UI</td>
                            <td class="py-2 px-4">✅</td>
                            <td class="py-2 px-4">❌ Feature overload</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4">No subscription needed</td>
                            <td class="py-2 px-4">✅</td>
                            <td class="py-2 px-4">❌ Paid tiers for basics</td>
                        </tr>
                        <tr class="bg-blue-50">
                            <td class="py-2 px-4">Self-host option</td>
                            <td class="py-2 px-4">✅</td>
                            <td class="py-2 px-4">❌ Locked-in ecosystem</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4">Privacy-focused</td>
                            <td class="py-2 px-4">✅</td>
                            <td class="py-2 px-4">❌ Collect user data</td>
                        </tr>
                        <tr class="bg-blue-50">
                            <td class="py-2 px-4">Fast, no learning curve</td>
                            <td class="py-2 px-4">✅</td>
                            <td class="py-2 px-4">❌ Complex setup</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-center text-lg text-gray-600 max-w-2xl mx-auto">
                You shouldn’t need a team just to manage your projects. With Projo, you can stay focused on building.
            </p>
        </section>

        <!-- How It Works Section -->
        <section class="bg-gradient-to-r from-blue-100 to-blue-200 py-20">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-blue-700 mb-8 text-center">How It Works — In Three Simple Steps</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
                    <div class="p-8 bg-white rounded-xl shadow">
                        <div class="text-4xl mb-3">📝</div>
                        <h4 class="font-bold text-xl mb-2">1. Sign Up in Seconds</h4>
                        <p class="text-gray-600">No email required.</p>
                    </div>
                    <div class="p-8 bg-white rounded-xl shadow">
                        <div class="text-4xl mb-3">📁</div>
                        <h4 class="font-bold text-xl mb-2">2. Add Your Projects</h4>
                        <p class="text-gray-600">Break them into tasks, set priorities and timelines.</p>
                    </div>
                    <div class="p-8 bg-white rounded-xl shadow">
                        <div class="text-4xl mb-3">🚀</div>
                        <h4 class="font-bold text-xl mb-2">3. Stay on Track</h4>
                        <p class="text-gray-600">Use Kanban, Gantt, or Calendar to keep moving forward.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Compact Testimonials Section -->
        <section class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <blockquote class="text-xl italic text-gray-700 border-l-4 border-blue-400 pl-6 py-4 bg-blue-50 rounded">
                    “Switched from Jira and Trello — Projo is refreshingly simple and does <em>exactly</em> what my 4-person team needs.”
                    <span class="block mt-2 text-base font-semibold text-blue-700">— Lena M., Frontend Developer</span>
                </blockquote>
                <blockquote class="text-xl italic text-gray-700 border-l-4 border-blue-400 pl-6 py-4 bg-blue-50 rounded">
                    “I just wanted a lightweight, private task board with real features. Projo nails it without all the bloat.”
                    <span class="block mt-2 text-base font-semibold text-blue-700">— Kofi D., Indie Hacker</span>
                </blockquote>
                <blockquote class="text-xl italic text-gray-700 border-l-4 border-blue-400 pl-6 py-4 bg-blue-50 rounded">
                    “I manage 3 client projects with Projo. Clean UI, solid features, no distractions. Perfect for solo devs.”
                    <span class="block mt-2 text-base font-semibold text-blue-700">— Priya S., Freelance Developer</span>
                </blockquote>
            </div>
        </section>


        <!-- FAQ / Info -->
        <section class="container mx-auto px-6 py-20">
            <h2 class="text-3xl font-bold text-blue-700 mb-8 text-center">Frequently Asked Questions</h2>
            <div class="max-w-3xl mx-auto space-y-8">
                <div>
                    <h4 class="font-semibold text-lg text-blue-800 mb-2">Is Projo really free?</h4>
                    <p class="text-gray-700">Yes! Projo is open source and free to use. You can use it here, or host it
                        yourself from our GitHub if you prefer full control.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-lg text-blue-800 mb-2">Do I need to be online to use Projo?</h4>
                    <p class="text-gray-700">This version is web-based and requires an internet connection. If you want
                        an offline/self-hosted version, check out our <a href="https://github.com/Arkadipta-Kundu/Projo"
                            target="_blank" class="text-blue-600 underline hover:text-blue-800">GitHub</a> for
                        instructions.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-lg text-blue-800 mb-2">Is my data private?</h4>
                    <p class="text-gray-700">Absolutely. We do not share or sell your data. For maximum privacy, you can
                        self-host Projo on your own server.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-lg text-blue-800 mb-2">How do I get started?</h4>
                    <p class="text-gray-700">Just <a href="register.php"
                            class="text-blue-600 underline hover:text-blue-800">create a free account</a> and start your
                        first project! To self-host, visit our <a href="https://github.com/Arkadipta-Kundu/Projo"
                            target="_blank" class="text-blue-600 underline hover:text-blue-800">GitHub</a> for setup
                        instructions.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-lg text-blue-800 mb-2">Is Projo good for teams?</h4>
                    <p class="text-gray-700">Yes — it's perfect for solo devs and teams of up to 5–6 people. Everything
                        stays clear and manageable.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-10">
        <div class="container mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between text-gray-500">
            <div>
                &copy;
                <?= date('Y') ?> <span class="font-bold text-blue-700">Projo</span> — Solo Project Manager.
                Made with <span class="text-red-400">&hearts;</span>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-4">
                <a href="https://github.com/Arkadipta-Kundu/Projo" target="_blank"
                    class="hover:text-blue-700">GitHub</a>
                <a href="mailto:arkadipta.dev@gmail.com" class="hover:text-blue-700">Contact</a>
            </div>
        </div>
    </footer>

    <style>
        .animate-bounce-slow {
            animation: bounce 2.5s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 1.2s ease;
        }

        .animate-fade-in-slow {
            animation: fadeIn 2s ease;
        }

        .animate-slide-down {
            animation: slideDown 1.2s cubic-bezier(.23, 1.01, .32, 1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>

</html>