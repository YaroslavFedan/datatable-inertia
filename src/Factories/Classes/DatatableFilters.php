<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use Dongrim\DatatableInertia\DatatableInertia;

class DatatableFilters
{
    public static function get(DatatableInertia $datatableInertia)
    {
        $data = [];

        if (!DatatableMethod::get($datatableInertia, $datatableInertia->datatable, 'filters')) {
            $filters = $datatableInertia->filters();
        } else {
            $filters = $datatableInertia->datatable->filters();
        }

        foreach ($filters as $filter) {
            $data[$filter] = request()->has($filter) ? request()->$filter : null;
        }

        return $data;
    }
}
