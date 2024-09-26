<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert user into the database
    $sql = "INSERT INTO users (username, email, password, status) VALUES (:username, :email, :password, 1)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['username' => $username, 'email' => $email, 'password' => $password])) {
        header("Location: login.php?register=success");
        exit();
    } else {
        echo "Error: Could not create account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Register - iovox Ticket System</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#131C41',
                        secondary: '#A5B2E2',
                        accent: '#906AE2',
                        background: '#F0F1F7',
                        text: '#333335',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-text">
    <!-- Header -->
    <header class="bg-primary text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-3xl font-semibold">iovox</h1>
        </div>
    </header>

    <!-- Nav Bar -->
    <nav class="bg-primary text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Ticket System</h1>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:text-secondary">Home</a></li>
                <li><a href="tickets.php" class="hover:text-secondary">Tickets</a></li>
                <li><a href="login.php" class="hover:text-secondary">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Registration Form -->
    <div class="container mx-auto p-4 mt-8 max-w-md">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-primary mb-4">Register</h2>
            <form method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700">Username:</label>
                    <input type="text" name="username" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email:</label>
                    <input type="email" name="email" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password:</label>
                    <input type="password" name="password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                </div>
                <button type="submit" class="bg-accent hover:bg-opacity-80 text-white font-bold py-2 px-4 rounded w-full">
                    Register
                </button>
            </form>
        </div>
    </div>
</body>
</html>
