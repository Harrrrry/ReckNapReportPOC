<?php
/**
 * Database initialization script for Railway deployment
 */

echo "🚀 Initializing ReckNap Database...\n<br>";

try {
    // Connect to database using Railway environment variables
    $host = $_ENV['MYSQL_HOST'] ?? 'localhost';
    $database = $_ENV['MYSQL_DATABASE'] ?? 'railway';
    $username = $_ENV['MYSQL_USER'] ?? 'root';
    $password = $_ENV['MYSQL_PASSWORD'] ?? '';
    $port = $_ENV['MYSQL_PORT'] ?? '3306';
    
    // Check if we have a DATABASE_URL (Railway often provides this)
    if (isset($_ENV['DATABASE_URL'])) {
        echo "🔗 Using DATABASE_URL: " . substr($_ENV['DATABASE_URL'], 0, 20) . "...\n<br>";
        $pdo = new PDO($_ENV['DATABASE_URL']);
    } else {
        echo "🔗 Using individual connection parameters\n<br>";
        echo "🔌 Connecting to: $host:$port/$database as $username\n<br>";
        
        // Try connection with different options for Railway
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 30,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ];
        
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8", $username, $password, $options);
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database successfully\n<br>";
    
    // Read and execute schema
    $schemaPath = __DIR__ . '/../database/schema.sql';
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema file not found at: $schemaPath");
    }
    
    $schema = file_get_contents($schemaPath);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    echo "📊 Creating database schema...\n<br>";
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ Database schema created successfully\n<br>";
    
    // Read and execute sample data
    $dataPath = __DIR__ . '/../database/sample_data.sql';
    if (!file_exists($dataPath)) {
        throw new Exception("Sample data file not found at: $dataPath");
    }
    
    $sampleData = file_get_contents($dataPath);
    
    // Split by semicolon and execute each statement
    $dataStatements = array_filter(array_map('trim', explode(';', $sampleData)));
    
    echo "🔧 Inserting sample data...\n<br>";
    foreach ($dataStatements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ Sample data inserted successfully\n<br>";
    
    // Verify tables were created
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Created tables: " . implode(', ', $tables) . "\n<br>";
    
    // Count records in each table
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "📊 $table: $count records\n<br>";
    }
    
    echo "\n<br>🎉 SUCCESS - Database initialized successfully!\n<br>";
    echo "🔗 <a href='/'>Go to Report Interface</a>\n<br>";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n<br>";
    echo "🔍 Debug info:\n<br>";
    echo "Host: " . ($_ENV['MYSQL_HOST'] ?? 'not set') . "\n<br>";
    echo "Database: " . ($_ENV['MYSQL_DATABASE'] ?? 'not set') . "\n<br>";
    echo "User: " . ($_ENV['MYSQL_USER'] ?? 'not set') . "\n<br>";
    echo "Port: " . ($_ENV['MYSQL_PORT'] ?? 'not set') . "\n<br>";
}
?>
