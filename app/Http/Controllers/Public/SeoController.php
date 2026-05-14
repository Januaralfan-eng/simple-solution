<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
             . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
             . '  <url><loc>'.url('/').'</loc></url>'."\n"
             . '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        return response("User-agent: *\nDisallow:\n", 200, ['Content-Type' => 'text/plain']);
    }
}
