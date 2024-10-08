<?php
require 'db_connection.php';
require 'fetch_tickets.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>iovox Ticket List</title>
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
    <header class="bg-primary text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-3xl font-semibold">iovox</h1>
        </div>
    </header>
    <nav class="bg-primary text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Ticket System</h1>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:text-secondary">Home</a></li>
                <li><a href="tickets.php" class="hover:text-secondary">Tickets</a></li>
                <li><a href="logout.php" class="hover:text-secondary">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mx-auto p-4 mt-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-semibold">Tickets</h2>
            <a href="create_ticket.php" class="bg-accent hover:bg-opacity-80 text-white font-bold py-2 px-4 rounded">
                Create Ticket
            </a>
        </div>
        <div class="container mx-auto p-4 mt-4">
            <!-- Output -->
            <?php foreach ($tickets as $index => $ticket) : ?>
                <div class="my-4">
                    <div class="rounded-lg shadow-md <?= $index % 2 === 0 ? 'bg-white' : 'bg-secondary bg-opacity-10' ?>">
                        <div class="p-4">
                            <h2 class="text-xl font-semibold text-primary">
                                <a href="ticket_summary.php?ticket_id=<?= $ticket['ticket_id'] ?>" class="hover:text-accent">
                                    <?= htmlspecialchars($ticket['title']) ?>
                                </a>
                            </h2>
                            <p class="text-gray-700 text-lg mt-2"><?= htmlspecialchars($ticket['description']) ?></p>
                            <ul class="mt-4">
                                <li class="mb-2">
                                    <strong>Partner:</strong> <?= htmlspecialchars($ticket['location']) ?>
                                    <span class="text-xs text-white rounded-full px-2 py-1 ml-2 <?= $ticket['location'] === 'Iovox Internal' ? 'bg-accent' : 'bg-secondary' ?>">
                                        <?= $ticket['location'] === 'Iovox Internal' ? 'Internal' : 'External' ?>
                                    </span>
                                </li>
                                <?php if (!empty($ticket['tags'])) : ?>
                                    <li class="mb-2">
                                        <strong>Tags:</strong>
                                        <?php
                                        $tags = explode(',', $ticket['tags']);
                                        foreach ($tags as $tag) {
                                            list($tagName, $tagColor) = explode('|', $tag);
                                            echo "<span class='inline-block text-xs text-white rounded-full px-2 py-1 ml-2' style='background-color: " . htmlspecialchars($tagColor) . ";'>" . htmlspecialchars($tagName) . "</span>";
                                        }
                                        ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
