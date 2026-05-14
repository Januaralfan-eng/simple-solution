<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function index()         { return response('Admin\\MediaController::index — TBD', 501); }
    public function upload()        { return response('Admin\\MediaController::upload — TBD', 501); }
    public function destroy($media) { return response('Admin\\MediaController::destroy — TBD', 501); }
}
