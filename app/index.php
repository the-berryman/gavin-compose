<?php
// Include the connection and fetching logic
require 'db_connection.php';
require 'fetch_tickets.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Ticket Listings</title>
</head>

<body class="bg-gray-100">
    <header class="bg-blue-500 text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-3xl font-semibold">Ticket Listings</h1>
        </div>
    </header>
    <div class="container mx-auto p-4 mt-4">
        <div class="bg-green-100 rounded-lg shadow-md p-6 my-6">
            <h2 class="text-2xl font-semibold mb-4">Tickets:</h2>
        </div>
        <!-- Output -->
        <?php foreach ($tickets as $index => $ticket) : ?>
            <div class="md my-4">
                <div class="rounded-lg shadow-md <?= $index % 2 === 0 ? 'bg-blue-100' : 'bg-white' ?>">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold"><?= htmlspecialchars($ticket['title']) ?></h2>
                        <p class="text-gray-700 text-lg mt-2"><?= htmlspecialchars($ticket['description']) ?></p>
                        <ul class="mt-4">
                            <li class="mb-2">
                                <strong>Location:</strong> <?= htmlspecialchars($ticket['location']) ?>
                                <span class="text-xs text-white <?= $ticket['location'] === 'Iovox Internal' ? 'bg-red-500' : 'bg-green-500'; ?> rounded-full px-2 py-1 ml-2"><?= $ticket['location'] === 'Iovox Internal' ? 'Internal' : 'External'; ?></span>
                            </li>
                            <?php if (!empty($ticket['tags'])) : ?>
                                <li class="mb-2">
                                    <strong>Tags:</strong> 
                                    <?php 
                                    $tags = explode(',', $ticket['tags']);
                                    foreach ($tags as $tag) {
                                        list($tagName, $tagColor) = explode('|', $tag);
                                        echo "<span class='inline-block text-white rounded-full px-2 py-1 mr-2' style='background-color: $tagColor;'>$tagName</span>";
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
</body>

</html>
