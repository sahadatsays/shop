---
name: veteran-store-design
description: "Apply the Jackpot BD LTD design system whenever creating or editing any Blade view, page, layout, or UI component in this project. Triggers for all frontend work: new pages, product listings, cart, checkout, auth screens, emails rendered as HTML, and any Tailwind styling. Defines the brand's colors, typography, spacing, component usage, and accessibility rules. Skip for backend-only PHP, migrations, and tests."
license: MIT
---

# Veteran Store Design System

Premium modern eCommerce for a Veteran Products Store. The brand represents honor, service, trust, patriotism, and quality craftsmanship. Every page must be minimal, elegant, accessible, and conversion-focused. Style references: Apple + Patagonia + Bellroy. Military-inspired without looking aggressive.

## Design Tokens (defined in `resources/css/app.css`)

All tokens are Tailwind v4 `@theme` variables. Never hardcode hex values in templates — use these utilities.

| Role | Token | Utility examples |
|------|-------|------------------|
| Primary (Deep Navy #0F172A) | `navy-50` … `navy-950` | `bg-navy-900`, `text-navy-700` |
| Secondary (Military Green #3B5D50) | `olive-50` … `olive-950` | `bg-olive-600`, `text-olive-800` |
| Accent (Bronze #B08968) | `bronze-50` … `bronze-950` | `bg-bronze-500`, `text-bronze-600` |
| Page background (#FAFAFA) | `canvas` | `bg-canvas` |
| Surface (white) | `surface` | `bg-surface` |
| Body text (#111827) | `ink` | `text-ink` |
| Success / Error | `success` / `danger` | or Tailwind `green-600` / `red-600` |

- Fonts: `font-sans` (Inter) for body, `font-display` (Plus Jakarta Sans) for headings. Headings get `font-display` automatically via base styles.
- Radii: `rounded-xl` (12px) for buttons/inputs, `rounded-card` (20px) for cards/sections, `rounded-field` (12px) for form fields.
- Shadows: `shadow-soft` (resting), `shadow-card` (cards), `shadow-card-hover` (hover), `shadow-glass` (floating/sticky).
- Glassmorphism: `glass` / `glass-dark` utilities — only for sticky nav, overlays, floating panels. Use sparingly.
- Animations: `animate-fade-in`, `animate-fade-in-up`, `animate-scale-in`. Add `data-reveal` to sections for scroll-triggered fade-in-up (wired in `resources/js/app.js`).

## Components (reuse, never rebuild)

Anonymous Blade components in `resources/views/components`:

- `<x-layouts.app>` — base layout with sticky glass nav + footer. Accepts `title` and `description` props. All pages must use it.
- `<x-ui.button variant="primary|secondary|accent|outline|ghost" size="sm|md|lg" :href="...">`
- `<x-ui.badge variant="neutral|navy|olive|bronze|success|danger">`
- `<x-ui.card :hover="true">` — white surface, `rounded-card`, `shadow-card`.
- `<x-ui.input name label type error hint>` — accessible form field with error state.
- `<x-ui.product-card name price category badge badge-variant image href>`
- `<x-ui.section-heading eyebrow title subtitle align="center|left">`

Extend this set with new `x-ui.*` components when a pattern repeats; match the existing prop/variant style.

## Layout Rules

- Page container: `mx-auto max-w-7xl px-4 sm:px-6 lg:px-8`.
- Section rhythm: `py-20 lg:py-28`. Generous whitespace is a brand feature — do not cram.
- Mobile-first: single column by default, expand with `sm:`/`lg:` grid columns. Use `gap-*`, not sibling margins.
- Product grids: `grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4` (or `lg:grid-cols-3` for larger cards).
- Dark sections (hero, footer, CTA) use `bg-navy-900` with `text-navy-200` body copy and bronze accents.

## Interaction & Motion

- Hover on cards: `transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover`.
- Buttons: subtle `active:scale-[0.98]`; never bouncy or aggressive motion.
- Product imagery: `group-hover:scale-105` with `duration-500` inside `overflow-hidden` container.
- Respect `prefers-reduced-motion` (handled globally in base CSS — do not add motion that bypasses it).

## Accessibility (non-negotiable)

- Meet WCAG AA contrast: never place `bronze-400`/`navy-400` text on light backgrounds for body copy.
- Every interactive icon-only control needs `aria-label`. Decorative SVGs get `aria-hidden="true"`.
- Keep the skip link and semantic landmarks (`header`, `nav`, `main`, `footer`) from the layout.
- Form fields: visible label (or `sr-only`), error text linked via `aria-describedby`, `aria-invalid` on error.
- Focus states are global (`focus-visible` bronze outline) — never remove outlines.

## Copy Tone

Confident, warm, service-oriented. Themes: honor, craftsmanship, reliability, giving back. Avoid aggressive military cliches, all-caps shouting, and gimmicky urgency.
