<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use Dongrim\DatatableInertia\DatatableInertia;

class DatatableItemsPerPage
{
    public static function get(DatatableInertia $datatableInertia, bool $serverSide): int
    {
        $itemsPerPage = DatatableProperty::get($datatableInertia, 'itemsPerPage');
        $perPageKey = DatatableProperty::get($datatableInertia, 'perPageKey');
        
        if ($serverSide && request()->filled($perPageKey)) {
            
            $requestItemsPerPage = request()->$perPageKey;

            if(filter_var($requestItemsPerPage, FILTER_VALIDATE_INT) !== false){

                $itemsPerPage = (int)$requestItemsPerPage !== 0 ? (int)$requestItemsPerPage : $itemsPerPage;
            }
        }

        return $itemsPerPage;
    }
}
