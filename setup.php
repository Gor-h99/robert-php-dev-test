<?php
/**
 * Setup script for Robert CAT Tool
 * This script initializes the application with sample translation units
 */

require_once __DIR__ . '/src/TranslationUnit.php';

echo "=== Robert CAT Tool - Setup Script ===\n\n";

try {
    // Check if database directory exists
    $databaseDir = __DIR__ . '/database';
    if (!is_dir($databaseDir)) {
        mkdir($databaseDir, 0755, true);
        echo "✓ Created database directory\n";
    }

    // Check if database file exists and is writable
    $databaseFile = $databaseDir . '/translations.json';
    if (file_exists($databaseFile) && !is_writable($databaseFile)) {
        throw new Exception("Database file is not writable: $databaseFile");
    }

    echo "✓ Database file is accessible\n\n";

    // Sample translation units with more variety
    $sampleTranslations = [
        ['Hello world', 'Bonjour le monde', 'Basic greeting'],
        ['Good morning', 'Bonjour', 'Morning greeting'],
        ['How are you?', 'Comment allez-vous?', 'Casual question'],
        ['Thank you', 'Merci', 'Expression of gratitude'],
        ['You\'re welcome', 'De rien', 'Response to thank you'],
        ['Goodbye', 'Au revoir', 'Farewell expression'],
        ['Please', 'S\'il vous plaît', 'Polite request'],
        ['Excuse me', 'Excusez-moi', 'Apology or attention'],
        ['I don\'t understand', 'Je ne comprends pas', 'Confusion expression'],
        ['Can you help me?', 'Pouvez-vous m\'aider?', 'Request for assistance'],
        ['What time is it?', 'Quelle heure est-il?', 'Time inquiry'],
        ['Where is the bathroom?', 'Où sont les toilettes?', 'Location question'],
        ['How much does this cost?', 'Combien ça coûte?', 'Price inquiry'],
        ['I love you', 'Je t\'aime', 'Expression of love'],
        ['Have a nice day', 'Bonne journée', 'Well-wishing']
    ];

    echo "Setting up sample data...\n";
    echo "Total translations to add: " . count($sampleTranslations) . "\n\n";

    // Clear existing data
    file_put_contents($databaseFile, '[]');
    echo "✓ Cleared existing data\n";

    // Add sample translations
    $successCount = 0;
    $errorCount = 0;

    foreach ($sampleTranslations as $index => $translation) {
        try {
            $unit = new TranslationUnit($translation[0], $translation[1]);
            $unit->save();
            $successCount++;
            echo "✓ Added: '{$translation[0]}' -> '{$translation[1]}' ({$translation[2]})\n";
        } catch (Exception $e) {
            $errorCount++;
            echo "✗ Failed to add: '{$translation[0]}' - {$e->getMessage()}\n";
        }
    }

    echo "\n=== Setup Summary ===\n";
    echo "✓ Successfully added: $successCount translations\n";
    if ($errorCount > 0) {
        echo "✗ Failed to add: $errorCount translations\n";
    }

    // Verify the data was saved correctly
    echo "\nVerifying data integrity...\n";
    $allUnits = TranslationUnit::fetchAll();
    $savedCount = count($allUnits);
    
    if ($savedCount === $successCount) {
        echo "✓ Data integrity verified: $savedCount units saved\n";
    } else {
        echo "✗ Data integrity issue: Expected $successCount, found $savedCount\n";
    }

    // Test API endpoint if possible
    echo "\nTesting API endpoint...\n";
    if (function_exists('curl_init')) {
        // Start a temporary server for testing
        $serverProcess = null;
        $testPort = 8001; // Use different port to avoid conflicts
        
        // Note: In a real scenario, you'd start the server here
        // For now, we'll just check if the API file exists
        $apiFile = __DIR__ . '/api/translations.php';
        if (file_exists($apiFile)) {
            echo "✓ API endpoint file exists: $apiFile\n";
        } else {
            echo "✗ API endpoint file missing: $apiFile\n";
        }
    } else {
        echo "⚠ cURL not available - skipping API test\n";
    }

    echo "\n=== Setup Complete! ===\n";
    echo "\nNext steps:\n";
    echo "1. Start PHP server: php -S localhost:8000\n";
    echo "2. Open frontend: http://localhost:8000/frontend\n";
    echo "3. Test API: http://localhost:8000/api/translations.php\n";
    echo "4. Run tests: php vendor/bin/phpunit tests/TranslationUnitTest.php\n";
    echo "\nSample data includes $savedCount English-French translation units.\n";

} catch (Exception $e) {
    echo "\n✗ Setup failed with error:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
} 