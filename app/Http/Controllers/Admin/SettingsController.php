<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()           { return response('Admin\\SettingsController::index — TBD', 501); }
    public function updateGeneral()   { return response('Admin\\SettingsController::updateGeneral — TBD', 501); }
    public function updateSeo()       { return response('Admin\\SettingsController::updateSeo — TBD', 501); }
    public function updateSocial()    { return response('Admin\\SettingsController::updateSocial — TBD', 501); }
    public function updatePricing()   { return response('Admin\\SettingsController::updatePricing — TBD', 501); }
}
