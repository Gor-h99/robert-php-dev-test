<?php
/**
 * Database Migration Script for Robert CAT Tool
 * This script sets up the database schema and initial data
 */

// Database configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'robert_cat_tool',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

try {
    // Create PDO connection
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL server successfully.\n";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '{$config['dbname']}' created or already exists.\n";
    
    // Select the database
    $pdo->exec("USE `{$config['dbname']}`");
    
    // Read and execute schema file
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        $schema = file_get_contents($schemaFile);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|\/\*)/', $statement)) {
                try {
                    $pdo->exec($statement);
                    echo "Executed: " . substr($statement, 0, 50) . "...\n";
                } catch (PDOException $e) {
                    // Skip if table/view already exists
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "Error executing statement: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        
        echo "\nDatabase schema created successfully!\n";
    } else {
        echo "Schema file not found: $schemaFile\n";
        exit(1);
    }
    
    // Insert sample data
    echo "\nInserting sample data...\n";
    
    // Insert sample translation units
    $sampleUnits = [
        ['Hello world', 'en', 'Basic greeting'],
        ['Good morning', 'en', 'Morning greeting'],
        ['How are you?', 'en', 'Casual question'],
        ['Thank you', 'en', 'Expression of gratitude'],
        ['You\'re welcome', 'en', 'Response to thank you'],
        ['Goodbye', 'en', 'Farewell expression'],
        ['Please', 'en', 'Polite request'],
        ['Excuse me', 'en', 'Apology or attention'],
        ['I don\'t understand', 'en', 'Confusion expression'],
        ['Can you help me?', 'en', 'Request for assistance']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO translation_units (source_text, source_language_code, context) VALUES (?, ?, ?)");
    
    foreach ($sampleUnits as $unit) {
        try {
            $stmt->execute($unit);
            echo "Added translation unit: '{$unit[0]}'\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "Error adding unit: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Insert sample translations
    $sampleTranslations = [
        [1, 'fr', 'Bonjour le monde'],
        [2, 'fr', 'Bonjour'],
        [3, 'fr', 'Comment allez-vous?'],
        [4, 'fr', 'Merci'],
        [5, 'fr', 'De rien'],
        [6, 'fr', 'Au revoir'],
        [7, 'fr', 'S\'il vous plaît'],
        [8, 'fr', 'Excusez-moi'],
        [9, 'fr', 'Je ne comprends pas'],
        [10, 'fr', 'Pouvez-vous m\'aider?']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO translations (unit_id, language_code, target_text, status) VALUES (?, ?, ?, 'approved')");
    
    foreach ($sampleTranslations as $translation) {
        try {
            $stmt->execute($translation);
            echo "Added translation: Unit {$translation[0]} -> {$translation[2]} ({$translation[1]})\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "Error adding translation: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nMigration completed successfully!\n";
    echo "Database is ready for use.\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "\nPlease check your database configuration in the migrate.php file.\n";
    echo "Make sure MySQL is running and the credentials are correct.\n";
    exit(1);
} 