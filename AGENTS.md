# Project Agent Rules

## School Page Design Standard

For all current and future school administration pages, follow the Teacher Registration page as the canonical application design unless the user explicitly requests a different design.

Apply this standard automatically even when the user does not repeat the instruction.

### Required Components

- Use `<x-school.list-header>` for page titles, breadcrumbs, search, Restore, Filter, Export, and primary actions.
- Use `<x-school.data-table>` and the existing table components for list tables.
- Use `<x-modal.form>` for create, edit, filter, and generator forms.
- Use the existing `<x-input.*>` components for search fields, controls, floating labels, photo fields, and dropdowns.
- Use `<x-button.primary>` and `<x-button.secondary>` for actions.
- Use `<x-dropdown>` and `<x-dropdown.item>` for export menus.
- Reuse existing action components when they fit; otherwise match their compact, centered edit/delete presentation.

### Behavior Preservation

- Preserve controllers, routes, APIs, validation, business logic, JavaScript behavior, element IDs, and user workflows unless a functional change is explicitly requested.
- Before replacing native selects with `<x-input.dropdown-select>`, inspect all JavaScript that depends on `options`, `selectedIndex`, or option `data-*` attributes.
- Add a component-aware JavaScript adapter when necessary so cascading dropdowns, edit mode, quick-create modals, metadata, and selected labels continue working.
- Keep asynchronous dependency order correct when edit forms populate class, group, section, session, exam, or subject fields.
- Do not remove specialized controls or report layouts when no equivalent shared component exists. Apply the component standard to the surrounding application shell instead.

### Responsive Design

- Preserve the same bordered, compact visual language on desktop, tablet, and mobile.
- Keep tables as tables on small screens and place horizontal scrolling inside the table frame; do not convert rows to cards unless explicitly requested.
- Prevent individual columns from expanding the page. Use fixed layouts, balanced widths, one-line cells, and the shared hidden per-cell scrolling behavior.
- Keep modal fields responsive with one column on small screens and balanced columns on medium and large screens.

### Implementation Procedure

1. Inspect the page markup, related partials, component APIs, JavaScript selectors, routes, and API dependencies before editing.
2. Replace only the presentation structure while preserving functionality.
3. Remove obsolete page-level global CSS that overrides shared tables, inputs, buttons, or modals.
4. Update dynamically generated rows and controls to match the shared component design and escape displayed API data where practical.
5. Verify static and dynamically generated element IDs used by JavaScript.
6. Run `php artisan view:clear` and `php artisan view:cache`.
7. Parse changed inline JavaScript with `node --check`.
8. Run `npm run build`.
9. Run relevant tests when available, plus `git diff --check`.
10. Attempt a live local-page verification when browser access is available, without submitting destructive or external side-effect actions.

### Scope Safety

- Do not alter unrelated dirty worktree changes.
- Do not change business rules merely to complete a visual refactor.
- Keep printable documents, transcripts, PDFs, and other specialized output formats intact unless the user explicitly asks to redesign them.
