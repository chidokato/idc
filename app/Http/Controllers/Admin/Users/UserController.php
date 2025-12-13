<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\User;
use App\Helpers\TreeHelper;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = User::where('permission', '<', 6)->orderBy('id', 'DESC')->get();
        return view('admin.user.index', compact('admins'));
    }
    public function member()
    {
        $users = User::where('permission', '=', 6)->orderBy('id', 'DESC')->get();
        return view('admin.user.member', compact('users'));
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

        $department = Department::findOrFail($data->department_id);
        $items = Department::all();

        $options = TreeHelper::buildOptions(
            items: $items,
            parentId: 0,
            prefix: '',
            selectedId: $data->department_id, 
            idField: 'id',
            parentField: 'parent',
            nameField: 'name'
        );

        return view('admin.user.edit', compact(
            'data',
            'department',
            'options'
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

        $departmentLv3 = Department::with('parentDepartment.parentDepartment')->findOrFail($request->department_id);


        $User->email = $request->email;
        $User->permission = $request->permission;
        $User->yourname = $request->yourname;
        $User->rank = $request->rank;
        $User->address = $request->address;
        $User->phone = $request->phone;
        $User->facebook = $request->facebook;
        $User->department_id = $departmentLv3->id; // lv3
        $User->department_lv2 = $departmentLv3->parentDepartment?->id; // lv2
        $User->department_lv1 = $departmentLv3->parentDepartment?->parentDepartment?->id; // lv1
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
        return redirect()->back()->with('success','Thành công');
    }




    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }


    public function changeStatus(Request $request)
    {
        $user = User::find($request->id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy user!'
            ], 404);
        }

        $user->status = $request->status;  // active / inactive
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái user thành công!'
        ]);
    }


    
}
