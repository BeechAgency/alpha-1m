# Alpha 1 Million

A lightweight WordPress theme built for the `alpha.org.au` project by Beech Agency.

## Overview

`Alpha 1 Million` is a custom theme based on the Underscores starter theme and extended with ACF flexible content support. It is designed to provide a clean, modular base for building content-driven pages with custom sections, responsive layout components, and editable ACF blocks.

## Key features

- Custom, theme-specific templates for page content and posts
- Flexible section-based page building using ACF JSON definitions
- Organized template parts in `template-parts/` for reusable layout components
- Asset pipeline with SCSS source files under `assets/scss/` and compiled CSS in `assets/css/`
- Custom navigation and responsive menu support
- Support for custom logos, featured images, threaded comments, and translation-ready strings
- Minimal starter styles so the theme is easy to extend and customize

## Project structure

- `style.css` — theme metadata, base CSS, and information for WordPress
- `functions.php` — theme setup, script and style registration, and theme support features
- `header.php`, `footer.php`, `index.php`, `page.php`, `single.php`, `search.php`, `404.php` — main theme templates
- `inc/` — reusable PHP utilities, template helpers, custom query logic, and theme support functions
- `template-parts/` — modular content sections and UI components used by theme templates
- `assets/scss/` — source stylesheets organized into partials, sections, components, and vendor code
- `assets/js/` — theme JavaScript, including core behavior and admin helpers
- `acf-json/` — saved ACF field groups for ACF JSON syncing and local development

## Development notes

- The theme is currently version `1.1.0` and targets compatibility with WordPress `5.4`.
- Requires PHP `5.6` or later.
- Text domain: `bfc`

## Customization

Update the theme metadata in `style.css` and adjust the file and template content to match your project branding.

Use the `assets/scss/` source files to extend or override styling, then compile to `assets/css/styles.css` as needed.

## License

This theme is released under the GNU General Public License v2 or later.
