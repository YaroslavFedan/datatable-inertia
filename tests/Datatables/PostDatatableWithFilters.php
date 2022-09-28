<?php

namespace Dongrim\DatatableInertia\Tests\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Dongrim\DatatableInertia\Tests\Models\Post;

class PostDatatableWithFilters extends PostDatatableExample
{
    public function query(): Builder
    {
        return Post::query()
            ->select('posts.id', 'posts.slug', 'posts.title', 'posts.body', 'posts.author_id')
            ->when(request()->search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            })
            ->when(request()->sort, function ($query, $sort) {
                $query->withoutGlobalScopes();
                $query->orderBy('posts.' . $sort, request()->order ?? 'asc');
            });
    }

    public function filters(): array
    {
        return ['search', 'order', 'sort'];
    }
}
