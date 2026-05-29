# ToolRent Pro — Power Tools Rental Shop Management System
### Complete Project Plan & Specification Document (No-Node.js Version)

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack](#2-tech-stack)
3. [System Architecture](#3-system-architecture)
4. [User Roles & Permissions](#4-user-roles--permissions)
5. [Features & Modules](#5-features--modules)
6. [Admin Panel — Super Admin](#6-admin-panel--super-admin)
7. [Admin Panel — Shop Admin](#7-admin-panel--shop-admin)
8. [All Screens & UI Pages](#8-all-screens--ui-pages)
9. [Database Schema (Overview)](#9-database-schema-overview)
10. [Design System](#10-design-system)
11. [Authentication & Login](#11-authentication--login)
12. [Hosting & Deployment](#12-hosting--deployment)
13. [Project Folder Structure](#13-project-folder-structure)
14. [Development Phases / Roadmap](#14-development-phases--roadmap)

---

## 1. Project Overview

**System Name:** ToolRent Pro *(customizable per shop from admin panel)*

**Purpose:** A multi-tenant, white-label power tools rental shop management system. The platform is distributed to multiple rental shops, each with their own configuration, branding, users, features, and data — all managed from a central Super Admin panel.

**Key Goals:**
- Full rental lifecycle management (booking → checkout → return → billing)
- Multi-tenant: one codebase, many shops, fully isolated data
- White-label: each shop can set their own name, logo, and color theme
- Modular features: enable/disable modules per shop
- Role-based access: control what each user can see and do
- Mobile-friendly UI with light/dark theme support

---

## 2. Tech Stack

### Frontend (Zero Node.js / No Build Step)
| Layer | Technology | Reason |
|---|---|---|
| Framework | Laravel Blade | Server-side rendering, tight backend integration |
| CSS Framework | Bootstrap 5.3 | Stable, responsive, no-build step required (via CDN/Local) |
| JS (Light) | Alpine.js | Lightweight reactivity via CDN (modals, dropdowns) |
| Icons | Bootstrap Icons / FontAwesome | Simple icon integration via CSS |
| Charts | Chart.js | Dashboard analytics (CDN) |
| Datepicker | Flatpickr | Lightweight, mobile-friendly (CDN) |
| Tables | DataTables.js | Searchable, sortable, paginated tables (CDN) |
| Notifications | SweetAlert2 | Clean, non-blocking alerts (CDN) |

### Backend
| Layer | Technology | Reason |
|---|---|---|
| Framework | Laravel 11 | Full-featured, batteries included |
| Language | PHP 8.2+ | Stable, widely hosted |
| Auth | Laravel Breeze (Blade/Livewire version) | Clean auth scaffolding |
| ORM | Eloquent | Readable, powerful DB layer |
| Queue | Laravel Queue (DB driver) | Background jobs (invoices, notifications) |
| PDF Generation | DomPDF (barryvdh/laravel-dompdf) | Invoices, receipts |
| File Upload | Laravel Storage | Tool photos, logos |
| Barcode/QR | SimpleSoftwareIO/simple-qrcode | Tool QR code generation |

### Database
| Layer | Technology |
|---|---|
| Primary DB | MySQL 8.0 |
| Cache | Laravel File Cache |
| Sessions | Database driver |

### Hosting
| Service | Platform | Cost |
|---|---|---|
| App + DB | Hostinger Shared / VPS | ~$3–6/mo |
| File Storage | Local disk | Included |
| SSL | Let's Encrypt | Free |

---

## 3. System Architecture

```
┌─────────────────────────────────────────────────┐
│                  SUPER ADMIN                    │
│  Manage Tenants · Feature Flags · Global Config │
└────────────────────┬────────────────────────────┘
                     │
        ┌────────────┼────────────┐
        ▼            ▼            ▼
  ┌──────────┐ ┌──────────┐ ┌──────────┐
  │ Shop A   │ │ Shop B   │ │ Shop C   │
  │ (Tenant) │ │ (Tenant) │ │ (Tenant) │
  └────┬─────┘ └────┬─────┘ └────┬─────┘
       │             │             │
  ┌────▼─────────────▼─────────────▼────┐
  │          Shared Laravel App          │
  │   Multi-Tenant via tenant_id column  │
  └──────────────────────────────────────┘
```

**Multi-Tenancy Approach:** Single database with `tenant_id` column on all tables. Each shop's data is scoped by tenant. Super Admin has access to all tenants.

---

## 4. User Roles & Permissions

### Role Hierarchy

```
Super Admin
    └── Shop Admin (per tenant)
            ├── Manager
            ├── Counter Staff
            └── Floor Staff
```

### Role Descriptions

| Role | Description |
|---|---|
| **Super Admin** | Platform owner. Manages all tenants, global settings, billing plans |
| **Shop Admin** | Owner/manager of a single shop. Full control over their shop |
| **Manager** | Can access reports, manage inventory, approve discounts |
| **Counter Staff** | Handles rentals, returns, billing, customer management |
| **Floor Staff** | Can view tool status, update condition, mark maintenance |

---

## 5. Features & Modules

*(Same as original - focuses on functionality)*

---

## 9. Database Schema (Overview)

*(Same as original)*

---

## 10. Design System

### Design Philosophy
- **Clean & Professional** — standard admin dashboard feel using Bootstrap 5.
- **No Node.js Build Step** — all assets are loaded via traditional `<link>` and `<script>` tags.
- **Mobile First** — utilizing Bootstrap's responsive grid system.

### Color System
- Primary colors are managed via inline CSS variables (`--bs-primary`) injected into the layout from the database.

### Components
- **Dashboard Layout:** Standard sidebar + navbar layout.
- **Modals:** Bootstrap Modals driven by Alpine.js for interactivity.
- **Alerts:** SweetAlert2 for success/error feedback.

---

## 11. Authentication & Login
- Standard Laravel Auth using Blade templates.
- Tenant-specific logos and colors on the login screen.

---

## 12. Hosting & Deployment
- Traditional FTP/Git deployment.
- **No `npm run build` required.** Just upload and run.

---

## 13. Project Folder Structure
- Assets (CSS/JS) located directly in `public/assets/` folder.

---

## 14. Development Phases / Roadmap
- **Phase 1:** Foundation (Laravel + DB + Multi-tenancy)
- **Phase 2:** Core Modules
- **Phase 3:** Admin & Reporting
- **Phase 4:** Polish & Deploy

---

## Summary

| Item | Decision |
|---|---|
| Backend | Laravel 11 (PHP 8.2) |
| Frontend | Blade + **Bootstrap 5** + Alpine.js |
| Build Tool | **None (No Node.js / NPM)** |
| Database | MySQL 8.0 |
| Multi-Tenancy | Single DB, tenant_id scoping |
