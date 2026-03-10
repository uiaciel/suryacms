<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Routing\Controller;

class DocumentationController extends Controller
{
    public function index()
    {
        return view('suryacms::admin.documentation');
    }
}
