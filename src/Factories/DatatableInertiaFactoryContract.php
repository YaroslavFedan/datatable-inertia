<?php

namespace Dongrim\DatatableInertia\Factories;

use Inertia\Response as InertiaResponse;
use Dongrim\DatatableInertia\DatatableInertia;

interface DatatableInertiaFactoryContract
{
    public static function build(InertiaResponse $response, DatatableInertia $datatableInertia): InertiaResponse;
}
