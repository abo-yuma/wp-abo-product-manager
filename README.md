# ABO YUMA Product Manager

A custom WordPress plugin built on top of the [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate).

It is specifically designed for Amway products and built exclusively for use with the ABO YUMA WordPress theme.  
The plugin automatically registers product pages.

⚠️ The development process follows a functionality-first mindset, followed by incremental refactoring.

## 📦 Admin Modules

- Product (CRUD)
- Product Property (CRUD)
- Product Brand (CRUD)
- Product Category (CRUD)

## 🌐 Public-Facing Features

- Product listing with jQuery DataTables
- Dynamic product detail page

## 🔁 Public-facing page routing

## 🔧 Installation

1. Place the plugin folder inside `wp-content/plugins/` directory.
2. Activate the plugin through the Plugins screen (Plugins > Installed Plugins).

## 🎨 Theme Dependency & Compatibility

⚠️ This plugin is developed primarily for use with the **ABO YUMA WordPress theme**.

It assumes that the theme already provides:

- Bootstrap (CSS & JS) integration
- Proper Bootstrap enqueue handling
- Base layout structure compatible with Bootstrap components

This plugin does **not** bundle or enqueue its own Bootstrap framework on the public-facing side. It only enqueues plugin-specific assets and dependencies (such as DataTables).

### ✅ Best Compatibility

The plugin works best when used with the **ABO YUMA theme**, where Bootstrap is already integrated and properly configured.

### ⚠️ Using With Other Themes

If activated with another theme that does not include Bootstrap:

- Frontend styling may appear broken or unstyled
- Layout components relying on Bootstrap classes may not render correctly

If support for other themes is required, Bootstrap integration must be handled either by the theme or manually extended within the plugin.

## 📌 TODO

- Integrate PHP CodeSniffer (PHPCS) for automated code quality checks
- Gradually align the codebase with WordPress PHP Coding Standards (WPCS)
- Refactor and improve Admin modules for better maintainability
