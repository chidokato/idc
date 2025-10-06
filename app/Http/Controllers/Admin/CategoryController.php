<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Category;
use Image;
use File;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::orderBy('view', 'asc')->get();
        return view('admin.category.index', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::where('sort_by', 'Product')->get();
        return view('admin.category.create', compact('category'));
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
        $category = new Category();
        $category->user_id = Auth::User()->id;
        $category->status = 'true';
        $category->view = $data['view'];
        $category->sort_by = $data['sort_by'];
        $category->icon = $data['icon'];
        $category->parent = $data['parent'];
        $category->name = $data['name'];
        $category->content = $data['content'];
        $category->title = $data['title'];
        $category->description = $data['description'];
        $category->slug = Str::slug($data['name'], '-');

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = $file->getClientOriginalName();
            while(file_exists("data/category/".$filename)){$filename = rand(0,99)."_".$filename;}
            $img = Image::make($file)->resize(800, 800, function ($constraint) {$constraint->aspectRatio();})->save(public_path('data/category/800/'.$filename));
            $category->img = $filename;
        }

        $category->save();
        return redirect('admin/category')->with('success','updated successfully');
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
        $data = Category::find($id);
        $category = Category::where('sort_by', $data->sort_by)->get();
        return view('admin.category.edit', compact('data', 'category'));
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
        $category = Category::find($id);
        $category->view = $data['view'];
        $category->icon = $data['icon'];
        $category->parent = $data['parent'];
        $category->name = $data['name'];
        $category->content = $data['content'];
        $category->title = $data['title'];
        $category->description = $data['description'];
        $category->slug = $data['slug'];

        // thêm ảnh
        if ($request->hasFile('img')) {
            if(File::exists('data/category/800/'.$category->img)) { File::delete('data/category/800/'.$category->img); } // xóa ảnh cũ
            $file = $request->file('img');
            $filename = $file->getClientOriginalName();
            while(file_exists("data/category/".$filename)){$filename = rand(0,99)."_".$filename;}
            $img = Image::make($file)->resize(800, 800, function ($constraint) {$constraint->aspectRatio();})->save(public_path('data/category/800/'.$filename));
            $category->img = $filename;
        }
        // thêm ảnh

        $category->save();
        
        return redirect('admin/category')->with('success','updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::find($id)->delete();
        return redirect()->back();
    }
}
