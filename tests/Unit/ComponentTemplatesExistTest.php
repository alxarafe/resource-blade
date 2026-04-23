<?php

declare(strict_types=1);

namespace Alxarafe\ResourceBlade\Tests\Unit;

use Alxarafe\ResourceBlade\BladeRenderer;
use Alxarafe\ResourceBlade\ViewHelper;
use Alxarafe\ResourceController\Component\AbstractField;
use Alxarafe\ResourceController\Component\Container\AbstractContainer;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ComponentTemplatesExistTest extends TestCase
{
    private BladeRenderer $renderer;

    protected function setUp(): void
    {
        $templatePaths = [__DIR__ . '/../../templates'];
        $cachePath = __DIR__ . '/../../var/cache';
        $this->renderer = new BladeRenderer($templatePaths, $cachePath);
    }

    public function testAllComponentTemplatesExist(): void
    {
        $baseDir = __DIR__ . '/../../vendor/alxarafe/resource-controller/src/Component';
        if (!is_dir($baseDir)) {
            $baseDir = __DIR__ . '/../../../resource-controller/src/Component';
        }
        
        $this->assertDirectoryExists($baseDir, "resource-controller Component directory not found");

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($baseDir));
        $missingTemplates = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = 'Alxarafe\\ResourceController\\Component\\' . str_replace('/', '\\', substr($file->getPathname(), strlen($baseDir) + 1, -4));
                
                if (class_exists($className)) {
                    $reflection = new ReflectionClass($className);
                    if (!$reflection->isAbstract() && ($reflection->isSubclassOf(AbstractField::class) || $reflection->isSubclassOf(AbstractContainer::class))) {
                        
                        // Instantiate with dummy parameters if required
                        try {
                            // Fields usually require $field, $label
                            if ($reflection->isSubclassOf(AbstractField::class)) {
                                $instance = $reflection->newInstance('dummy', 'Dummy');
                            } else {
                                // Containers might require different parameters
                                $instance = $reflection->newInstance('dummy');
                            }
                        } catch (\Throwable $e) {
                            // Skip components that can't be instantiated simply
                            continue;
                        }

                        $viewName = ViewHelper::getViewName($instance);
                        if ($viewName) {
                            $viewExists = $this->renderer->getViewFactory()->exists($viewName);
                            if (!$viewExists) {
                                $missingTemplates[] = "Missing template for {$className}: expected view '{$viewName}'";
                            }
                        }
                    }
                }
            }
        }

        $this->assertEmpty($missingTemplates, implode("\n", $missingTemplates));
    }
}
