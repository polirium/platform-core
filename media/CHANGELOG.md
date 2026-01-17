# Changelog

## [Unreleased] - 2026-01-17

### Added

- **Sidebar Auto-Close**: Implemented functionality to automatically close the sidebar when clicking outside the sidebar or file items.
- **Loading State**: Added `wire:loading` spinner to the sidebar to indicate when file details are being fetched.
- **Cropper CSS**: Imported `cropperjs/dist/cropper.css` to enable proper image editing functionality.

### Changed

- **Sidebar UI Overhaul**:
  - Completely redesigned `sidebar.blade.php` to function as a "Property Inspector".
  - Constrained preview image to `200px` height with `contain` fit.
  - Improved typography with muted labels and bold values.
  - Converted action links to full-width, styled block buttons.
- **Toolbar Layout**: Enforced consistent height (`38px`) for all toolbar elements (buttons, inputs) to fix alignment issues.
- **Asset Loading**:
  - Updated `MediaServiceProvider` to explicit load asset configuration.
  - Updated `MediaController` to use `Assets::loadCss` and `Assets::loadJs` correctly.
- **Empty State**: Centered the "No files" message both vertically and horizontally within the grid container.
- **Refactoring**: Consolidated SCSS styles into `media-manager.scss` and removed inline scripts from Blade templates.

### Fixed

- **Sidebar Visibility**: Removed legacy `right: -340px` CSS which caused the sidebar to be rendered off-screen even when active.
- **Image Editor**: Fixed broken cropping UI by importing missing styles.
