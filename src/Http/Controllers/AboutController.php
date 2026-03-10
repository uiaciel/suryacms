<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Routing\Controller;

class AboutController extends Controller
{
    public function index()
    {
        return view('suryacms::admin.about');
    }
}
