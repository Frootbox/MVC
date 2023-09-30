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
     * @return void
     * @throws \Exception
     */
    public function addCacheFile(string $file): void
    {
        if (!file_exists($file)) {
            throw new \Exception('Cache file does not exist.');
        }

        // Set data from cache
        $this->setData(require $file);
    }

    /**
     * @param string $file
     * @param string $language
     * @return void
     */
    public function addResource(string $file, string $language = 'de-DE' ): void
    {
        if (!isset($this->data[$language])) {
            $this->data[$language] = [];
        }

        $this->data[$language] = array_merge_recursive($this->data[$language], require($file));
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data ?? [];
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function translate(string $key): ?string
    {
        return $this->data[$this->languagePrimary][$key] ?? $key;
    }

    /**
     * @param string $cacheFile
     * @return void
     * @throws \Exception
     */
    public function writeCache(string $cacheFile): void
    {
        // Generate cache file source
        $source = '<?php' . PHP_EOL . 'return ' . var_export($this->getData(), true) . ';' . PHP_EOL;

        // Write cache file
        $file = new \Frootbox\Filesystem\File($cacheFile);
        $file->setSource($source);
        $file->write();
    }
}
