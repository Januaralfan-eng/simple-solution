<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BlogCategoryController extends Controller
{
    public function index()            { return response('Admin\\BlogCategoryController::index — TBD', 501); }
    public function store()            { return response('Admin\\BlogCategoryController::store — TBD', 501); }
    public function update($category)  { return response('Admin\\BlogCategoryController::update — TBD', 501); }
    public function destroy($category) { return response('Admin\\BlogCategoryController::destroy — TBD', 501); }
}
