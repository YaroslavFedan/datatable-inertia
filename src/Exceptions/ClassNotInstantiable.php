<?php

namespace Dongrim\DatatableInertia\Exceptions;

class ClassNotInstantiable extends \Exception
{
    public static function make($class, $instance): self
    {
        return new static("Class {$class} is not an instance of a class {$instance}");
    }
}
