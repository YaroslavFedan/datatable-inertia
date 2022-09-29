<?php

namespace Dongrim\DatatableInertia\Factories\Classes;

use Dongrim\DatatableInertia\DatatableInertia;
use Dongrim\DatatableInertia\Factories\Classes\DatatableGuard;
use Dongrim\DatatableInertia\Factories\Classes\DatatableTotal;
use Dongrim\DatatableInertia\Factories\Classes\DatatableModify;
use Dongrim\DatatableInertia\Factories\Classes\DatatableColumns;
use Dongrim\DatatableInertia\Factories\Classes\DatatableFilters;
use Dongrim\DatatableInertia\Factories\Classes\DatatableProperty;

class DatatableArrayResponse
{
    public static function get(DatatableInertia $datatableInertia, $fetchData, $itemsPerPage, $isServerSide)
    {
        $method = $isServerSide ? 'through' : 'map';
        $columns = DatatableColumns::get($datatableInertia);

        $result = $fetchData->$method(function ($item) use ($datatableInertia, $columns){
            $data = [];

            $item = DatatableModify::get($datatableInertia, $item);

            foreach ($columns as $column) {
                if (isset($item->$column)) {
                    $data[$column] = $item->$column;
                }
            }

            if ($quard = DatatableGuard::get($datatableInertia, $item)) {
                $data['can'] = $quard;
            }

            return $data;
        })->toArray();

        $data = $isServerSide ? $result : ['data' => $result];
       
        foreach (self::setParams($datatableInertia, $data, $itemsPerPage) as $key => $value) {
            if (!isset($data[$key])) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    private static function setParams($datatableInertia, $data, $itemsPerPage): array
    {
        return [
            'data' => [],
            'filters' => DatatableFilters::get($datatableInertia),
            'links' => [],
            'per_page' => $itemsPerPage,
            'total' => DatatableTotal::get($data),
            'current_page' => 1,
            'first_page_url' => null,
            'from' => null,
            'to' => null,
            'last_page' => null,
            'last_page_url' => null,
            'next_page_url' => null,
            'prev_page_url' => null,
            'path' => null,
            'server_side' => DatatableProperty::get($datatableInertia,'serverSide'),
        ];
    }
}
