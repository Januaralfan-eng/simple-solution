<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function index()            { return response('Admin\\ServiceController::index — TBD', 501); }
    public function create()           { return response('Admin\\ServiceController::create — TBD', 501); }
    public function store()            { return response('Admin\\ServiceController::store — TBD', 501); }
    public function edit($service)     { return response('Admin\\ServiceController::edit — TBD', 501); }
    public function update($service)   { return response('Admin\\ServiceController::update — TBD', 501); }
    public function destroy($service)  { return response('Admin\\ServiceController::destroy — TBD', 501); }
    public function sort()             { return response('Admin\\ServiceController::sort — TBD', 501); }
}
