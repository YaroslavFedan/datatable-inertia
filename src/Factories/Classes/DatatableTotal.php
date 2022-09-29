<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

class DatatableTotal
{
    public static function get($data): int
    {
        if (isset($data['data']) && is_array($data['data'])) {
            return count($data['data']);
        }
        return 0;
    }
}
