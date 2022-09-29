<?php

namespace Dongrim\DatatableInertia\Factories;


use ReflectionMethod;
use ReflectionObject;
use Illuminate\Support\Facades\Schema;
use Inertia\Response as InertiaResponse;
use Dongrim\DatatableInertia\DatatableInertia;
use Dongrim\DatatableInertia\Factories\Classes\DatatableQuery;
use Dongrim\DatatableInertia\Factories\Classes\DatatableColumns;
use Dongrim\DatatableInertia\Factories\Classes\DatatablePerPage;
use Dongrim\DatatableInertia\Factories\Classes\DatatableProperty;
use Dongrim\DatatableInertia\Factories\Classes\DatatableItemsPerPage;
use Dongrim\DatatableInertia\Factories\Classes\DatatableQueryBuilder;
use Dongrim\DatatableInertia\Factories\Classes\DatatableArrayResponse;

class DatatableInertiaFactory implements DatatableInertiaFactoryContract
{
    public static function build(InertiaResponse $response, DatatableInertia $datatableInertia): InertiaResponse
    {
        $isServerSide = DatatableProperty::get($datatableInertia,'serverSide');
        $itemsPerPage = DatatableItemsPerPage::get($datatableInertia, $isServerSide);
        $fetchData = DatatableQuery::get($datatableInertia, $itemsPerPage, $isServerSide);

        return $response->with(
            DatatableProperty::get($datatableInertia,'datatableName'),
            DatatableArrayResponse::get($datatableInertia, $fetchData, $itemsPerPage, $isServerSide)
        );
    }
}
