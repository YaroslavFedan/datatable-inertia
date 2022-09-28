<?php

namespace Dongrim\DatatableInertia\Tests\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Dongrim\DatatableInertia\Tests\Models\Post;

class PostDatatableWithColumns extends PostDatatableExample
{
    /**
     * Список возвращаемых колонок можно указать двумя способами
     * или используя метод select в запросе
     * или указав список полей в методе columns
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return Post::query()
            ->select('posts.id', 'posts.title', 'posts.body', 'slug');
    }

    public function columns(): array
    {
        return ['id', 'title', 'body'];
    }
}
