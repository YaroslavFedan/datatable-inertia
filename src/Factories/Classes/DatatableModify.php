<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use Illuminate\Database\Eloquent\Model;
use Dongrim\DatatableInertia\DatatableInertia;
use Dongrim\DatatableInertia\Factories\Classes\DatatableMethod;

class DatatableModify
{
    public static function get(DatatableInertia $datatableInertia, Model $item) :Model
    {
        if (DatatableMethod::get($datatableInertia, $datatableInertia->datatable, 'modify')) {
            return $datatableInertia->datatable->modify($item);
        }

        return $item;
    }
}
