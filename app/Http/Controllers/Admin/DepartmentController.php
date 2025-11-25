<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TreeHelper;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::latest()->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $departments = Department::latest()->get();
        return view('admin.departments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:departments,code',
        ]);

        Department::create([
            'name'        => $request->name,
            'code'        => $request->code,
            'description' => $request->description,
            'user_id'     => Auth::User()->id,
            'parent'      => $request->parent,
        ]);


        return redirect()->route('departments.index')
            ->with('success', 'Thêm phòng ban thành công');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $items = Department::all();

        $options = TreeHelper::buildOptions(
            items: $items,
            parentId: 0,
            prefix: '',
            selectedId: $department->parent, 
            idField: 'id',
            parentField: 'parent',
            nameField: 'name'
        );

        return view('admin.departments.edit', compact('department', 'options'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:departments,code,' . $department->id,
        ]);

        $department->update([
            'name'        => $request->name,
            'code'        => $request->code,
            'description' => $request->description,
            'user_id'     => Auth::User()->id,
            'parent'      => $request->parent,
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Xóa thành công');
    }
}
