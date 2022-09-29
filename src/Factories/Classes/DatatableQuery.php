<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use Dongrim\DatatableInertia\DatatableInertia;

class DatatableQuery
{
    public static function get(DatatableInertia $datatableInertia, int $itemsPerPage, bool $isServerSide): mixed
    {
        if ($isServerSide) {
            return $datatableInertia->datatable
                ->query()
                ->paginate($itemsPerPage)
                ->withQueryString();
        } else {
            return $datatableInertia->datatable
                ->query()
                ->get();
        }
    }
}
