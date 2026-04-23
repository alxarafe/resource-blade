<?php

declare(strict_types=1);

namespace Alxarafe\ResourceBlade;

use Alxarafe\ResourceController\Component\AbstractField;
use Alxarafe\ResourceController\Component\Container\AbstractContainer;
use Illuminate\View\Factory;

class ViewHelper
{
    public static function getViewName(object $component): string
    {
        $viewName = '';
        if ($component instanceof AbstractField) {
            $name = $component->getComponent();
            if ($name === 'text') {
                $name = 'input';
            } elseif ($name === 'select2') {
                $name = 'select';
            }
            $viewName = 'component.form.' . $name;
        } elseif ($component instanceof AbstractContainer) {
            $viewName = 'component.container.' . $component->getContainerType();
        }
        return $viewName;
    }

    public static function render(Factory $env, object $component, array $extraData = []): string
    {
        $viewName = self::getViewName($component);

        if (!$viewName) {
            return '';
        }

        $data = [];
        if ($component instanceof AbstractField || $component instanceof AbstractContainer) {
            $data = $component->jsonSerialize();
        }
        if ($component instanceof AbstractContainer) {
            $data['container'] = $component;
        }
        
        $data = array_merge($data, $extraData);
        
        if (!isset($data['attributes']) && class_exists(\Illuminate\View\ComponentAttributeBag::class)) {
            $data['attributes'] = new \Illuminate\View\ComponentAttributeBag($data['options'] ?? []);
        }

        return $env->make($viewName, $data)->render();
    }
}
