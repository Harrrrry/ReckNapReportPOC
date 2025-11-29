<?php
/**
 * Test Setup Script for ReckNap Report POC
 * Verifies that all components are working correctly
 */

echo "🧪 ReckNap Report POC - System Test\n";
echo "===================================\n\n";

$tests = [];
$passed = 0;
$failed = 0;

// Test 1: Check PHP version
echo "1. Testing PHP Version...\n";
$phpVersion = PHP_VERSION;
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "   ✅ PHP $phpVersion (OK)\n";
    $tests['php_version'] = true;
    $passed++;
} else {
    echo "   ❌ PHP $phpVersion (Requires 7.4+)\n";
    $tests['php_version'] = false;
    $failed++;
}

// Test 2: Check required PHP extensions
echo "\n2. Testing PHP Extensions...\n";
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'zip'];
$extension_tests = [];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext extension loaded\n";
        $extension_tests[$ext] = true;
    } else {
        echo "   ❌ $ext extension missing\n";
        $extension_tests[$ext] = false;
        $failed++;
    }
}

if (array_sum($extension_tests) === count($required_extensions)) {
    $tests['extensions'] = true;
    $passed++;
} else {
    $tests['extensions'] = false;
}

// Test 3: Check file structure
echo "\n3. Testing File Structure...\n";
$required_files = [
    'database/schema.sql',
    'database/sample_data.sql',
    'app/Controller/ReportsController.php',
    'app/Model/ReportField.php',
    'app/View/Reports/index.ctp',
    'webroot/js/report-manager.js',
    'composer.json'
];

$file_tests = [];
foreach ($required_files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✅ $file exists\n";
        $file_tests[$file] = true;
    } else {
        echo "   ❌ $file missing\n";
        $file_tests[$file] = false;
        $failed++;
    }
}

if (array_sum($file_tests) === count($required_files)) {
    $tests['file_structure'] = true;
    $passed++;
} else {
    $tests['file_structure'] = false;
}

// Test 4: Check database connection
echo "\n4. Testing Database Connection...\n";
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'recknap_reports';
    
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✅ MySQL server connection successful\n";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ Database '$database' exists\n";
        
        // Connect to database and check tables
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        $expected_tables = ['report_fields', 'customers', 'invoices', 'products'];
        $missing_tables = array_diff($expected_tables, $tables);
        
        if (empty($missing_tables)) {
            echo "   ✅ All required tables exist\n";
            $tests['database'] = true;
            $passed++;
        } else {
            echo "   ❌ Missing tables: " . implode(', ', $missing_tables) . "\n";
            echo "   💡 Run 'php setup.php' to create tables\n";
            $tests['database'] = false;
            $failed++;
        }
    } else {
        echo "   ❌ Database '$database' does not exist\n";
        echo "   💡 Run 'php setup.php' to create database\n";
        $tests['database'] = false;
        $failed++;
    }
    
} catch (PDOException $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "   💡 Check database configuration in app/Config/database.php\n";
    $tests['database'] = false;
    $failed++;
}

// Test 5: Check Composer dependencies
echo "\n5. Testing Composer Dependencies...\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "   ✅ Composer dependencies installed\n";
    
    // Check for PhpSpreadsheet
    if (file_exists(__DIR__ . '/vendor/phpoffice/phpspreadsheet')) {
        echo "   ✅ PhpSpreadsheet library available\n";
        $tests['composer'] = true;
        $passed++;
    } else {
        echo "   ❌ PhpSpreadsheet library missing\n";
        echo "   💡 Run 'composer install' to install dependencies\n";
        $tests['composer'] = false;
        $failed++;
    }
} else {
    echo "   ❌ Composer dependencies not installed\n";
    echo "   💡 Run 'composer install' to install dependencies\n";
    $tests['composer'] = false;
    $failed++;
}

// Test 6: Check web server configuration
echo "\n6. Testing Web Server Configuration...\n";
if (isset($_SERVER['HTTP_HOST'])) {
    echo "   ✅ Running via web server\n";
    echo "   🌐 Access URL: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/\n";
    $tests['web_server'] = true;
    $passed++;
} else {
    echo "   ⚠️  Running via CLI (not web server)\n";
    echo "   💡 Configure web server to point to 'webroot' directory\n";
    echo "   💡 Example: http://localhost/recknapReportPoc/webroot/\n";
    $tests['web_server'] = false;
}

// Test 7: Check permissions
echo "\n7. Testing File Permissions...\n";
$temp_dir = sys_get_temp_dir();
$test_file = $temp_dir . '/recknap_test_' . uniqid() . '.txt';

if (is_writable($temp_dir)) {
    file_put_contents($test_file, 'test');
    if (file_exists($test_file)) {
        echo "   ✅ Temporary directory writable\n";
        unlink($test_file);
        $tests['permissions'] = true;
        $passed++;
    } else {
        echo "   ❌ Cannot create files in temporary directory\n";
        $tests['permissions'] = false;
        $failed++;
    }
} else {
    echo "   ❌ Temporary directory not writable\n";
    echo "   💡 Excel export requires write access to temp directory\n";
    $tests['permissions'] = false;
    $failed++;
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";

foreach ($tests as $test => $result) {
    $status = $result ? '✅ PASS' : '❌ FAIL';
    $test_name = ucwords(str_replace('_', ' ', $test));
    echo sprintf("%-20s: %s\n", $test_name, $status);
}

echo "\nResults: $passed passed, $failed failed\n";

if ($failed === 0) {
    echo "\n🎉 All tests passed! Your ReckNap Report POC is ready to use.\n";
    echo "\n📋 Next Steps:\n";
    echo "1. Access the application via web browser\n";
    echo "2. Try creating a sample report\n";
    echo "3. Test Excel export functionality\n";
    echo "4. Explore field management features\n";
} else {
    echo "\n⚠️  Some tests failed. Please resolve the issues above before proceeding.\n";
    echo "\n🔧 Common Solutions:\n";
    echo "- Run 'composer install' for dependencies\n";
    echo "- Run 'php setup.php' for database setup\n";
    echo "- Check PHP extensions and versions\n";
    echo "- Configure web server properly\n";
}

echo "\n📚 For detailed setup instructions, see README.md\n";
?>
