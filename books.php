<?php

header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/db.php';


$search = '';
if (isset($_GET['q']) && is_string($_GET['q'])) {
    $search = trim($_GET['q']);
}
$pattern = '%' . $search . '%';


$subject = filter_input(INPUT_GET, 'subject', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);


$available = filter_input(INPUT_GET, 'available', FILTER_VALIDATE_INT);
if ($available !== 1) {
    $available = null;
}


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


if (is_int($subject)) {
    $sql .= " AND books.subject_id = :subject";
    $params[':subject'] = $subject;
}


if ($available === 1) {
    $sql .= " AND (books.copies - books.on_loan) > 0";
}


$sql .= " ORDER BY books.title";


$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);
