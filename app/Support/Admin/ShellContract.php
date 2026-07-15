<?php

/**
 * Admin shell layout contract.
 *
 * Regions:
 * - Sidebar: primary IA via NavRegistry, collapse/mobile drawer state in localStorage
 * - Topbar: breadcrumb, search/palette trigger, quick actions, notifications, theme, profile
 * - Main: page header + module slot (only scrollable content region)
 * - Overlays: command palette, toast host, modal host, page loader (portal-style)
 *
 * Module pages must provide: breadcrumbs array, title, and main slot content.
 * Modules must not add a second global sidebar or fork shell chrome.
 *
 * Responsive breakpoints:
 * - Mobile <768px: sidebar drawer, compact topbar
 * - Tablet 768–1023px: icon rail default
 * - Desktop ≥1024px: expanded sidebar with optional collapse to rail
 *
 * Dark mode: class="dark" on html, localStorage admin-theme (light|dark|system)
 *
 * @see App\Support\Admin\Navigation\NavRegistry
 */

namespace App\Support\Admin;

final class ShellContract
{
    public const SIDEBAR_WIDTH = '16rem';

    public const SIDEBAR_RAIL = '4.5rem';

    public const TOPBAR_HEIGHT = '3.5rem';

    public const BREAKPOINT_MOBILE = 768;

    public const BREAKPOINT_DESKTOP = 1024;
}
