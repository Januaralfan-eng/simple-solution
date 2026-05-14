<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PortfolioController extends Controller
{
    public function index()           { return response('Admin\\PortfolioController::index — TBD', 501); }
    public function create()          { return response('Admin\\PortfolioController::create — TBD', 501); }
    public function store()           { return response('Admin\\PortfolioController::store — TBD', 501); }
    public function edit($project)    { return response('Admin\\PortfolioController::edit — TBD', 501); }
    public function update($project)  { return response('Admin\\PortfolioController::update — TBD', 501); }
    public function destroy($project) { return response('Admin\\PortfolioController::destroy — TBD', 501); }
    public function sort()            { return response('Admin\\PortfolioController::sort — TBD', 501); }
}
