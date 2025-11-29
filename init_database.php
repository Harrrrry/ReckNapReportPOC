<?php
/**
 * Database initialization script for Railway deployment
 */

echo "🚀 Initializing ReckNap Database...\n";

try {
    // Connect to database
    $host = $_ENV['MYSQLHOST'] ?? 'localhost';
    $database = $_ENV['MYSQLDATABASE'] ?? 'recknap_reports';
    $username = $_ENV['MYSQLUSER'] ?? 'root';
    $password = $_ENV['MYSQLPASSWORD'] ?? '';
    $port = $_ENV['MYSQLPORT'] ?? '3306';
    
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database successfully\n";
    
    // Check if tables already exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'report_fields'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Database already initialized\n";
        exit(0);
    }
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ Schema created successfully\n";
    
    // Read and execute sample data
    $sampleData = file_get_contents(__DIR__ . '/database/sample_data.sql');
    $statements = array_filter(array_map('trim', explode(';', $sampleData)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ Sample data inserted successfully\n";
    echo "🎉 Database initialization complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
