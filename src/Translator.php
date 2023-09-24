<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;
 
class Translator {

    protected string $languagePrimary = 'de-DE';
    protected array $data;

    /**
     * @param string $file
     * @param string $language
     * @return void
     */
    public function addResource(string $file, string $language = 'de-DE' ): void
    {
        $this->data[$language] = require($file);
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function translate(string $key): ?string
    {
        return $this->data[$this->languagePrimary][$key] ?? $key;
    }
}
