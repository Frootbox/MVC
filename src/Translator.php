<?php
/**
 * 
 */

namespace Frootbox\MVC;
 
class Translator {

    protected $languagePrimary = 'de-DE';
    protected $data;

    /**
     *
     */
    public function addResource(string $file, $language = 'de-DE' ): void
    {
        $this->data[$language] = require($file);
    }

    /**
     *
     */
    public function translate(string $key): ?string
    {
        return $this->data[$this->languagePrimary][$key] ?? $key;
    }
}