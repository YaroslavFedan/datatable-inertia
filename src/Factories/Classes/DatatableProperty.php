<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use ReflectionObject;
use Dongrim\DatatableInertia\DatatableInertia;

class DatatableProperty
{
    public static function get(DatatableInertia $datatableInertia, string $propertyName): mixed
    {
        $childClass = $datatableInertia->datatable;
        $childReflectionClass = new ReflectionObject($childClass);
        $properties = $childReflectionClass->getDefaultProperties();

        if (!isset($properties[$propertyName]) || !$properties[$propertyName]) {
            return $childClass->$propertyName;
        } else {
            return $properties[$propertyName];
        }
    }
}
