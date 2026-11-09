<?php

header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/db.php';

/* 1. Read q safely */
$search = '';
if (isset($_GET['q']) && is_string($_GET['q'])) {
    $search = trim($_GET['q']);
}
$pattern = '%' . $search . '%';

/* 2. Validate subject filter */
$subject = filter_input(INPUT_GET, 'subject', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);
// $subject is now either an int ≥ 1 or null

/* 3. Validate available filter */
$available = filter_input(INPUT_GET, 'available', FILTER_VALIDATE_INT);
$available = ($available === 1) ? 1 : null;

/* 4. Build SQL in steps */
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
    WHERE (books.title LIKE :title OR books.author LIKE :author)
";

$params = [
    ':title'  => $pattern,
    ':author' => $pattern
];

/* Add subject filter if valid */
if (is_int($subject)) {
    $sql .= " AND books.subject_id = :subject";
    $params[':subject'] = $subject;
}

/* Add available filter if set */
if ($available === 1) {
    $sql .= " AND (books.copies - books.on_loan) > 0";
}

/* 5. Add ORDER BY */
$sql .= " ORDER BY books.title";

/* 6. Execute */
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);
