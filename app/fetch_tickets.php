<?php
// fetch_tickets.php
require 'db_connection.php';

// Fetch tickets and associated tags with colors
$sql = "
    SELECT 
        t.ticket_id, t.title, t.description, p.partner_name AS location, 
        GROUP_CONCAT(CONCAT(tg.tag_name, '|', tg.tag_color) SEPARATOR ',') AS tags
    FROM 
        tickets t
    LEFT JOIN 
        partners p ON t.partner_id = p.partner_id
    LEFT JOIN 
        ticket_tags tt ON t.ticket_id = tt.ticket_id
    LEFT JOIN 
        tags tg ON tt.tag_id = tg.tag_id
    WHERE 
        t.status = 1 AND p.status = 1 AND tg.status = 1 AND tt.status = 1
    GROUP BY 
        t.ticket_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
