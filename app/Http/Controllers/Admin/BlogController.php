<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index()         { return response('Admin\\BlogController::index — TBD', 501); }
    public function create()        { return response('Admin\\BlogController::create — TBD', 501); }
    public function store()         { return response('Admin\\BlogController::store — TBD', 501); }
    public function edit($post)     { return response('Admin\\BlogController::edit — TBD', 501); }
    public function update($post)   { return response('Admin\\BlogController::update — TBD', 501); }
    public function destroy($post)  { return response('Admin\\BlogController::destroy — TBD', 501); }
}
