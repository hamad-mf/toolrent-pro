# ToolRent Pro - Design & Engineering Standards

## Design Philosophy (Modern & Minimal)
- **Aesthetic:** Clean, professional, and "Airy". High use of whitespace.
- **Color Palette:** 
    - Primary: `#0d6efd` (Electric Blue)
    - Background: `#f8f9fa` (Light Gray)
    - Cards/Surface: `#ffffff` (White)
    - Text: `#212529` (Near Black)
- **Components:**
    - **Cards:** Subtle shadows (`box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);`), no borders.
    - **Corners:** Rounded (`border-radius: 0.5rem`).
    - **Buttons:** Flat design, subtle hover effects.
- **Typography:** Modern Sans-serif (Inter preferred).

## Technical Constraints
- **No Node.js/NPM:** All assets via CDN (Bootstrap 5, Alpine.js, Bootstrap Icons).
- **Backend:** Laravel 11 / PHP 8.2.
- **Database:** MySQL 8.0.
- **Multi-tenancy:** Single DB, `tenant_id` scoping.
