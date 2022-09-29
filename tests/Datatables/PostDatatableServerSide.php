<?php

namespace Dongrim\DatatableInertia\Tests\Datatables;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class PostDatatableServerSide extends PostDatatableExample
{
    public $datatableName = "testDatatable";
    public $serverSide = true;
    public $itemsPerPage = 10;

    public function guard($data): array
    {
        return [
            'edit' => $data->id,
            'delete' => $data->id
        ];
    }

    public function columns(): array
    {
        return ['id', 'title', 'body'];
    }

    public function filters(): array
    {
        return ['search', 'order', 'sort'];
    }

    public function modify($data): Model
    {
        $data->slug .= '-' . $data->id;
        $data->title .= ' ' . $data->id;
        $data->body = Str::substr($data->body, 0, 100);

        return $data;
    }
}
