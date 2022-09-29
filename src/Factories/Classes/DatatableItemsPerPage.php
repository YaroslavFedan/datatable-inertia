<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use Dongrim\DatatableInertia\DatatableInertia;

class DatatableItemsPerPage
{
    public static function get(DatatableInertia $datatableInertia, bool $serverSide): int
    {
        $itemsPerPage = DatatableProperty::get($datatableInertia, 'itemsPerPage');
        $perPageKey = DatatableProperty::get($datatableInertia, 'perPageKey');

        if ($serverSide && request()->has($perPageKey)) {
            return request()->$perPageKey;
        }

        return $itemsPerPage;
    }
}
