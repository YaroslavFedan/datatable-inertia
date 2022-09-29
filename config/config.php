<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Datatable Key
    |--------------------------------------------------------------------------
    |
    | Named datatable array on response,
    | You can change this on your datatable, set public property datatableKey
    |
    */
    'datatableKey' => 'datatable',

    /*
    |--------------------------------------------------------------------------
    | Count items on per page
    |--------------------------------------------------------------------------
    |
    | Number of items displayed per page,
    | You can change this on your datatable, set public property perPage
    |
    */
    'itemsPerPage' => 25,

    /*
    |--------------------------------------------------------------------------
    | The name of the key of the elements on the per page
    |--------------------------------------------------------------------------
    |
    | If you set the property of isServerSide as True,
    | this package should know which key in the request
    | will be set the number of elements on the page.
    |
    */
    'perPageKey' => 'per_page',

    /*
    |--------------------------------------------------------------------------
    | Server side response
    |--------------------------------------------------------------------------
    |
    | Will pagination and filters be created and processed on the server side,
    | You can change this on your datatable, set public property serverSide
    |
    */
    'serverSide' => false,

    /*
    |--------------------------------------------------------------------------
    | Base destination datatable folder
    |--------------------------------------------------------------------------
    | When creating Datatable in a command console,
    | you can dynamically change the path to the location folder
    |
    */
    'basePath' => 'App/Datatables',
];
