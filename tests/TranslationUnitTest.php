<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/TranslationUnit.php';

class TranslationUnitTest extends TestCase
{
    private $testDataFile;
    private $originalData;

    protected function setUp(): void
    {
        $this->testDataFile = __DIR__ . '/../database/translations.json';
        
        // Backup original data
        if (file_exists($this->testDataFile)) {
            $this->originalData = file_get_contents($this->testDataFile);
        }
        
        // Start with empty data for each test
        file_put_contents($this->testDataFile, '[]');
    }

    protected function tearDown(): void
    {
        // Restore original data
        if (isset($this->originalData)) {
            file_put_contents($this->testDataFile, $this->originalData);
        } else {
            unlink($this->testDataFile);
        }
    }

    public function testConstructorCreatesTranslationUnit()
    {
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde');
        
        $this->assertNotNull($unit);
        $this->assertInstanceOf(TranslationUnit::class, $unit);
    }

    public function testConstructorGeneratesIdWhenNotProvided()
    {
        $unit1 = new TranslationUnit('Hello world');
        $unit2 = new TranslationUnit('Goodbye world');
        
        $this->assertNotEmpty($unit1->toArray()['id']);
        $this->assertNotEmpty($unit2->toArray()['id']);
        $this->assertNotEquals($unit1->toArray()['id'], $unit2->toArray()['id']);
    }

    public function testConstructorUsesProvidedId()
    {
        $customId = 'test-id-123';
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde', $customId);
        
        $this->assertEquals($customId, $unit->toArray()['id']);
    }

    public function testSaveStoresTranslationUnit()
    {
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde');
        $unit->save();
        
        $allUnits = TranslationUnit::fetchAll();
        $this->assertCount(1, $allUnits);
        $this->assertEquals('Hello world', $allUnits[0]['source']);
        $this->assertEquals('Bonjour le monde', $allUnits[0]['target']);
    }

    public function testGetByIdReturnsCorrectUnit()
    {
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde');
        $unit->save();
        
        $retrievedUnit = TranslationUnit::getById($unit->toArray()['id']);
        
        $this->assertNotNull($retrievedUnit);
        $this->assertEquals('Hello world', $retrievedUnit->toArray()['source']);
        $this->assertEquals('Bonjour le monde', $retrievedUnit->toArray()['target']);
    }

    public function testGetByIdReturnsNullForNonExistentId()
    {
        $unit = TranslationUnit::getById('non-existent-id');
        
        $this->assertNull($unit);
    }

    public function testUpdateModifiesTargetAndKeepsHistory()
    {
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde');
        $unit->save();
        
        $originalId = $unit->toArray()['id'];
        $unit->update('Salut le monde');
        
        $updatedUnit = TranslationUnit::getById($originalId);
        $this->assertEquals('Salut le monde', $updatedUnit->toArray()['target']);
        
        // Check that history is maintained
        $this->assertNotEmpty($updatedUnit->toArray()['history']);
        $this->assertEquals('Bonjour le monde', $updatedUnit->toArray()['history'][0]['target']);
    }

    public function testUpdateMultipleTimesMaintainsHistory()
    {
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde');
        $unit->save();
        
        $unit->update('Salut le monde');
        $unit->update('Ciao mondo');
        
        $updatedUnit = TranslationUnit::getById($unit->toArray()['id']);
        $history = $updatedUnit->toArray()['history'];
        
        $this->assertCount(2, $history);
        $this->assertEquals('Bonjour le monde', $history[0]['target']);
        $this->assertEquals('Salut le monde', $history[1]['target']);
        $this->assertEquals('Ciao mondo', $updatedUnit->toArray()['target']);
    }

    public function testToArrayReturnsCorrectStructure()
    {
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde', 'test-id');
        
        $array = $unit->toArray();
        
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('source', $array);
        $this->assertArrayHasKey('target', $array);
        $this->assertArrayHasKey('history', $array);
        
        $this->assertEquals('test-id', $array['id']);
        $this->assertEquals('Hello world', $array['source']);
        $this->assertEquals('Bonjour le monde', $array['target']);
        $this->assertIsArray($array['history']);
    }

    public function testFetchAllReturnsEmptyArrayWhenNoData()
    {
        $units = TranslationUnit::fetchAll();
        
        $this->assertIsArray($units);
        $this->assertEmpty($units);
    }

    public function testFetchAllReturnsAllUnits()
    {
        $unit1 = new TranslationUnit('Hello world', 'Bonjour le monde');
        $unit2 = new TranslationUnit('Goodbye world', 'Au revoir le monde');
        
        $unit1->save();
        $unit2->save();
        
        $allUnits = TranslationUnit::fetchAll();
        
        $this->assertCount(2, $allUnits);
    }

    public function testDeleteRemovesUnit()
    {
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde');
        $unit->save();
        
        $id = $unit->toArray()['id'];
        $success = TranslationUnit::delete($id);
        
        $this->assertTrue($success);
        $this->assertNull(TranslationUnit::getById($id));
    }

    public function testDeleteReturnsFalseForNonExistentId()
    {
        $success = TranslationUnit::delete('non-existent-id');
        
        $this->assertFalse($success);
    }

    public function testDeleteMaintainsOtherUnits()
    {
        $unit1 = new TranslationUnit('Hello world', 'Bonjour le monde');
        $unit2 = new TranslationUnit('Goodbye world', 'Au revoir le monde');
        
        $unit1->save();
        $unit2->save();
        
        $id1 = $unit1->toArray()['id'];
        $id2 = $unit2->toArray()['id'];
        
        TranslationUnit::delete($id1);
        
        $this->assertNull(TranslationUnit::getById($id1));
        $this->assertNotNull(TranslationUnit::getById($id2));
    }

    public function testHistoryContainsTimestamp()
    {
        $unit = new TranslationUnit('Hello world', 'Bonjour le monde');
        $unit->save();
        
        $unit->update('Salut le monde');
        
        $updatedUnit = TranslationUnit::getById($unit->toArray()['id']);
        $history = $updatedUnit->toArray()['history'];
        
        $this->assertNotEmpty($history);
        $this->assertArrayHasKey('target', $history[0]);
        $this->assertArrayHasKey('date', $history[0]);
        $this->assertNotEmpty($history[0]['date']);
    }
}
