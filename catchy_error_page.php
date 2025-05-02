<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Connection Error</title>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            color: #333;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .error-container {
            text-align: center;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        .error-container h1 {
            font-size: 2.5rem;
            color: #e3342f;
            animation: bounce 1.5s infinite;
        }

        .error-container p {
            margin: 1rem 0;
            font-size: 1.2rem;
            color: #555;
        }

        .error-container button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            margin: 0.5rem;
            transition: background-color 0.3s ease-in-out, transform 0.2s;
        }

        .error-container button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .error-details {
            margin-top: 1rem;
            color: #555;
            display: none;
            font-size: 0.9rem;
        }

        .error-gif {
            width: 200px;
            margin: 1rem auto;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <img src="https://media.giphy.com/media/3o7abKhOpu0NwenH3O/giphy.gif" alt="Error GIF" class="error-gif">
        <h1>Don't worry we will right back !</h1>
        <p>It seems like your XAMPP or MySQL server is not running. Please check and try again.</p>
        <button id="retry-button">Retry</button>
        <button id="show-error-button">Show Error Details</button>
        <p class="error-details" id="error-details"></p>
    </div>

    <script>
        function checkConnection() {
            $.get('/projo/connection_check.php', function(response) {
                if (response.success) {
                    window.location.href = '/projo/index.php'; // Redirect to the main page
                } else {
                    $('#error-details').text(response.error);
                }
            });
        }

        // Retry button click
        $('#retry-button').on('click', function() {
            checkConnection();
        });

        // Show error details button click
        $('#show-error-button').on('click', function() {
            $('#error-details').toggle();
        });

        // Automatically retry every 5 seconds
        setInterval(checkConnection, 5000);
    </script>
</body>

</html>