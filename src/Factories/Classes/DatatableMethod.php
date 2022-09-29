<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use ReflectionMethod;

class DatatableMethod
{
    public static function get($baseClass, $childClass, $methodName)
    {
        if (!method_exists($childClass, $methodName)) {
            return false; // doesn't exist in the child class or base class
        }
        if (!method_exists($baseClass, $methodName)) {
            return true; // only exists on child class, as otherwise it would have returned above
        }
        // now to check if it is overloaded
        $baseMethod = new ReflectionMethod($baseClass, $methodName);
        $childMethod = new ReflectionMethod($childClass, $methodName);
        return $childMethod->class !== $baseMethod->class;
    }
}
