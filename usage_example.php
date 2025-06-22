<?php
/**
 * Usage Example for Robert CAT Tool
 * Demonstrates all the functionality of the TranslationUnit class
 */

require_once 'src/TranslationUnit.php';

echo "=== Robert CAT Tool - Usage Example ===\n\n";

try {
    // Clear existing data for clean demonstration
    file_put_contents(__DIR__ . '/database/translations.json', '[]');
    echo "1. Cleared existing data for demonstration\n\n";

    // 1. Add new translation units
    echo "2. Adding new translation units:\n";
    $unit1 = new TranslationUnit('Hello World', 'Bonjour le monde');
    $unit1->save();
    echo "   - Added: 'Hello World' -> 'Bonjour le monde'\n";

    $unit2 = new TranslationUnit('How are you?', 'Comment allez-vous?');
    $unit2->save();
    echo "   - Added: 'How are you?' -> 'Comment allez-vous?'\n";

    $unit3 = new TranslationUnit('Thank you', 'Merci');
    $unit3->save();
    echo "   - Added: 'Thank you' -> 'Merci'\n\n";

    // 2. Retrieve a translation unit by ID
    echo "3. Retrieving translation unit by ID:\n";
    $retrievedUnit = TranslationUnit::getById($unit1->toArray()['id']);
    if ($retrievedUnit) {
        $data = $retrievedUnit->toArray();
        echo "   - Retrieved Unit ID: {$data['id']}\n";
        echo "   - Source: '{$data['source']}'\n";
        echo "   - Target: '{$data['target']}'\n";
        echo "   - History entries: " . count($data['history']) . "\n\n";
    }

    // 3. Update a translation unit and keep history
    echo "4. Updating translation unit (keeping history):\n";
    if ($retrievedUnit) {
        $retrievedUnit->update('Salut le monde');
        echo "   - Updated target to: 'Salut le monde'\n";
        
        $updatedUnit = TranslationUnit::getById($retrievedUnit->toArray()['id']);
        $updatedData = $updatedUnit->toArray();
        echo "   - New target: '{$updatedData['target']}'\n";
        echo "   - History entries: " . count($updatedData['history']) . "\n";
        if (!empty($updatedData['history'])) {
            echo "   - Previous target: '{$updatedData['history'][0]['target']}'\n";
            echo "   - Changed on: {$updatedData['history'][0]['date']}\n";
        }
        echo "\n";
    }

    // 4. Fetch all translation units
    echo "5. Fetching all translation units:\n";
    $allUnits = TranslationUnit::fetchAll();
    echo "   - Total units: " . count($allUnits) . "\n";
    foreach ($allUnits as $index => $unit) {
        echo "   - Unit " . ($index + 1) . ": '{$unit['source']}' -> '{$unit['target']}'\n";
    }
    echo "\n";

    // 5. Test delete functionality
    echo "6. Testing delete functionality:\n";
    $unitToDelete = $allUnits[count($allUnits) - 1]; // Delete the last unit
    $deleteId = $unitToDelete['id'];
    echo "   - Deleting unit: '{$unitToDelete['source']}'\n";
    
    $deleteResult = TranslationUnit::delete($deleteId);
    if ($deleteResult) {
        echo "   - Successfully deleted unit\n";
        
        // Verify deletion
        $deletedUnit = TranslationUnit::getById($deleteId);
        if ($deletedUnit === null) {
            echo "   - Confirmed: Unit no longer exists\n";
        } else {
            echo "   - Error: Unit still exists after deletion\n";
        }
    } else {
        echo "   - Failed to delete unit\n";
    }
    echo "\n";

    // 6. Final state
    echo "7. Final state:\n";
    $finalUnits = TranslationUnit::fetchAll();
    echo "   - Remaining units: " . count($finalUnits) . "\n";
    foreach ($finalUnits as $index => $unit) {
        echo "   - Unit " . ($index + 1) . ": '{$unit['source']}' -> '{$unit['target']}'\n";
    }
    echo "\n";

    // 7. Test error handling
    echo "8. Testing error handling:\n";
    $nonExistentUnit = TranslationUnit::getById('non-existent-id');
    if ($nonExistentUnit === null) {
        echo "   - Correctly returned null for non-existent ID\n";
    } else {
        echo "   - Error: Should have returned null for non-existent ID\n";
    }

    $deleteNonExistent = TranslationUnit::delete('non-existent-id');
    if ($deleteNonExistent === false) {
        echo "   - Correctly returned false for deleting non-existent ID\n";
    } else {
        echo "   - Error: Should have returned false for non-existent ID\n";
    }
    echo "\n";

    echo "=== Usage Example Completed Successfully! ===\n";
    echo "\nTo run the full application:\n";
    echo "1. php setup.php (to add sample data)\n";
    echo "2. php -S localhost:8000 (start server)\n";
    echo "3. Open http://localhost:8000/frontend in your browser\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 