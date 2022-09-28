<?php

namespace Dongrim\DatatableInertia\Tests\Datatables;

class PostDatatableWithGuard extends PostDatatableExample
{
    public function guard($data): array
    {
        return [
            'edit' => $data->id,
            'delete' => $data->id
        ];
    }
}
