<?php

namespace Dongrim\DatatableInertia\Exceptions;

class DatatableClassNotFound extends \Exception
{
    public static function make($class): self
    {
        return new static("Class {$class} not found");
    }
}
