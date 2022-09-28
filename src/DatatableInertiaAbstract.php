<?php

namespace Dongrim\DatatableInertia;

use Inertia\Response as InertiaResponse;
use Illuminate\Database\Eloquent\Builder;
use Dongrim\DatatableInertia\Exceptions\ClassNotInstantiable;
use Dongrim\DatatableInertia\Exceptions\DatatableClassNotFound;
use Dongrim\DatatableInertia\Factories\DatatableInertiaFactory;

abstract class DatatableInertiaAbstract
{
    public object|string $datatable;

    /**
     * Eloquent datatable query builder
     *
     * @return Builder
     */
    abstract public function query(): Builder;

    /**
     * Check valid object or string type
     *
     * @param object|string $datatable
     *
     * @return self
     */
    public function with(object|string $datatable): self
    {
        if (is_string($datatable)) {
            if (!class_exists($datatable)) {
                throw DatatableClassNotFound::make($datatable);
            }

            $this->datatable = new $datatable();
        }

        if (is_object($datatable)) {
            $this->datatable = $datatable;
        }

        if (!is_subclass_of($this->datatable, self::class)) {
            throw ClassNotInstantiable::make($this->datatable, self::class);
        }

        return $this;
    }

    /**
     * @param InertiaResponse $response
     *
     * @return InertiaResponse
     */
    public function applyTo(InertiaResponse $response): InertiaResponse
    {
        return $response->with(
            $this->datatable->datatableName,
            DatatableInertiaFactory::build($this)
        );
    }
}
