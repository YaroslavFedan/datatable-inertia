<?php

namespace Dongrim\DatatableInertia\Factories;

use ReflectionMethod;
use Illuminate\Support\Facades\Schema;
use Dongrim\DatatableInertia\DatatableInertia;

class DatatableInertiaFactory implements DatatableInertiaFactoryContract
{
    public static function build(DatatableInertia $datatableInertia)
    {
        if ($datatableInertia->datatable->serverSide) {
            $method = 'through';
            $fetchData = $datatableInertia->datatable->query()
                ->paginate(self::getPerPage($datatableInertia))
                ->withQueryString();
        } else {
            $method = 'map';
            $fetchData = $datatableInertia->datatable->query()
                ->get();
        }

        return self::toArrayData($fetchData, $method, $datatableInertia);
    }


    private static function toArrayData($fetchData, string $method, DatatableInertia $datatableInertia)
    {
        $columns = self::getColumns($datatableInertia);

        $result = $fetchData->$method(function ($item) use ($columns, $datatableInertia) {
            $data = [];

            $item = self::getModify($datatableInertia, $item);

            foreach ($columns as $column) {
                if (isset($item->$column)) {
                    $data[$column] = $item->$column;
                }
            }

            if ($quard = self::getGuard($datatableInertia, $item)) {
                $data['can'] = $quard;
            }

            return $data;
        })->toArray();

        if (!$datatableInertia->datatable->serverSide) {
            $data['data'] = $result;
        } else {
            $data = $result;
        }

        foreach (self::setParams($datatableInertia, $data) as $key => $value) {
            if (!isset($data[$key])) {
                $data[$key] = $value;
            }
        }

        return $data;
    }


    private static function setParams($datatableInertia, $data): array
    {
        return [
            'data' => [],
            'filters' => self::getFilters($datatableInertia),
            'links' => [],
            'per_page' => self::getPerPage($datatableInertia),
            'total' => self::getTotal($data),
            'current_page' => 1,
            'first_page_url' => null,
            'from' => null,
            'to' => null,
            'last_page' => null,
            'last_page_url' => null,
            'next_page_url' => null,
            'prev_page_url' => null,
            'path' => null,
            'server_side' => $datatableInertia->datatable->serverSide
        ];
    }

    private static function getColumns(DatatableInertia $datatableInertia)
    {
        if (!self::childMethodExists($datatableInertia, $datatableInertia->datatable, 'columns')) {
            return Schema::getColumnListing($datatableInertia->datatable->query()->getModel()->getTable());
        }

        return $datatableInertia->datatable->columns();
    }

    private static function getFilters(DatatableInertia $datatableInertia)
    {
        $data = [];

        if (!self::childMethodExists($datatableInertia, $datatableInertia->datatable, 'filters')) {
            $filters = $datatableInertia->filters();
        } else {
            $filters = $datatableInertia->datatable->filters();
        }

        foreach ($filters as $filter) {
            $data[$filter] = request()->has($filter) ? request()->$filter : null;
        }

        return $data;
    }


    private static function getTotal($data): int
    {
        if (isset($data['data']) && is_array($data['data'])) {
            return count($data['data']);
        }
        return 0;
    }

    private static function getPerPage(DatatableInertia $datatableInertia)
    {
        $key = $datatableInertia->datatable->perPageKey;

        if ($datatableInertia->datatable->serverSide && request()->has($key)) {
            return request()->$key;
        }

        return $datatableInertia->datatable->itemPerPage;
    }

    private static function getModify(DatatableInertia $datatableInertia, $item)
    {
        if (self::childMethodExists($datatableInertia, $datatableInertia->datatable, 'modify')) {
            return $datatableInertia->datatable->modify($item);
        }

        return $item;
    }

    private static function getGuard(DatatableInertia $datatableInertia, $item)
    {
        if (self::childMethodExists($datatableInertia, $datatableInertia->datatable, 'guard')) {
            return $datatableInertia->datatable->guard($item);
        }

        return null;
    }

    private static function childMethodExists($baseClass, $childClass, $methodName)
    {
        if (!method_exists($childClass, $methodName)) {
            return false; // doesn't exist in the child class or base class
        }
        if (!method_exists($baseClass, $methodName)) {
            return true; // only exists on child class, as otherwise it would have returned above
        }
        // now to check if it is overloaded
        $baseMethod = new ReflectionMethod($baseClass, $methodName);
        $childMethod = new ReflectionMethod($childClass, $methodName);
        return $childMethod->class !== $baseMethod->class;
    }
}
