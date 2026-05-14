<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function index()        { return response('Admin\\AnalyticsController::index — TBD', 501); }
    public function chart($type)   { return response("Admin\\AnalyticsController::chart($type) — TBD", 501); }
}
