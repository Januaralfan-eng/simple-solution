<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function index()                      { return response('Admin\\PageController::index — TBD', 501); }
    public function create()                     { return response('Admin\\PageController::create — TBD', 501); }
    public function store()                      { return response('Admin\\PageController::store — TBD', 501); }
    public function edit($page)                  { return response('Admin\\PageController::edit — TBD', 501); }
    public function update($page)                { return response('Admin\\PageController::update — TBD', 501); }
    public function destroy($page)               { return response('Admin\\PageController::destroy — TBD', 501); }
    public function clone($page)                 { return response('Admin\\PageController::clone — TBD', 501); }
}
