<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use Illuminate\Support\Facades\Schema;
use Dongrim\DatatableInertia\DatatableInertia;

class DatatableColumns
{
    public static function get(DatatableInertia $datatableInertia)
    {
        if (!DatatableMethod::get($datatableInertia, $datatableInertia->datatable, 'columns')) {
            return Schema::getColumnListing($datatableInertia->datatable->query()->getModel()->getTable());
        }

        return $datatableInertia->datatable->columns();
    }
}
