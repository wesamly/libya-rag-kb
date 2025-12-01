<?php
// config.php (optional: separate config)
// $host = '127.0.0.1';
// $port = '5432';
// $dbname = 'db1';
// $user = 'user1';
// $password = 's3cure&g00d';

try {
    // Build DSN
    //$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";

    // PDO options
    // $options = [
    //     //PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    //     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    //     PDO::ATTR_EMULATE_PREPARES   => false,
    // ];
$options = [
    8  => 0,
    3  => 2,
    11 => 0,
    17 => 0,
];
$dataJson = '["pgsql:host=127.0.0.1;dbname=\'db1\';port=5432;client_encoding=\'utf8\';sslmode=prefer","user1","s3cure&g00d",{"8":0,"3":2,"11":0,"17":false}]';
[$dsn, $user, $password, $options] = json_decode($dataJson, true);
    // Connect
    $pdo = new PDO($dsn, $user, $password, $options);\DevyTools::devLog($pdo, "55");
    \DevyTools::devLog($pdo, "57");
    // Prepare and execute COUNT query
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM article_vectors");
    $stmt->execute();
    $result = $stmt->fetch();

    // Output
    echo "Total records in article_vectors: " . ($result['total'] ?? 0) . "\n";

} catch (PDOException $e) {
    // Handle errors (print to stderr or log)
    fwrite(STDERR, "Database error: " . $e->getMessage() . "\n");
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, "General error: " . $e->getMessage() . "\n");
    exit(1);
}