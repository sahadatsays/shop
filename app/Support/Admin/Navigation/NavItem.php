<?php

namespace App\Support\Admin\Navigation;

readonly class NavItem
{
    /**
     * @param  array<int, NavItem>|null  $children
     */
    public function __construct(
        public string $label,
        public ?string $route = null,
        public ?string $routePrefix = null,
        public ?string $icon = null,
        public ?string $badge = null,
        public bool $disabled = false,
        public ?array $children = null,
        public ?string $paletteGroup = null,
        public ?string $permission = null,
    ) {}

    public function isActive(?string $currentRoute): bool
    {
        if ($currentRoute === null) {
            return false;
        }

        if ($this->route !== null) {
            return $currentRoute === $this->route;
        }

        if ($this->routePrefix !== null) {
            return str_starts_with($currentRoute, $this->routePrefix);
        }

        if ($this->children !== null) {
            foreach ($this->children as $child) {
                if ($child->isActive($currentRoute)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function href(): ?string
    {
        if ($this->disabled || $this->route === null) {
            return null;
        }

        return route($this->route);
    }
}
