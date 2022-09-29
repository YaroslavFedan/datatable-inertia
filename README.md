# Datatable-Inertia

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This package provides a DataTables-like experience for Inertia.js with support for searching, filtering, sorting and pagination.

## Laravel compatibility

| Laravel                    | datatable-inertia |
| :------------------------- | :---------------- |
| 6.0-9.x (PHP 7.1 required) | 0.0.x             |

## Installation

**Install the package via composer.**

```bash
composer require dongrim/datatable-inertia
```

## Config Files

**In order to edit the default configuration you may execute:**

```bash
php artisan vendor:publish --provider="Dongrim\DatatableInertia\DatatableInertiaServiceProvider"
```

## Usage

- ### **Generate a datatable**

```bash
php artisan datatable:make SomeDatatable
```

By default, the command generates the `SomeDatatable` class in the \App\Datatables directory <br>
If you want to change the destination path of a class, you can use one of these methods:

1. Specify a new path in the file /config/datatables.php

```php
'basePath' => '\App\Datatables'
```

2. Run command `php artisan datatable:make` without specifying the name of the generated class and answer questions.

- ### **How to use in Controller**

To generate data, a `table` macro has been created for Inertia\Response.<br>
You can take advantage of dependency injection

```php
public function index(PostDatatable $datatable)
{
    return Inertia::render('Post/Index')->table($datatable);
}
```

Or just give the classpath

```php
public function index()
{
    return Inertia::render('Post/Index')->table(PostDatatable::class); // '\App\Datatables\PostDatatable'
}
```

If you need to pass additional data, use the usual method of passing data in InertiaJs

```php
public function index(PostDatatable $datatable)
{
    return Inertia::render('Post/Index', ['data' => 'some data'])->table($datatable);
}
```

- ### **How to use Datatable class**

By default datatable class is generated in minimal configuration

For example

```php
namespace \App\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Dongrim\DatatableInertia\DatatableInertia;

class PostDatatable extends DatatableInertia
{
    /**
     * Eloquent datatable query builder
     *
     * @return Builder
     */
    public function query(): Builder
    {
        // code
    }
}
```

### Public properties available in the class:

| Properties      |  Type  |  Default | Description |
| :-------------- | :----: | :---------: | :------- |
| `datatableName` | string | 'datatable' | The name of the object containing all returned data from datatable |
| `perPageKey`    | string | 'per_page'  | The key in the request responsible for changing the number of displayed elements on the page (only when rendering on the client side) |
| `itemsPerPage`   |  int   |    15       | Parameter responsible for the number of displayed elements on the page by default |
| `serverSide` | bool | false| Parameter responsible for the server or client side rendering |

> Note that these options are set globally in the config/datatables.php configuration file.<br> You can override them directly in you the class


### Public methods available in the class:

| Method   |  Response type | Required | Description |
| :------- | :------------: | :------------: | :---------- |
| `query`  | `\Illuminate\Database\Eloquent\Builder`| `required` |Creates prepared Eloquent query builder |
| `modify` | `\Illuminate\Database\Eloquent\Model` | `optional` |  Changing the value of returned fields |
| `columns`| `array` | `optional` | If method columns not implemented in the derived class, will be returned all fillable fields|
| `guard`  | `array` | `optional` | Adding access rights to a specific entry to change, delete, etc. |
| `filters`| `array` | `optional` | Adding filters and their values (server-side rendering only) | 


For ExampleDatatable:

```php

namespace App\Datatables;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Dongrim\DatatableInertia\DatatableInertia;

class PostDatatable extends DatatableInertia
{
    public $datatableName = 'post_datatable';

    public $perPageKey = 'post_per_page';

    public $itemsPerPage = 25;

    public $serverSide = true;

    public function query(): Builder
    {
        return Post::select('posts.*', 'users.username as author_name')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'posts.author_id');
            })
            ->when(request()->search, function ($query, $search) {
                return $query->where('title', 'like', "{$search}%")
                    ->orWhere('text', 'like', "{$search}%")
                    ->orWhere('users.username', 'like', "{$search}%");
            })
            ->when(request()->sort, function ($query, $sort) {
                $query->withoutGlobalScopes();
                $table = ($sort == 'id') ? "posts." : "";
                $query->orderBy($table . $sort, request()->order ?? 'asc');
            })
            ->when(request()->active, function ($query, $active) {
                $active = $active === 'true' ? true : false;
                $query->where('posts.active', $active);
            });
    }

    public function columns(): array
    {
        return ['id', 'position', 'active', 'title', 'text', 'author_name'];
    }

    public function filters(): array
    {
        return ['sort', 'order', 'search', 'active'];
    }

    public function guard($data): array
    {
        return [
            'edit' => Auth::user()->can('post.edit') ? route('post.edit', $data->id) : null,
            'destroy' => Auth::user()->can('post.destroy') ? route('post.destroy', $data->id) : null,
            'restore' => Auth::user()->can('post.restore') ? route('post.restore', $data->id) : null,
        ];
    }

    public function modify($data)
    {
        $data->text = str($data->text)->substr(0, 100);
        return $data;
    }

}

```