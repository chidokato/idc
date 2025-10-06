<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id', 'DESC')->get();
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        $User->address = $request->address;
        $User->phone = $request->phone;
        $User->facebook = $request->facebook;
        $User->save();
        return redirect('admin/users')->with('success','successfully');
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
        $data = User::find($id);
        return view('admin.user.edit', compact(
            'data',
        ));
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
        $User = User::find($id);

        if($request->changepassword == "on")
        {
            $this->validate($request,
            [
                'password' => 'Required',
                'passwordagain' => 'Required|same:password'                
            ],
            [] );
            $User->password = bcrypt($request->password);
        }

        $User->email = $request->email;
        $User->permission = $request->permission;
        $User->yourname = $request->yourname;
        $User->address = $request->address;
        $User->phone = $request->phone;
        $User->facebook = $request->facebook;
        $User->save();
        return redirect()->back()->with('success','Thành công');
        // return redirect('admin/users')->with('success','successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->back();
    }



    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
