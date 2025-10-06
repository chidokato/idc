<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

use App\Models\Section;
use App\Models\SectionTranslation;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function index($pid)
    {
        $locale = Session::get('locale');
        $section = SectionTranslation::where('post_id', $pid)->where('locale', $locale)->orderBy('view', 'asc')->get();
        return view('admin.section.index', compact('section'));
    }

    
}
