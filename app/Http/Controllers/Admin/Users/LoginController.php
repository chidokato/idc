<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\User;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $locale = Session::get('locale');
        // $category = CategoryTranslation::where('locale', $locale)->orderBy('category_id', 'DESC')->get();
        // return view('category.index', compact('category'));
        return view('admin.login.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:filter',
            'password' => 'required'
        ]);

        if (Auth::attempt([
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                // 'permission' => '1'
            ], $request->input('remember'))) {
            return redirect()->route('admin');
        }
        Session::flash('error', 'Email hoặc Password không đúng');
        return redirect()->back();
    }

    public function register(Request $request)
    {
        $this->validate($request,
        [
            'password' => 'Required',
            'passwordagain' => 'Required|same:password',
            'email'=>'required|email|unique:users,email',
        ],
        [
            'email.unique'=>'Email đã tồn tại',
        ] );
        $data = $request->all();
        $User = new User();
        $User->email = $request->email;
        $User->password = bcrypt($request->password);
        $User->permission = $request->permission;
        $User->yourname = $request->yourname;
        $User->save();
        return redirect()->route('home')->with('success','Đăng ký thành công');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // echo "ok";
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
