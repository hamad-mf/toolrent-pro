# ToolRent Pro — Modern Design System & Theming Spec

> **Purpose:** A complete, implementation-ready design specification to modernize the ToolRent Pro UI while keeping it minimalist. Includes a full **dark / light mode** system with a user-facing toggle. Written so an AI agent (Gemini) or developer can implement it without guesswork.
>
> **Hard constraint — No Node.js / No build step.** Everything uses Bootstrap 5.3 (CDN), CSS custom properties, and Alpine.js (CDN). No Tailwind, no SCSS build, no Vite. All theming is done with native CSS variables + Bootstrap 5.3's built-in color modes (`data-bs-theme`).
>
> **Design philosophy:** Clean, professional, calm. Lots of whitespace, soft shadows, rounded corners, one accent color (the tenant's primary color), restrained use of color. Modern but not flashy.

---

## Table of Contents

1. [Design Principles](#1-design-principles)
2. [Design Tokens (CSS Variables)](#2-design-tokens-css-variables)
3. [Dark / Light Mode System](#3-dark--light-mode-system)
4. [Theme Toggle Implementation](#4-theme-toggle-implementation)
5. [Tenant Primary Color Injection](#5-tenant-primary-color-injection)
6. [Layout & Shell](#6-layout--shell)
7. [Component Specs](#7-component-specs)
8. [Typography](#8-typography)
9. [Iconography & Imagery](#9-iconography--imagery)
10. [Motion & Interaction](#10-motion--interaction)
11. [Accessibility](#11-accessibility)
12. [File-by-File Implementation Plan](#12-file-by-file-implementation-plan)
13. [Reference Snippets](#13-reference-snippets)

---

## 1. Design Principles

1. **Minimalist first.** Reduce borders, use whitespace and subtle elevation instead. One accent color per tenant.
2. **Token-driven.** Every color, radius, shadow, and spacing value is a CSS variable. Dark mode = swap token values, not rewrite components.
3. **Bootstrap-native theming.** Use Bootstrap 5.3's `data-bs-theme="light|dark"` attribute on `<html>`. Bootstrap already restyles its components for dark mode — we layer tokens on top.
4. **Tenant-aware.** The accent (`--tr-primary`) comes from the tenant record; the rest of the palette is fixed light/dark.
5. **Zero build.** CDN + inline `<style>` token blocks + Alpine for the toggle. Nothing compiles.
6. **Consistency.** All cards, tables, buttons, and forms share the same radius, shadow, and spacing scale.

---

## 2. Design Tokens (CSS Variables)

Define all tokens on `:root` (light) and override under `[data-bs-theme="dark"]`. These map onto Bootstrap's own variables where possible so existing Bootstrap classes inherit them automatically.

```css
:root,
[data-bs-theme="light"] {
  /* Surfaces */
  --tr-bg:            #f6f7f9;   /* app background */
  --tr-surface:       #ffffff;   /* cards, navbar, sidebar */
  --tr-surface-2:     #f1f3f5;   /* subtle raised / table head */
  --tr-border:        #e9ecef;   /* hairline borders */

  /* Text */
  --tr-text:          #1f2329;   /* primary text */
  --tr-text-muted:    #6b7280;   /* secondary text */
  --tr-text-subtle:   #9aa1ab;   /* tertiary / placeholders */

  /* Accent (overridden per-tenant; see §5) */
  --tr-primary:       #4f46e5;   /* default indigo accent */
  --tr-primary-rgb:   79, 70, 229;
  --tr-primary-hover: #4338ca;
  --tr-on-primary:    #ffffff;

  /* Status */
  --tr-success:       #16a34a;
  --tr-warning:       #d97706;
  --tr-danger:        #dc2626;
  --tr-info:          #0ea5e9;

  /* Elevation */
  --tr-shadow-sm: 0 1px 2px rgba(16,24,40,.05);
  --tr-shadow-md: 0 4px 12px rgba(16,24,40,.08);
  --tr-shadow-lg: 0 12px 32px rgba(16,24,40,.12);

  /* Geometry */
  --tr-radius-sm: .5rem;
  --tr-radius:    .75rem;
  --tr-radius-lg: 1rem;

  /* Spacing scale (rem) */
  --tr-space-1: .25rem;
  --tr-space-2: .5rem;
  --tr-space-3: 1rem;
  --tr-space-4: 1.5rem;
  --tr-space-5: 2.5rem;

  /* Map onto Bootstrap */
  --bs-body-bg:        var(--tr-bg);
  --bs-body-color:     var(--tr-text);
  --bs-border-color:   var(--tr-border);
  --bs-primary:        var(--tr-primary);
  --bs-primary-rgb:    var(--tr-primary-rgb);
}

[data-bs-theme="dark"] {
  --tr-bg:            #0f1115;
  --tr-surface:       #171a21;
  --tr-surface-2:     #1f242d;
  --tr-border:        #2a2f3a;

  --tr-text:          #e6e8eb;
  --tr-text-muted:    #9aa1ab;
  --tr-text-subtle:   #6b7280;

  /* accent stays tenant-driven; lighten hover for contrast */
  --tr-primary-hover: #6366f1;
  --tr-on-primary:    #ffffff;

  --tr-success:       #22c55e;
  --tr-warning:       #f59e0b;
  --tr-danger:        #ef4444;
  --tr-info:          #38bdf8;

  --tr-shadow-sm: 0 1px 2px rgba(0,0,0,.4);
  --tr-shadow-md: 0 4px 12px rgba(0,0,0,.45);
  --tr-shadow-lg: 0 12px 32px rgba(0,0,0,.55);

  --bs-body-bg:      var(--tr-bg);
  --bs-body-color:   var(--tr-text);
  --bs-border-color: var(--tr-border);
}
```

> **Rule:** Components must reference tokens (`var(--tr-surface)`, `var(--tr-text)`, etc.), never hardcoded hex. This is what makes dark mode "just work."

---

## 3. Dark / Light Mode System

**Mechanism:** Bootstrap 5.3 ships native color modes via the `data-bs-theme` attribute on `<html>`. We support three user choices:

- `light`
- `dark`
- `auto` (follows the OS via `prefers-color-scheme`)

**Persistence:** Store the choice in `localStorage` under key `tr-theme`. Apply it **before paint** to avoid a flash of the wrong theme (FOUC).

**Resolution order:**
1. If `localStorage.tr-theme` is `light` or `dark` → use it.
2. If `auto` or unset → use `matchMedia('(prefers-color-scheme: dark)')`.

---

## 4. Theme Toggle Implementation

### 4.1 Anti-FOUC script (must be in `<head>`, BEFORE the stylesheet renders)

Place this as the **first** script in `<head>` of both `layouts/admin.blade.php` and `layouts/app.blade.php`:

```html
<script>
  (function () {
    const stored = localStorage.getItem('tr-theme') || 'auto';
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = stored === 'auto' ? (systemDark ? 'dark' : 'light') : stored;
    document.documentElement.setAttribute('data-bs-theme', theme);
  })();
</script>
```

### 4.2 Toggle control (in the navbar, near the user menu)

Use Alpine.js (already loaded via CDN). A simple 3-state cycle or a dropdown:

```html
<div x-data="themeToggle()" class="me-3">
  <button class="btn btn-sm btn-light border-0 rounded-circle"
          @click="cycle()" :title="'Theme: ' + mode" style="width:36px;height:36px;">
    <i class="bi" :class="icon"></i>
  </button>
</div>

<script>
  function themeToggle() {
    return {
      mode: localStorage.getItem('tr-theme') || 'auto',
      get icon() {
        return this.mode === 'dark'  ? 'bi-moon-stars-fill'
             : this.mode === 'light' ? 'bi-sun-fill'
             : 'bi-circle-half';
      },
      apply() {
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = this.mode === 'auto' ? (systemDark ? 'dark' : 'light') : this.mode;
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('tr-theme', this.mode);
      },
      cycle() {
        this.mode = this.mode === 'light' ? 'dark' : this.mode === 'dark' ? 'auto' : 'light';
        this.apply();
      },
    };
  }
  // Keep "auto" in sync with OS changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if ((localStorage.getItem('tr-theme') || 'auto') === 'auto') {
      document.documentElement.setAttribute('data-bs-theme',
        window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    }
  });
</script>
```

Icons: `bi-sun-fill` (light), `bi-moon-stars-fill` (dark), `bi-circle-half` (auto). Bootstrap Icons are already loaded.

### 4.3 Where to place the toggle
- **Admin shell:** top navbar, immediately left of the user avatar pill.
- **Login / app shell:** top-right corner of the page.

---

## 5. Tenant Primary Color Injection

The accent color is per-tenant (resolves the white-label gap noted in `COMPLETION_TASKS.md` TASK-B1). Inject it into `:root` after the token block so it overrides the default accent in **both** light and dark modes.

In `layouts/admin.blade.php` (and `layouts/app.blade.php` on tenant subdomains):

```blade
@php
    $accent = session('tenant_primary_color', '#4f46e5');
    // derive an RGB triple for rgba() usage
    [$r,$g,$b] = sscanf($accent, "#%02x%02x%02x");
@endphp
<style>
  :root {
    --tr-primary: {{ $accent }};
    --tr-primary-rgb: {{ $r }}, {{ $g }}, {{ $b }};
    --bs-primary: {{ $accent }};
    --bs-primary-rgb: {{ $r }}, {{ $g }}, {{ $b }};
    --bs-link-color: {{ $accent }};
  }
  /* Make Bootstrap utilities follow the tenant accent */
  .btn-primary {
    --bs-btn-bg: var(--tr-primary);
    --bs-btn-border-color: var(--tr-primary);
    --bs-btn-hover-bg: var(--tr-primary-hover);
    --bs-btn-hover-border-color: var(--tr-primary-hover);
    --bs-btn-active-bg: var(--tr-primary-hover);
    color: var(--tr-on-primary);
  }
  .text-primary { color: var(--tr-primary) !important; }
  .bg-primary   { background-color: var(--tr-primary) !important; }
</style>
```

> If `custom_css` is implemented (TASK-B3), inject it AFTER this block so tenant overrides win. Treat `custom_css` as trusted-admin-only.

---

## 6. Layout & Shell

### 6.1 Admin shell (sidebar + topbar)
- **Sidebar:** fixed left, width `260px` (desktop), `var(--tr-surface)` background, hairline right border `var(--tr-border)`, no heavy shadow. Collapses to an off-canvas drawer on mobile (Bootstrap Offcanvas).
- **Nav items:** `--tr-radius-sm`, `var(--tr-space-2) var(--tr-space-3)` padding, icon + label. Hover = `rgba(var(--tr-primary-rgb), .08)` background. Active = `rgba(var(--tr-primary-rgb), .12)` background + `var(--tr-primary)` text + a 3px left accent bar.
- **Section labels:** uppercase, `--tr-text-subtle`, letter-spacing `.05em`, small.
- **Topbar:** sticky, `var(--tr-surface)`, hairline bottom border, contains: mobile menu button, page title (or breadcrumb), spacer, theme toggle, notifications (optional), user avatar pill.
- **Main content:** max-width container with generous `var(--tr-space-4)` padding; page header row with title + primary action button.

### 6.2 Grid & spacing
- Use Bootstrap grid. Card gutters `g-4`. Section vertical rhythm `var(--tr-space-4)`.

---

## 7. Component Specs

### 7.1 Cards
- Background `var(--tr-surface)`, border `none`, radius `--tr-radius`, shadow `--tr-shadow-sm` (hover `--tr-shadow-md` for interactive cards).
- Card header: transparent background, no bottom border, bold title, optional action on the right.

### 7.2 Stat / KPI cards
- Replace the current solid-color cards (`bg-primary`, `bg-success`, etc.) with **neutral surface cards** that use a small colored icon chip:
  - Icon chip: 40px rounded square, `rgba(var(--tr-primary-rgb), .12)` bg, accent-colored icon.
  - Big number in `--tr-text`, label in `--tr-text-muted`.
- This reads cleaner in both light and dark modes and avoids harsh saturated blocks.

### 7.3 Tables
- Header row: `var(--tr-surface-2)` background, `--tr-text-muted`, uppercase small.
- Rows: hairline `var(--tr-border)` dividers, hover `rgba(var(--tr-primary-rgb), .04)`.
- Wrap in a card; footer holds pagination.
- Status badges: soft style — `rgba(status, .12)` background + status-colored text + matching subtle border. (Already used; keep and tokenize.)

### 7.4 Buttons
- Radius `--tr-radius-sm`, font-weight 500, padding `.5rem 1rem`.
- Primary = accent; Secondary = `var(--tr-surface-2)` bg + `var(--tr-text)` text; Subtle/ghost for table actions = transparent + border `var(--tr-border)`.
- Destructive = `--tr-danger` text on hover-fill.

### 7.5 Forms
- Inputs: `var(--tr-surface)` bg (dark mode handled by Bootstrap), border `var(--tr-border)`, radius `--tr-radius-sm`, focus ring `0 0 0 3px rgba(var(--tr-primary-rgb), .25)`.
- Labels: `--tr-text`, medium weight. Help text: `--tr-text-muted`.
- Use Bootstrap floating labels or top-aligned labels consistently (pick one).

### 7.6 Modals / dialogs
- Bootstrap modal with `--tr-radius-lg`, `--tr-shadow-lg`, `var(--tr-surface)` bg. Driven by Alpine where interactivity is needed.

### 7.7 Alerts & toasts
- Inline alerts: soft tinted backgrounds (`rgba(status,.12)`) + status text + no harsh borders.
- Prefer SweetAlert2 toasts for transient success/error (ties into `COMPLETION_TASKS.md` TASK-F2). Configure SweetAlert2 with `theme`-aware colors using the tokens.

### 7.8 Empty states
- Centered muted icon (`display-4`, `opacity-25`), short heading, one-line description, primary CTA. (Pattern already present — keep and standardize.)

---

## 8. Typography

- **Font:** Inter (already loaded via Google Fonts) with system fallback stack.
- **Base size:** `0.9375rem` (15px) for dense admin readability.
- **Scale:** h1 `1.5rem`/700, h2 `1.25rem`/700, h3 `1.125rem`/600, body `0.9375rem`/400, small `0.8125rem`.
- **Line height:** 1.5 body, 1.25 headings.
- **Weights:** 400 body, 500 labels/buttons, 600–700 headings. Avoid 300 for body text in dark mode (too thin).

---

## 9. Iconography & Imagery

- **Icons:** Bootstrap Icons (already loaded). One icon set only — no FontAwesome mixing.
- **Logo:** tenant logo in sidebar header + login; fallback to `bi-tools` accent icon.
- **Tool images:** square thumbnails, `--tr-radius-sm`, object-fit cover, neutral placeholder when missing (ties into `COMPLETION_TASKS.md` TASK-C1).
- **Avatars:** initials in an accent-tinted circle (pattern already present).

---

## 10. Motion & Interaction

- Transitions: `150ms ease` for color/background/box-shadow on interactive elements. No layout-shifting animations.
- Hover elevation on clickable cards (`--tr-shadow-sm` → `--tr-shadow-md`).
- Respect `prefers-reduced-motion: reduce` — disable non-essential transitions.
- Sidebar drawer slide-in on mobile via Bootstrap Offcanvas.

---

## 11. Accessibility

- **Contrast:** Verify text/background pairs meet WCAG AA (4.5:1 body, 3:1 large). The dark `--tr-text` (#e6e8eb) on `--tr-surface` (#171a21) and light pairings are chosen for this, but **re-verify the tenant accent** against `--tr-on-primary` — some tenant colors may need dark text on the button instead of white. Consider computing button text color from accent luminance.
- **Focus visible:** never remove focus outlines; use the accent focus ring.
- **Toggle button:** include `aria-label` describing current mode; announce changes politely.
- **Color independence:** status is conveyed by text + icon, not color alone.
- **Keyboard:** all actions (delete, return, toggle) reachable and operable by keyboard.
- **Note:** Full WCAG validation requires manual testing with assistive technologies and expert review; this spec sets the baseline, not a guarantee.

---

## 12. File-by-File Implementation Plan

1. **Create `resources/views/partials/theme-head.blade.php`** — contains the anti-FOUC script (§4.1) + the full token `<style>` block (§2). `@include` it in the `<head>` of both layouts, before the Bootstrap stylesheet link for the script and after for tokens (tokens can also live in a static CSS file at `public/assets/css/theme.css`).
2. **Create `public/assets/css/theme.css`** — the token definitions from §2 (so it's cacheable and out of the Blade file). Link it after Bootstrap in both layouts.
3. **Create `resources/views/partials/theme-toggle.blade.php`** — the Alpine toggle (§4.2). Include it in both layout navbars.
4. **Edit `resources/views/layouts/admin.blade.php`:**
   - Add anti-FOUC script as first `<head>` script.
   - Link `theme.css` after Bootstrap.
   - Add tenant accent injection block (§5).
   - Replace the inline hardcoded `#0d6efd`/`#f8f9fa`/`#ffffff` style rules with token references.
   - Convert solid-color KPI cards to neutral surface + icon-chip style (§7.2).
   - Add the theme toggle partial to the navbar.
   - Make the sidebar an Offcanvas on mobile.
5. **Edit `resources/views/layouts/app.blade.php`** (auth/login shell): same anti-FOUC script, theme.css, tenant branding (§5 + `COMPLETION_TASKS.md` TASK-B2), and toggle.
6. **Edit index views** (`tools`, `customers`, `rentals`, `categories`, tenants, users): apply tokenized table styling (§7.3) — mostly automatic once tokens drive `--bs-` variables.
7. **Edit `resources/views/shop-admin/dashboard.blade.php`:** restyle KPI cards (§7.2); make Chart.js colors theme-aware by reading CSS variables in JS (see §13.3).
8. **SweetAlert2 theming** (optional, with TASK-F2): create a small JS helper that builds confirm/toast dialogs using the token palette and current `data-bs-theme`.

> Keep all new CSS/JS in `public/assets/` or inline Blade partials. **No npm, no Vite, no SCSS compilation.**

---

## 13. Reference Snippets

### 13.1 Neutral KPI card (replaces solid `bg-primary` card)
```html
<div class="card h-100">
  <div class="card-body d-flex align-items-center justify-content-between">
    <div>
      <div class="text-uppercase fw-semibold small" style="color:var(--tr-text-muted)">Total Tools</div>
      <div class="fs-2 fw-bold" style="color:var(--tr-text)">{{ $stats['total_tools'] }}</div>
    </div>
    <span class="d-inline-flex align-items-center justify-content-center"
          style="width:44px;height:44px;border-radius:var(--tr-radius-sm);
                 background:rgba(var(--tr-primary-rgb),.12);color:var(--tr-primary)">
      <i class="bi bi-tools fs-5"></i>
    </span>
  </div>
</div>
```

### 13.2 Sidebar active item with accent bar
```css
.sidebar .nav-link.active {
  color: var(--tr-primary);
  background: rgba(var(--tr-primary-rgb), .12);
  position: relative;
}
.sidebar .nav-link.active::before {
  content: "";
  position: absolute; left: 0; top: 8px; bottom: 8px;
  width: 3px; border-radius: 0 3px 3px 0;
  background: var(--tr-primary);
}
```

### 13.3 Theme-aware Chart.js colors
```js
function trToken(name) {
  return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}
const textColor   = trToken('--tr-text-muted');
const gridColor   = trToken('--tr-border');
const accentColor = trToken('--tr-primary');
// Re-render or update chart options when data-bs-theme changes:
new MutationObserver(() => { /* chart.update() with refreshed tokens */ })
  .observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
```

### 13.4 Required `<head>` order (both layouts)
```
1. <meta> tags + <title>
2. Anti-FOUC <script> (§4.1)          ← FIRST script, no defer
3. Bootstrap CSS (CDN)
4. Bootstrap Icons (CDN)
5. public/assets/css/theme.css (tokens)
6. Tenant accent <style> block (§5)
7. Alpine.js (CDN, defer)
8. Other libs (SweetAlert2, Flatpickr, Chart.js as needed)
```

---

## Summary of changes this design introduces

| Area | Before | After |
|---|---|---|
| Theming | Hardcoded hex colors | CSS-variable token system |
| Modes | Light only | Light / Dark / Auto with toggle + persistence |
| Tenant color | Stored but unused | Injected into `--tr-primary` + Bootstrap vars |
| KPI cards | Saturated solid blocks | Neutral surfaces with accent icon chips |
| Sidebar | Static blue accents | Tokenized accent + active bar + mobile offcanvas |
| Tables | Plain Bootstrap | Tokenized, soft, theme-aware |
| Alerts | Native + success-only | Soft tinted + SweetAlert2 toasts (both states) |
| Build step | None (good) | Still none — CDN + CSS vars + Alpine only |
