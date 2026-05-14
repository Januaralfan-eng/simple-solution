<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    public function index()             { return response('Admin\\ContactController::index — TBD', 501); }
    public function show($contact)      { return response('Admin\\ContactController::show — TBD', 501); }
    public function markRead($contact)  { return response('Admin\\ContactController::markRead — TBD', 501); }
    public function destroy($contact)   { return response('Admin\\ContactController::destroy — TBD', 501); }
    public function htmxList()          { return response('Admin\\ContactController::htmxList — TBD', 501); }
}
