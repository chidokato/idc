<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\InternalCategory;
use Image;
use File;

class InternalCategoryController extends Controller
{
    function saveImage($file, $path = 'data/images/', $maxWidth = 1500, $maxHeight = 1500) {
        $originalFilename = $file->getClientOriginalName();
        $filenameWithoutExtension = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME), '-');
        $extension = $file->getClientOriginalExtension();
        $filename = $filenameWithoutExtension . '.' . $extension;

        while (file_exists(public_path($path . $filename))) {
            $filename = $filenameWithoutExtension . '_' . rand(0, 99) . '.' . $extension;
        }
        $img = Image::make($file);
        $img->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save(public_path($path . $filename));
        return $filename;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = InternalCategory::orderBy('view', 'asc')->get();
        return view('admin.internalcategory.index', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = InternalCategory::get();
        return view('admin.internalcategory.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $category = new InternalCategory();
        $category->user_id = Auth::User()->id;
        $category->status = 'true';
        $category->view = $data['view'];
        $category->icon = $data['icon'];
        $category->parent = $data['parent'];
        $category->name = $data['name'];
        $category->content = $data['content'];
        $category->title = $data['title'];
        $category->description = $data['description'];
        $category->slug = Str::slug($data['name'], '-');

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = $this->saveImage($file);
            $category->img = $filename;
        }

        $category->save();
        return redirect('admin/internalcategory')->with('success','updated successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = InternalCategory::find($id);
        $category = InternalCategory::get();
        return view('admin.internalcategory.edit', compact('data', 'category'));
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
        $data = $request->all();
        // dd($data);
        $category = InternalCategory::find($id);
        $category->view = $data['view'];
        $category->icon = $data['icon'];
        $category->parent = $data['parent'];
        $category->name = $data['name'];
        $category->content = $data['content'];
        $category->title = $data['title'];
        $category->description = $data['description'];
        $category->slug = $data['slug'];

        if ($request->hasFile('img')) {
            if(File::exists('data/images/'.$category->img)) { File::delete('data/images/'.$category->img);} // xóa ảnh cũ
            $file = $request->file('img');
            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
            $filename = $this->saveImage($file);
            $category->img = $filename;
        }

        $category->save();
        
        return redirect('admin/internalcategory')->with('success','updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        InternalCategory::find($id)->delete();
        return redirect()->back();
    }
}
