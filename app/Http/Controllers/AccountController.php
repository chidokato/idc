<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\Setting;
use App\Models\Menu;
use App\Models\User;

class AccountController extends Controller
{
    function __construct()
    {
        $setting = Setting::find('1');
        $menu = Menu::orderBy('view', 'asc')->get();
        view()->share( [
            'setting'=>$setting,
            'menu'=>$menu,
        ]);
    }

    public function dangnhap()
    {
        return view('account.login');
    }

    public function index()
    {
        $user = User::find(Auth::User()->id);
        return view('account.main', compact(
            'user',
        ));

    }
}
