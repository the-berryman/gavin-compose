<?php
require 'db_connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the ticket ID from the URL
$ticket_id = $_GET['ticket_id'];

// Fetch ticket details
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all users for assignment dropdown
$stmt = $pdo->query("SELECT user_id, username FROM users WHERE status = 1");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch active tags
$stmt = $pdo->query("SELECT tag_id, tag_name, tag_color FROM tags WHERE status = 1");
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch assigned tags for the ticket
$stmt = $pdo->prepare("SELECT tag_id FROM ticket_tags WHERE ticket_id = ? AND status = 1");
$stmt->execute([$ticket_id]);
$assigned_tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment'])) {
        $comment = $_POST['comment'];
        $stmt = $pdo->prepare("UPDATE tickets SET comments = ?, comment_timestamp = NOW() WHERE ticket_id = ?");
        $stmt->execute([$comment, $ticket_id]);
    }

    if (isset($_POST['assigned_to'])) {
        $assigned_to = $_POST['assigned_to'];
        $stmt = $pdo->prepare("UPDATE tickets SET assigned_to = ?, assigned_timestamp = NOW() WHERE ticket_id = ?");
        $stmt->execute([$assigned_to, $ticket_id]);

        // Insert into assignment history
        $stmt = $pdo->prepare("INSERT INTO ticket_assignment_history (ticket_id, assigned_by, assigned_to) VALUES (?, ?, ?)");
        $stmt->execute([$ticket_id, $_SESSION['user_id'], $assigned_to]);
    }

    if (isset($_POST['tags'])) {
        // First, remove all existing tags for this ticket
        $stmt = $pdo->prepare("UPDATE ticket_tags SET status = 0 WHERE ticket_id = ?");
        $stmt->execute([$ticket_id]);

        // Then, add the selected tags
        $selected_tags = $_POST['tags'];
        foreach ($selected_tags as $tag_id) {
            $stmt = $pdo->prepare("INSERT INTO ticket_tags (ticket_id, tag_id, status) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE status = 1");
            $stmt->execute([$ticket_id, $tag_id]);
        }
    }

    // Redirect to the same page to avoid form resubmission
    header("Location: ticket_summary.php?ticket_id=$ticket_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Ticket Summary</title>
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
    <!-- Navigation Bar -->
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

    <!-- Ticket Summary -->
    <div class="container mx-auto p-4 mt-4">
        <h2 class="text-3xl font-semibold mb-6">Ticket Summary</h2>

        <!-- Ticket Details -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Ticket Details</h3>
            <p><strong>Title:</strong> <?= htmlspecialchars($ticket['title']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($ticket['description']) ?></p>
            <p><strong>Partner:</strong> <?= htmlspecialchars($ticket['partner_id']) ?></p>
        </div>

        <!-- Tag Management -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Manage Tags</h3>
            <form action="ticket_summary.php?ticket_id=<?= $ticket_id ?>" method="POST">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tags</label>
                    <div class="mt-2 space-x-2">
                        <?php foreach ($tags as $tag): ?>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="tags[]" value="<?= $tag['tag_id'] ?>" class="form-checkbox text-accent" <?= in_array($tag['tag_id'], $assigned_tags) ? 'checked' : '' ?>>
                                <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full" style="background-color: <?= $tag['tag_color'] ?>; color: white;">
                                    <?= htmlspecialchars($tag['tag_name']) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-accent hover:bg-opacity-80 text-white font-bold py-2 px-4 rounded">Update Tags</button>
                </div>
            </form>
        </div>

        <!-- Ticket Assignment -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Assign Ticket</h3>
            <form action="ticket_summary.php?ticket_id=<?= $ticket_id ?>" method="POST">
                <div class="mb-4">
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign to:</label>
                    <select name="assigned_to" id="assigned_to" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                        <option value="">-- Select User --</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['user_id'] ?>" <?= $user['user_id'] == $ticket['assigned_to'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-accent hover:bg-opacity-80 text-white font-bold py-2 px-4 rounded">Assign</button>
                </div>
            </form>
        </div>

        <!-- Comments Section -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Add Comment</h3>
            <form action="ticket_summary.php?ticket_id=<?= $ticket_id ?>" method="POST">
                <textarea name="comment" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50"><?= htmlspecialchars($ticket['comments']) ?></textarea>
                <div class="mt-6">
                    <button type="submit" class="bg-accent hover:bg-opacity-80 text-white font-bold py-2 px-4 rounded">Add Comment</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
