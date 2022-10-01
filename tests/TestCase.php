<?php

namespace Dongrim\DatatableInertia\Tests;

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Dongrim\DatatableInertia\DatatableInertiaServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected $params = [
        'data',
        'filters',
        'links',
        'per_page',
        'total',
        'current_page',
        'first_page_url',
        'from',
        'to',
        'last_page',
        'last_page_url',
        'next_page_url',
        'prev_page_url',
        'path',
        'server_side'
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . "/database/migrations");
        $this->withFactories(__DIR__ . "/database/factories");
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            DatatableInertiaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
    }

    protected function makeInertiaResponse($datatable, $params = [])
    {
        $queryString = '/';

        Route::get($queryString, function () use ($datatable) {
            return Inertia::render('Test')->table($datatable);
        });

        if (!blank($params)) {
            $queryString .= '?' . http_build_query($params);
        }

        return $this->withoutExceptionHandling()->get($queryString, ['X-Inertia' => 'true']);
    }


    protected function inertiaResponseJsonToArray($response)
    {
        return json_decode($response->getContent(), true);
    }


    /**
     * Check the keys of an array against a list of values. Returns true if all values in the list
     * is not in the array as a key. Returns false otherwise.
     *
     * @param $array Associative array with keys and values
     * @param $mustHaveKeys Array whose values contain the keys that MUST exist in $array
     * @param &$missingKeys Array. Pass by reference. An array of the missing keys in $array as string values.
     * @return Boolean. Return true only if all the values in $mustHaveKeys appear in $array as keys.
     */
    protected function checkIfParamsExist($array, $mustHaveKeys = [], &$missingKeys = [])
    {
        if (blank($mustHaveKeys)) {
            $mustHaveKeys = $this->params;
        }

        // extract the keys of $array as an array
        $keys = array_keys($array);
        // ensure the keys we look for are unique
        $mustHaveKeys = array_unique($mustHaveKeys);
        // $missingKeys = $mustHaveKeys - $keys
        // we expect $missingKeys to be empty if all goes well
        $missingKeys = array_diff($mustHaveKeys, $keys);
        return empty($missingKeys);
    }
}
