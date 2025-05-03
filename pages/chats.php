<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Chats</h2>
        <div class="flex flex-col md:flex-row bg-white rounded shadow overflow-hidden" style="min-height: 500px;">
            <!-- Conversation List -->
            <div id="conversation-list" class="w-full md:w-1/3 border-r border-gray-200 p-4 overflow-y-auto">
                <input type="text" id="user-search" class="border border-gray-300 p-2 rounded w-full mb-2" placeholder="Search username...">
                <ul id="user-results" class="mb-4"></ul>
                <h3 class="text-lg font-semibold mb-2">Conversations</h3>
                <ul id="conversations"></ul>
            </div>
            <!-- Chat Box -->
            <div id="chat-panel" class="w-full md:w-2/3 p-4 hidden md:flex flex-col h-[500px]">
                <button id="back-to-list" class="mb-4 px-3 py-1 bg-gray-200 rounded md:hidden">&larr; Back</button>
                <div id="chat-header" class="font-bold text-xl mb-2"></div>
                <div id="chat-messages" class="border border-gray-300 rounded p-2 mb-2 bg-gray-50 flex-1 overflow-y-auto"></div>
                <form id="chat-form" class="flex">
                    <input type="text" id="chat-input" class="border border-gray-300 p-2 rounded flex-1" placeholder="Type a message..." autocomplete="off">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">Send</button>
                </form>
            </div>
        </div>
    </main>
    <script>
        let selectedUserId = null;
        let selectedUserName = '';
        let chatInterval = null;

        // Load conversations
        function loadConversations() {
            $.get('/projo/api/messages.php', {
                action: 'conversations'
            }, function(res) {
                if (res.success) {
                    const list = $('#conversations');
                    list.empty();
                    if (res.conversations.length === 0) {
                        list.append('<li class="text-gray-500">No conversations yet.</li>');
                    }
                    res.conversations.forEach(u => {
                        const dot = u.unread_count > 0 ?
                            '<span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2 align-middle"></span>' :
                            '';
                        list.append(`<li class="cursor-pointer hover:bg-gray-200 p-2 rounded mb-1 flex items-center" data-id="${u.id}" data-name="${u.name}">${u.name}${dot}</li>`);
                    });
                }
            });
        }

        // Search users by username
        $('#user-search').on('input', function() {
            const q = $(this).val();
            if (!q) {
                $('#user-results').empty();
                return;
            }
            $.get('/projo/api/messages.php', {
                action: 'search',
                q
            }, function(res) {
                if (res.success) {
                    $('#user-results').empty();
                    res.users.forEach(u => {
                        $('#user-results').append(`<li class="cursor-pointer hover:bg-blue-100 p-2 rounded" data-id="${u.id}" data-name="${u.name}">${u.name} <span class="text-xs text-gray-400">(@${u.username})</span></li>`);
                    });
                }
            });
        });

        // Click on search result to start chat
        $('#user-results').on('click', 'li', function() {
            selectedUserId = $(this).data('id');
            selectedUserName = $(this).data('name');
            openChat();
            $('#user-results').empty();
            $('#user-search').val('');
        });

        // Click on conversation to open chat
        $('#conversations').on('click', 'li', function() {
            selectedUserId = $(this).data('id');
            selectedUserName = $(this).data('name');
            openChat();
        });

        // Open chat panel
        function openChat() {
            $('#chat-header').text(selectedUserName);
            $('#chat-panel').removeClass('hidden');
            if ($(window).width() < 768) {
                $('#conversation-list').hide();
            }
            loadMessages();
            if (chatInterval) clearInterval(chatInterval);
            chatInterval = setInterval(loadMessages, 2000);
        }

        // Back to conversation list (mobile)
        $('#back-to-list').on('click', function() {
            $('#chat-panel').addClass('hidden');
            $('#conversation-list').show();
            if (chatInterval) clearInterval(chatInterval);
        });

        // Load messages
        function loadMessages() {
            if (!selectedUserId) return;
            $.get('/projo/api/messages.php', {
                action: 'fetch',
                user_id: selectedUserId
            }, function(res) {
                if (res.success) {
                    const chat = $('#chat-messages');
                    chat.empty();
                    res.messages.forEach(m => {
                        const align = m.sender_id == <?= (int)$_SESSION['user_id'] ?> ? 'text-right' : 'text-left';
                        chat.append(`<div class="${align} mb-1"><span class="inline-block bg-blue-100 px-2 py-1 rounded">${m.sender_name}: ${m.message}</span><br><span class="text-xs text-gray-400">${m.created_at}</span></div>`);
                    });
                    chat.scrollTop(chat[0].scrollHeight);
                }
            });
        }

        // Send message
        $('#chat-form').on('submit', function(e) {
            e.preventDefault();
            const msg = $('#chat-input').val();
            if (!msg.trim() || !selectedUserId) return;
            $.ajax({
                url: '/projo/api/messages.php?action=send',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    receiver_id: selectedUserId,
                    message: msg
                }),
                success: function(res) {
                    if (res.success) {
                        $('#chat-input').val('');
                        loadMessages();
                        loadConversations();
                    }
                }
            });
        });

        // Initial load
        $(function() {
            loadConversations();
            // Responsive: show chat panel if wide
            if ($(window).width() >= 768) {
                $('#chat-panel').removeClass('hidden');
            }
        });
    </script>
    <script>
        // Toggle dark mode
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;

        // Load saved theme from localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark');
            if (themeToggle) themeToggle.textContent = 'Light Mode';
        }

        // Toggle theme on button click
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                body.classList.toggle('dark');
                const isDark = body.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                themeToggle.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            });
        }
    </script>
</body>

</html>