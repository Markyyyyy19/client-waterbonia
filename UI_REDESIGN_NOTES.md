# Waterbonia UI/UX Redesign

## What changed
- Rebuilt the authenticated app shell with a cleaner fixed desktop sidebar and responsive mobile offcanvas navigation.
- Simplified the top bar and removed the decorative admin image/badge treatment.
- Redesigned the dashboard hierarchy, KPI cards, recent-orders table, low-stock panel, and 7-day sales section.
- Redesigned the sign-in experience and removed all visible demo/example credentials.
- Redesigned the public laundry tracking experience for clearer search and status presentation.
- Updated global cards, tables, forms, filters, buttons, alerts, modals, pagination, status timeline, spacing, typography, and responsive behavior.
- Simplified employee-management wording (for example, "Employee Accounts" -> "Employees") while keeping the feature intact.
- Added consistent page-header treatment across existing screens.

## Build note
The source SCSS is in `resources/css/app.scss`. The redesigned stylesheet was also compiled into the production CSS asset currently referenced by `public/build/manifest.json`, so the packaged project includes the new design immediately.

If reinstalling frontend dependencies on another machine, run the normal project commands (`npm install` and `npm run build`) to regenerate platform-specific Vite/Rollup dependencies and build assets.
