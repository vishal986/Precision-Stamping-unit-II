<?php
$start = microtime(true);
try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=precision_stamping', 'root', '');
    $stmt = $pdo->query('SELECT 1');
    echo "Query successful. Time: " . (microtime(true) - $start) . " seconds\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
