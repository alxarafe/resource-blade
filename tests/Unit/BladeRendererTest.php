<?php

declare(strict_types=1);

namespace Alxarafe\ResourceBlade\Tests\Unit;

use Alxarafe\ResourceBlade\BladeRenderer;
use Alxarafe\ResourceController\Contracts\RendererContract;
use PHPUnit\Framework\TestCase;

class BladeRendererTest extends TestCase
{
    private string $templatePath;
    private string $cachePath;

    protected function setUp(): void
    {
        $this->templatePath = sys_get_temp_dir() . '/blade_test_templates_' . uniqid();
        $this->cachePath = sys_get_temp_dir() . '/blade_test_cache_' . uniqid();
        mkdir($this->templatePath, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->templatePath);
        $this->removeDir($this->cachePath);
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($dir);
    }

    public function testImplementsRendererContract(): void
    {
        $renderer = new BladeRenderer($this->templatePath, $this->cachePath);
        $this->assertInstanceOf(RendererContract::class, $renderer);
    }

    public function testRenderSimpleTemplate(): void
    {
        file_put_contents($this->templatePath . '/hello.blade.php', 'Hello, {{ $name }}!');

        $renderer = new BladeRenderer($this->templatePath, $this->cachePath);
        $result = $renderer->render('hello', ['name' => 'World']);

        $this->assertSame('Hello, World!', $result);
    }

    public function testRenderWithBladeDirectives(): void
    {
        file_put_contents(
            $this->templatePath . '/list.blade.php',
            '@foreach($items as $item){{ $item }},@endforeach'
        );

        $renderer = new BladeRenderer($this->templatePath, $this->cachePath);
        $result = $renderer->render('list', ['items' => ['a', 'b', 'c']]);

        $this->assertSame('a,b,c,', $result);
    }

    public function testAddTemplatePath(): void
    {
        $extraPath = sys_get_temp_dir() . '/blade_extra_' . uniqid();
        mkdir($extraPath, 0755, true);
        file_put_contents($extraPath . '/extra.blade.php', 'Extra template');

        $renderer = new BladeRenderer($this->templatePath, $this->cachePath);
        $renderer->addTemplatePath($extraPath);
        $result = $renderer->render('extra');

        $this->assertSame('Extra template', $result);
        $this->removeDir($extraPath);
    }

    public function testRenderWithMultipleInitialPaths(): void
    {
        $secondPath = sys_get_temp_dir() . '/blade_second_' . uniqid();
        mkdir($secondPath, 0755, true);
        file_put_contents($this->templatePath . '/first.blade.php', 'First');
        file_put_contents($secondPath . '/second.blade.php', 'Second');

        $renderer = new BladeRenderer([$this->templatePath, $secondPath], $this->cachePath);

        $this->assertSame('First', $renderer->render('first'));
        $this->assertSame('Second', $renderer->render('second'));

        $this->removeDir($secondPath);
    }

    public function testGetViewFactory(): void
    {
        $renderer = new BladeRenderer($this->templatePath, $this->cachePath);
        $this->assertInstanceOf(\Illuminate\View\Factory::class, $renderer->getViewFactory());
    }

    public function testCacheDirectoryIsCreated(): void
    {
        $newCache = sys_get_temp_dir() . '/blade_new_cache_' . uniqid();
        $this->assertDirectoryDoesNotExist($newCache);

        new BladeRenderer($this->templatePath, $newCache);

        $this->assertDirectoryExists($newCache);
        $this->removeDir($newCache);
    }
}
