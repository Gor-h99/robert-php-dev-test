<?php

class TranslationUnit
{
    private $id;
    private $source;
    private $target;
    private $history = [];

    public function __construct($source, $target = '', $id = null)
    {
        $this->id = $id ?: uniqid();
        $this->source = $source;
        $this->target = $target;
    }

    public function save()
    {
        $translations = self::fetchAll();
        $translations[] = $this->toArray();
        file_put_contents(__DIR__ . '/../database/translations.json', json_encode($translations, JSON_PRETTY_PRINT));
    }

    public static function getById($id)
    {
        $translations = self::fetchAll();
        foreach ($translations as $data) {
            if ($data['id'] === $id) {
                $unit = new self($data['source'], $data['target'], $data['id']);
                $unit->history = $data['history'];
                return $unit;
            }
        }
        return null;
    }

    public function update($newTarget)
    {
        $translations = self::fetchAll();
        foreach ($translations as &$data) {
            if ($data['id'] === $this->id) {
                $this->history[] = ['target' => $this->target, 'date' => date('Y-m-d H:i:s')];
                $this->target = $newTarget;
                $data['target'] = $newTarget;
                $data['history'] = $this->history;
                break;
            }
        }
        file_put_contents(__DIR__ . '/../database/translations.json', json_encode($translations, JSON_PRETTY_PRINT));
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'source' => $this->source,
            'target' => $this->target,
            'history' => $this->history,
        ];
    }

    public static function fetchAll()
    {
        $content = file_get_contents(__DIR__ . '/../database/translations.json');
        return json_decode($content, true) ?: [];
    }

    public static function delete($id)
    {
        $translations = self::fetchAll();
        $updatedTranslations = array_filter($translations, function ($unit) use ($id) {
            return $unit['id'] !== $id;
        });

        if (count($translations) === count($updatedTranslations)) {
            return false; // Not found
        }

        file_put_contents(__DIR__ . '/../database/translations.json', json_encode(array_values($updatedTranslations), JSON_PRETTY_PRINT));
        return true;
    }
}
