<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Images;

class ImagesController extends Controller
{
    public function index()
    {
        $Images = Images::all();
    }
}
