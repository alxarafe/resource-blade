# alxarafe/resource-blade

> [!WARNING]
> **DEPRECADO Y OBSOLETO**
> 
> Este paquete ha sido deprecado y su funcionalidad se ha integrado de forma nativa en [**resource-controller**](https://github.com/alxarafe/resource-controller) a través de `DefaultRenderer` y plantillas HTML estáticas.
> Ya no necesitas este paquete. Por favor, elimínalo de tus dependencias.

![PHP Version](https://img.shields.io/badge/PHP-8.2+-blueviolet?style=flat-square)
![CI](https://github.com/alxarafe/resource-blade/actions/workflows/ci.yml/badge.svg)
![Tests](https://github.com/alxarafe/resource-blade/actions/workflows/tests.yml/badge.svg)
![Static Analysis](https://img.shields.io/badge/static%20analysis-PHPStan%20%2B%20Psalm-blue?style=flat-square)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](https://github.com/alxarafe/resource-blade/issues)

**Adaptador Blade para alxarafe/resource-controller.**

Proporciona una implementación de `RendererContract` usando el motor Blade de Laravel para renderizado de plantillas del lado del servidor.

## Ecosistema

| Paquete | Propósito | Estado |
|---|---|---|
| **[resource-controller](https://github.com/alxarafe/resource-controller)** | Motor CRUD central + componentes UI | ✅ Estable |
| **[resource-eloquent](https://github.com/alxarafe/resource-eloquent)** | Adaptador ORM Eloquent | ✅ Estable |
| **[resource-blade](https://github.com/alxarafe/resource-blade)** | Adaptador de renderizado con Blade | ✅ Estable |
| **[resource-twig](https://github.com/alxarafe/resource-twig)** | Adaptador de renderizado con Twig | 🚧 Próximamente |

## Instalación

```bash
composer require alxarafe/resource-blade
```

Esto también instalará `alxarafe/resource-controller` como dependencia.

## Uso

```php
use Alxarafe\ResourceBlade\BladeRenderer;

// Crear un renderer con las rutas de plantillas y directorio de caché
$renderer = new BladeRenderer(
    templatePaths: [__DIR__ . '/views'],
    cachePath: __DIR__ . '/cache/views'
);

// Renderizar una plantilla
echo $renderer->render('products.index', [
    'title' => 'Productos',
    'items' => $products,
]);

// Añadir rutas de plantillas adicionales en tiempo de ejecución
$renderer->addTemplatePath(__DIR__ . '/module-views');
```

### Uso autónomo (sin Laravel)

Este paquete funciona **sin** el framework Laravel completo. Solo requiere `illuminate/view` e `illuminate/filesystem`, que son componentes ligeros.

### Con Laravel

Si ya estás usando Laravel, puedes usar este adaptador para proporcionar una interfaz `RendererContract` consistente que desacopla tus controladores de recursos del sistema de vistas de Laravel.

## Desarrollo

### Docker

```bash
docker compose up -d
docker exec alxarafe-resources composer install
```

### Ejecutar el pipeline CI en local

```bash
bash bin/ci_local.sh
```

### Ejecutar solo los tests

```bash
bash bin/run_tests.sh
```

## Licencia

GPL-3.0-or-later
