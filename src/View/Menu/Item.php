<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\View\Menu;

class Item
{
    /**
     * @param string $title
     * @param string|null $url
     * @param string|null $icon
     * @param string|null $class
     * @param string|null $confirm
     * @param array|null $paths
     * @param int|null $badge
     * @param bool $isModal
     * @param array|null $subItems
     */
    public function __construct(
        protected string $title,
        protected ?string $url = null,
        protected ?string $icon = null,
        protected ?string $class = null,
        protected ?string $confirm = null,
        protected ?array $paths = null,
        protected ?int $badge = null,
        protected bool $isModal = false,
        protected ?array $subItems = null,
    )
    {

    }

    /**
     * @return int|null
     */
    public function getBadge(): ?int
    {
        return $this->badge;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function getConfirm(): ?string
    {
        return $this->confirm;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        if (empty($this->icon)) {
            return 'minus';
        }

        return $this->icon;
    }

    /**
     * @return bool
     */
    public function isModal(): bool
    {
        return $this->isModal;
    }

    /**
     * @return array
     */
    public function getSubItems(): array
    {
        return $this->subItems ?? [];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {

        if (ORIGINAL_REQUEST == $this->getUrl()) {
            return true;
        }

        $staticRequest = explode('?', ORIGINAL_REQUEST)[0];

        if ($staticRequest == $this->getUrl()) {
            return true;
        }

        if (empty($this->paths)) {
            return false;
        }

        foreach ($this->paths as $path) {

            if (ORIGINAL_REQUEST == $path) {
                return true;
            }

            if ($staticRequest == $path) {
                return true;
            }
        }

        return false;
    }
}
