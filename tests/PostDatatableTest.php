<?php

namespace Dongrim\DatatableInertia\Tests;

use Inertia\Inertia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Inertia\Response as InertiaResponse;
use Dongrim\DatatableInertia\DatatableInertia;
use Dongrim\DatatableInertia\Tests\Models\Post;
use Dongrim\DatatableInertia\Tests\Datatables\PostDatatableExample;
use Dongrim\DatatableInertia\Tests\Datatables\PostDatatableWithGuard;
use Dongrim\DatatableInertia\Tests\Datatables\PostDatatableWithModify;
use Dongrim\DatatableInertia\Tests\Datatables\PostDatatableWithColumns;
use Dongrim\DatatableInertia\Tests\Datatables\PostDatatableWithFilters;

class PostDatatableTest extends TestCase
{
    /** @test */
    public function is_exist_post_datatable()
    {
        $this->assertTrue(class_exists(PostDatatableExample::class));
    }

    /** @test */
    public function is_post_datatable_class_extends_inertia_datatable()
    {
        $datatableClass = new PostDatatableExample();

        $this->assertTrue(is_subclass_of($datatableClass, DatatableInertia::class));
    }

    /** @test */
    public function is_сlass_name_post_datatable_inheritor_inertia_datatable()
    {
        $className = PostDatatableExample::class;

        $this->assertTrue(is_string($className));
        $this->assertTrue(class_exists($className));
        $this->assertTrue(is_subclass_of((new $className()), DatatableInertia::class));
    }

    /** @test */
    public function this_is_an_inertial_response_when_passing_an_object_of_a_class()
    {
        $datatable = new PostDatatableExample();
        $response = Inertia::render('Post/Index')->table($datatable);

        $this->assertEquals(get_class($response), InertiaResponse::class);
        $this->assertClassHasAttribute('props', get_class($response));
    }

    /** @test */
    public function this_is_an_inertial_response_when_passing_an_name_of_a_class()
    {
        $response = Inertia::render('Post/Index')->table(PostDatatableExample::class);

        $this->assertEquals(get_class($response), InertiaResponse::class);
        $this->assertClassHasAttribute('props', get_class($response));
    }

    /** @test */
    public function can_change_datatable_name()
    {
        $datatable = new PostDatatableExample();
        $datatable->datatableName = 'PostDatatableExample';
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertArrayHasKey('PostDatatableExample', $data['props']);
    }

    // /** @test */
    // public function can_change_per_page_key()
    // {
    //     $datatable = new PostDatatableExample();
    //     $datatable->perPageKey = 'custom_per_page';
    //     $datatable->itemsPerPage = 15;
    //     $response = $this->makeInertiaResponse($datatable);
    //     $data = $this->inertiaResponseJsonToArray($response);
    //     dd($data);
    // }

    /** @test */
    public function when_rendering_data_on_the_client_side_is_not_possible_to_change_the_number_of_elements_per_page()
    {
        $perPage = 10;
        $datatable = new PostDatatableExample();
        $datatable->itemsPerPage = $perPage;
        $response = $this->makeInertiaResponse($datatable, ['per_page' => 5]);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertEquals($perPage, $data['props']['datatable']['per_page']);
    }

    /** @test */
    public function when_rendering_data_on_the_server_side_is_it_possible_to_change_the_number_of_elements_per_page()
    {
        factory(Post::class, 100)->create();

        $perPage = 5;
        $datatable = new PostDatatableExample();
        $datatable->serverSide = true;
        $response = $this->makeInertiaResponse($datatable, ['per_page' => $perPage]);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertCount($perPage, $data['props']['datatable']['data']);
        $this->assertEquals($perPage, (int)$data['props']['datatable']['per_page']);
    }

    /** @test */
    public function when_per_page_param_is_not_filled_must_be_returned_default_in_config_value()
    {
        $perPage = '';
        $datatable = new PostDatatableExample();
        $datatable->serverSide = true;
        $response = $this->makeInertiaResponse($datatable, ['per_page' => $perPage]);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertEquals(config('datatables.itemsPerPage'), (int)$data['props']['datatable']['per_page']);
    }


