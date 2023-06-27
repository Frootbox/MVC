<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\View\Menu;

class Item
{
    /**
     *
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
    )
    {

    }

    /**
     *
     */
    public function getBadge(): ?int
    {
        return $this->badge;
    }

    /**
     *
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     *
     */
    public function getConfirm(): ?string
    {
        return $this->confirm;
    }

    /**
     *
     */
    public function getIcon(): string
    {
        if (empty($this->icon)) {
            return 'minus';
        }

        return $this->icon;
    }

    /**
     *
     */
    public function isModal(): bool
    {
        return $this->isModal;
    }

    /**
     *
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     *
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     *
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
