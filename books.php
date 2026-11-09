<?php

header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/db.php';

// 1. Read q safely
$search = '';
if (isset($_GET['q']) && is_string($_GET['q'])) {
    $search = trim($_GET['q']);
}

$pattern = '%' . $search . '%';

// 2–6. Query books with subject name, copies, available, sorted by title
$sql = "
    SELECT 
        books.title,
        books.author,
        books.year,
        subjects.name AS subject,
        books.copies,
        (books.copies - books.on_loan) AS available
    FROM books
    JOIN subjects ON books.subject_id = subjects.id
    WHERE books.title LIKE ?
       OR books.author LIKE ?
    ORDER BY books.title
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$pattern, $pattern]);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);
