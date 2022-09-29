<?php

namespace Dongrim\DatatableInertia;


class DatatableInertia extends DatatableInertiaAbstract
{
    /**
     * The name of the object containing all returned data from datatable
     *
     * @var string
     */
    public $datatableName;

    /**
     * The key in the request responsible for changing the number
     * of displayed elements on the page (only when rendering on the client side)
     *
     * @var string
     */
    public $perPageKey;

    /**
     * Parameter responsible for the number of displayed elements on the page by default
     *
     * @var int
     */
    public $itemsPerPage;

    /**
     * Parameter responsible for the server or client side rendering
     *
     * @var boolean
     */
    public $serverSide;

    /**
     * Eloquent query builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->datatable->query();
    }

    /**
     * If method columns not implemented in the derived class, will be returned all fields
     *
     * @return array
     */
    public function columns()
    {
        return [];
    }

    /**
     * Changing the value of returned fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function modify($data)
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
    public function guard($data)
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
    public function filters()
    {
        if (method_exists('filters', get_class($this->datatable))) {
            return $this->datatable->filters();
        }

        return [];
    }
}
