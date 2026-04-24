# alxarafe/resource-blade

> [!WARNING]
> **DEPRECATED AND OBSOLETE**
> 
> This package has been deprecated and its functionality has been natively integrated into [**resource-controller**](https://github.com/alxarafe/resource-controller) via the `DefaultRenderer` and static HTML templates. 
> You no longer need this package. Please remove it from your dependencies.

![PHP Version](https://img.shields.io/badge/PHP-8.2+-blueviolet?style=flat-square)
![CI](https://github.com/alxarafe/resource-blade/actions/workflows/ci.yml/badge.svg)
![Tests](https://github.com/alxarafe/resource-blade/actions/workflows/tests.yml/badge.svg)
![Static Analysis](https://img.shields.io/badge/static%20analysis-PHPStan%20%2B%20Psalm-blue?style=flat-square)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](https://github.com/alxarafe/resource-blade/issues)

**Blade adapter for alxarafe/resource-controller.**

Provides a `RendererContract` implementation using Laravel's Blade engine for server-side template rendering.

## Ecosystem

| Package | Purpose | Status |
|---|---|---|
| **[resource-controller](https://github.com/alxarafe/resource-controller)** | Core CRUD engine + UI components | ✅ Stable |
| **[resource-eloquent](https://github.com/alxarafe/resource-eloquent)** | Eloquent ORM adapter | ✅ Stable |
| **[resource-blade](https://github.com/alxarafe/resource-blade)** | Blade template renderer adapter | ✅ Stable |
| **[resource-twig](https://github.com/alxarafe/resource-twig)** | Twig template renderer adapter | 🚧 Coming soon |

## Installation

```bash
composer require alxarafe/resource-blade
```

This will also install `alxarafe/resource-controller` as a dependency.

## Usage

```php
use Alxarafe\ResourceBlade\BladeRenderer;

// Create a renderer with template paths and cache directory
$renderer = new BladeRenderer(
    templatePaths: [__DIR__ . '/views'],
    cachePath: __DIR__ . '/cache/views'
);

// Render a template
echo $renderer->render('products.index', [
    'title' => 'Products',
    'items' => $products,
]);

// Add additional template paths at runtime
$renderer->addTemplatePath(__DIR__ . '/module-views');
```

### Standalone (without Laravel)

This package works **without** the full Laravel framework. It only requires `illuminate/view` and `illuminate/filesystem`, which are lightweight components.

### With Laravel

If you're already using Laravel, you can still use this adapter to provide a consistent `RendererContract` interface that decouples your resource controllers from Laravel's view system.

## Development

### Docker

```bash
docker compose up -d
docker exec alxarafe-resources composer install
```

### Running the CI pipeline locally

```bash
bash bin/ci_local.sh
```

### Running tests only

```bash
bash bin/run_tests.sh
```

## License

GPL-3.0-or-later
