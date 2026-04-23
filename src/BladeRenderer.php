<?php

declare(strict_types=1);

namespace Alxarafe\ResourceBlade;

use Alxarafe\ResourceController\Contracts\RendererContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

/**
 * BladeRenderer — RendererContract implementation using Laravel's Blade engine.
 *
 * Can be used standalone (without the full Laravel framework) or within
 * a Laravel application. Supports multiple template paths.
 */
class BladeRenderer implements RendererContract
{
    private Factory $viewFactory;
    private FileViewFinder $viewFinder;
    private Filesystem $filesystem;

    /**
     * @param string|string[] $templatePaths Directories where Blade templates are located.
     * @param string          $cachePath     Directory for compiled Blade templates.
     */
    public function __construct(string|array $templatePaths, string $cachePath)
    {
        $this->filesystem = new Filesystem();
        $paths = is_array($templatePaths) ? $templatePaths : [$templatePaths];

        if (!$this->filesystem->isDirectory($cachePath)) {
            $this->filesystem->makeDirectory($cachePath, 0755, true);
        }

        $compiler = new BladeCompiler($this->filesystem, $cachePath);
        
        $compiler->directive('renderComponent', function ($expression) {
            return "<?php echo \Alxarafe\ResourceBlade\ViewHelper::render(\$__env, {$expression}); ?>";
        });

        $engineResolver = new EngineResolver();
        $engineResolver->register('blade', function () use ($compiler) {
            return new CompilerEngine($compiler);
        });

        $this->viewFinder = new FileViewFinder($this->filesystem, $paths);
        $this->viewFactory = new Factory($engineResolver, $this->viewFinder, new \Illuminate\Events\Dispatcher());
    }

    #[\Override]
    public function render(string $template, array $data = []): string
    {
        return $this->viewFactory->make($template, $data)->render();
    }

    #[\Override]
    public function addTemplatePath(string $path): void
    {
        $this->viewFinder->addLocation($path);
    }

    /**
     * Get the underlying Blade View Factory for advanced usage.
     */
    public function getViewFactory(): Factory
    {
        return $this->viewFactory;
    }
}
