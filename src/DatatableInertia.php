<?php

namespace Dongrim\DatatableInertia;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DatatableInertia extends DatatableInertiaAbstract
{
    /**
     * The name of the object containing all returned data from datatable
     * @var string
     */
    public string $datatableName;

    /**
     * The key in the request responsible for changing the number
     * of displayed elements on the page (only when rendering on the client side)
     */
    public string $perPageKey;

    /**
     * Parameter responsible for the number of displayed elements on the page by default
     */
    public int $itemPerPage;

    /**
     * Parameter responsible for the server or client side rendering
     */
    public bool $serverSide;


    public function __construct()
    {
        $this->datatableName = config('datatables.datatableKey', 'datatable');
        $this->itemPerPage = config('datatables.itemPerPage', 15);
        $this->perPageKey = config('datatables.perPageKey', 'per_page');
        $this->serverSide = config('datatables.serverSide', false);
    }

    /**
     * Eloquent query builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->datatable->query();
    }

    /**
     * If method columns not implemented in the derived class, will be returned all fields
     *
     * @return array
     */
    public function columns(): array
    {
        return [];
    }

    /**
     * Changing the value of returned fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function modify($data): Model
    {
        if (method_exists('modify', get_class($this->datatable))) {
            return $this->datatable->modify($data);
        }

        return $data;
    }

    /**
     * Adding access rights to a specific entry to change, delete, etc.
     *
     * @return array
     */
    public function guard($data): array
    {
        if (method_exists('guard', get_class($this->datatable))) {
            return $this->datatable->guard($data);
        }

        return $data;
    }

    /**
     * Adding filters and their values (server-side rendering only)
     *
     * @return array
     */
    public function filters(): array
    {
        if (method_exists('filters', get_class($this->datatable))) {
            return $this->datatable->filters();
        }

        return [];
    }
}
