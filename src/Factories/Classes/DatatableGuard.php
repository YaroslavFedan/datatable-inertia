<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use Illuminate\Database\Eloquent\Model;
use Dongrim\DatatableInertia\DatatableInertia;

class DatatableGuard
{
    public static function get(DatatableInertia $datatableInertia, Model $item): array|null
    {
        if (DatatableMethod::get($datatableInertia, $datatableInertia->datatable, 'guard')) {
            return $datatableInertia->datatable->guard($item);
        }

        return null;
    }
}
