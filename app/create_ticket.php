<?php
require 'db_connection.php';

// Fetch partners
$stmt = $pdo->query("SELECT partner_id, partner_name FROM partners");
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch tags
$stmt = $pdo->query("SELECT tag_id, tag_name, tag_color FROM tags");
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $title = $_POST['title'];
    $description = $_POST['description'];
    $partner_id = $_POST['partner_id'];
    $selected_tags = $_POST['tags'] ?? [];

    // Insert ticket
    $stmt = $pdo->prepare("INSERT INTO tickets (title, description, partner_id) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $partner_id]);
    $ticket_id = $pdo->lastInsertId();

    // Insert ticket tags
    if (!empty($selected_tags)) {
        $stmt = $pdo->prepare("INSERT INTO ticket_tags (ticket_id, tag_id) VALUES (?, ?)");
        foreach ($selected_tags as $tag_id) {
            $stmt->execute([$ticket_id, $tag_id]);
        }
    }

    // Redirect to tickets page
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <title>Create Ticket</title>
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
    <nav class="bg-primary text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Ticket System</h1>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:text-secondary">Home</a></li>
                <li><a href="tickets.php" class="hover:text-secondary">Tickets</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mx-auto p-4 mt-4">
        <h2 class="text-3xl font-semibold mb-6">Create New Ticket</h2>
        <form action="create_ticket.php" method="POST">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50"></textarea>
            </div>
            <div class="mb-4">
                <label for="partner_id" class="block text-sm font-medium text-gray-700">Partner</label>
                <select name="partner_id" id="partner_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                    <?php foreach ($partners as $partner): ?>
                        <option value="<?= $partner['partner_id'] ?>"><?= htmlspecialchars($partner['partner_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tags</label>
                <div class="mt-2 space-x-2">
                    <?php foreach ($tags as $tag): ?>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="tags[]" value="<?= $tag['tag_id'] ?>" class="form-checkbox text-accent">
                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full" style="background-color: <?= $tag['tag_color'] ?>; color: white;">
                                <?= htmlspecialchars($tag['tag_name']) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-accent hover:bg-opacity-80 text-white font-bold py-2 px-4 rounded">
                    Create Ticket
                </button>
            </div>
        </form>
    </div>
    <script>
    document.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
        const label = checkbox.nextElementSibling;
    // Set opacity based on the initial checked state
        if (!checkbox.checked) {
            label.classList.add('opacity-50');
            }

    // Add event listener to update opacity on change
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                label.classList.remove('opacity-50');
            } else {
                label.classList.add('opacity-50');
                }
            });
        });
    </script>
</body>
</html>