    /** @test */
    public function when_per_page_param_is_not_number_or_is_zero_must_be_returned_default_in_config_value()
    {
        $perPages = ['a', 'test', .5, 10.2, '14a', 0];

        foreach ($perPages as $perPage) {
            $datatable = new PostDatatableExample();
            $datatable->serverSide = true;
            $response = $this->makeInertiaResponse($datatable, ['per_page' => $perPage]);
            $data = $this->inertiaResponseJsonToArray($response);
    
            $this->assertEquals(config('datatables.itemsPerPage'), (int)$data['props']['datatable']['per_page']);
        }
    }

    /** @test */
    public function is_total_items_equals_all_post_count()
    {
        $recordCount = 100;
        factory(Post::class, $recordCount)->create();

        // when client side
        $datatable = new PostDatatableExample();
        $datatable->itemsPerPage = $recordCount;
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertEquals($recordCount, (int)$data['props']['datatable']['total']);

        // when server side
        $datatable = new PostDatatableExample();
        $datatable->serverSide = true;
        $datatable->itemsPerPage = $recordCount;
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertEquals($recordCount, (int)$data['props']['datatable']['total']);
    }


    /** @test */
    public function is_props_datatable_contains_all_posts()
    {
        $recordCount = 5;
        factory(Post::class, $recordCount)->create();

        // when client side
        $datatable = new PostDatatableExample();
        $datatable->itemsPerPage = $recordCount;
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertCount($recordCount, $data['props']['datatable']['data']);

        // when server side
        $datatable = new PostDatatableExample();
        $datatable->serverSide = true;
        $datatable->itemsPerPage = $recordCount;
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertCount($recordCount, $data['props']['datatable']['data']);
    }


    /** @test */
    public function if_the_method_columns_is_not_implemented_returns_all_fields_of_the_table()
    {
        factory(Post::class)->create();

        $model = new Post();
        $fields = Schema::getColumnListing($model->getTable());

        // when client side
        $response = $this->makeInertiaResponse(PostDatatableExample::class);
        $data = $this->inertiaResponseJsonToArray($response);
        $firstItem = $data['props']['datatable']['data'][0];

        $this->assertTrue($this->checkIfParamsExist($firstItem, $fields));

        // when server side
        $datatable = new PostDatatableExample();
        $datatable->serverSide = true;
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);
        $firstItem = $data['props']['datatable']['data'][0];

