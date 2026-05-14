<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function index()
    {
        return response('Services index — not yet implemented', 501);
    }

    public function show(string $slug)
    {
        return response("Service [$slug] — not yet implemented", 501);
    }
}
