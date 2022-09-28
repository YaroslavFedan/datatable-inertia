<?php

namespace Dongrim\DatatableInertia\Tests\Datatables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostDatatableWithModify extends PostDatatableExample
{
    public function modify($data): Model
    {
        $data->slug .= '-' . $data->id;
        $data->title .= ' ' . $data->id;
        $data->body = Str::substr($data->body, 0, 100);

        return $data;
    }
}
