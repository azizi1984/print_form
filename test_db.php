<?php
$host = '172.26.240.1';
$db   = 'printform';
$user = 'printform';
$pass = 'isylzjkO@123456';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $stmt = $pdo->query("DESCRIBE profile_templates");
     while ($row = $stmt->fetch()) {
         echo $row['Field'] . " - " . $row['Type'] . "\n";
     }
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
