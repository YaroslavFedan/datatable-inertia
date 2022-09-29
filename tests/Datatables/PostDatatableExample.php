<?php

namespace Dongrim\DatatableInertia\Tests\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Dongrim\DatatableInertia\DatatableInertia;
use Dongrim\DatatableInertia\Tests\Models\Post;

class PostDatatableExample extends DatatableInertia
{
    public function query(): Builder
    {
        return Post::query();
    }
}