        $this->assertTrue($this->checkIfParamsExist($firstItem, $fields));
    }

    /** @test */
    public function if_method_columns_exist_only_the_specified_fields_are_returned()
    {
        factory(Post::class)->create();

        $datatable = new PostDatatableWithColumns();
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);
        $firstItem = $data['props']['datatable']['data'][0];
        $columns = $datatable->columns();

        foreach ($columns as $column) {
            $this->assertArrayHasKey($column, $firstItem);
        }
    }

    /** @test */
    public function when_rendering_data_response_must_by_contains_filters_params()
    {
        $datatable = new PostDatatableWithFilters();
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);
        $dataFilters = $data['props']['datatable']['filters'];
        $filters = $datatable->filters();

        foreach ($filters as $filter) {
            $this->assertArrayHasKey($filter, $dataFilters);
        }
    }

    /** @test */
    public function when_filters_contain_a_search_parameter_the_response_will_return_the_filtered_data()
    {
        factory(Post::class, 5)->create();

        $now = Carbon::now()->timestamp;
        Post::create([
            'author_id' => 1,
            'slug' => 'test-post-slug-' . $now,
            'title' => 'Test post title-' . $now,
            'body' => 'Test post body-' . $now,
        ]);

        $datatable = new PostDatatableWithFilters();
        $searchStrings = ['slug', 'test', 'title', 'body', $now];

        foreach ($searchStrings as $searchStr) {
            $response = $this->makeInertiaResponse($datatable, ['search' => $searchStr]);
            $data = $this->inertiaResponseJsonToArray($response);
            $result = $data['props']['datatable']['data'];
            $dataFilters = $data['props']['datatable']['filters'];

            $this->assertEquals($searchStr, $dataFilters['search']);
            $this->assertTrue(count($result) == 1);
        }
    }

    /** @test */
    public function when_filters_contain_a_sort_and_order_parameter_the_response_will_return_the_filtered_data()
    {
        factory(Post::class, 5)->create();

        $datatable = new PostDatatableWithFilters();
        $sorts = ['id', 'title', 'body'];
        $orders = ['asc', 'desc'];

        foreach ($sorts as $sort) {
            foreach ($orders as $order) {
                $response = $this->makeInertiaResponse($datatable, ['sort' => $sort, 'order' => $order]);
                $data = $this->inertiaResponseJsonToArray($response);
                $dataPosts = $data['props']['datatable']['data'];
                $dataFilters = $data['props']['datatable']['filters'];

                $posts = Post::select('id', 'author_id', 'slug', 'title', 'body')
                    ->orderBy($sort, $order)
                    ->get()
                    ->toArray();

                $this->assertEquals($posts, $dataPosts);
                $this->assertEquals($dataFilters['sort'], $sort);
                $this->assertEquals($dataFilters['order'], $order);
            }
        }
    }

    /** @test */
    public function can_pagination_data_when_server_side_render()
    {
        factory(Post::class, 100)->create();

        $datatable = new PostDatatableExample();
        $datatable->serverSide = true;

        $perPages = [1, 5, 10, 15, 20];
        $currentPages = [1, 3, 5];

        foreach ($perPages as $perPage) {
            foreach ($currentPages as $currentPage) {
                $response = $this->makeInertiaResponse($datatable, ['page' => $currentPage, 'per_page' => $perPage]);
                $dataPosts = $this->inertiaResponseJsonToArray($response)['props']['datatable'];

                $posts = Post::paginate($perPage, ['*'], 'page', $currentPage)
                    ->toArray();

                $this->assertEquals($dataPosts['data'], $posts['data']);
            }
        }
    }

    /** @test */
    public function when_the_quard_method_is_implemented_each_entry_must_contain_this_information()
    {
        factory(Post::class, 3)->create();

        $datatable = new PostDatatableWithGuard();
        $response = $this->makeInertiaResponse(PostDatatableWithGuard::class);
        $dataPosts = $this->inertiaResponseJsonToArray($response)['props']['datatable']['data'];
        $posts = Post::all();

        foreach ($posts as $key => $post) {
            $posts[$key]['can'] = $datatable->guard($post);
        }

        $this->assertEquals($dataPosts, $posts->toArray());
    }

    /** @test */
    public function when_implementing_the_modify_method_each_record_must_contain_the_modified_information()
    {
        factory(Post::class, 3)->create();

        $datatable = new PostDatatableWithModify();
        $response = $this->makeInertiaResponse(PostDatatableWithModify::class);
        $dataPosts = $this->inertiaResponseJsonToArray($response)['props']['datatable']['data'];
        $posts = Post::all();

        foreach ($posts as $key => $post) {
            $posts[$key] = $datatable->modify($post);
        }

        $this->assertEquals($dataPosts, $posts->toArray());
    }


    /** @test */
    public function response_data_must_be_contains_all_params()
    {
        factory(Post::class, 100)->create();

        // when client side
        $response = $this->makeInertiaResponse(PostDatatableExample::class);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertTrue($this->checkIfParamsExist($data['props']['datatable']));

        // when server side
        $datatable = new PostDatatableExample();
        $datatable->serverSide = true;
        $response = $this->makeInertiaResponse($datatable);
        $data = $this->inertiaResponseJsonToArray($response);

        $this->assertTrue($this->checkIfParamsExist($data['props']['datatable']));
    }
}
