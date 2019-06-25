<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * routing to '/' main page
 */
Route::get('/', function (Request $request) {
    $args = $request->query();
    $empty = 0;
    if(empty($args['page'])){
        $args['page'] = 1;
        $empty++;
    }
    else{
        $args['page'] = (int)$args['page'];
    }
    if(empty($args['perPage'])){
        $args['perPage'] = 20;
        $empty++;
    }
    else{
        $args['perPage'] = (int)$args['perPage'];
    }
    return view('welcome', [
        'page' => $args['page'],
        'perPage' => $args['perPage'],
        'empty' => $empty,
        ]);
});

/**
 * routing to one document
 */
Route::get('/document/{id}', function (Request $request, $id) {
    return view('document', ['documentId' => $id]);
});
