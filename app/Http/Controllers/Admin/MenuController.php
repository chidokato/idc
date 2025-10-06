<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Image;
use File;
class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = Menu::orderBy('view', 'asc')->get();
        return view('admin.menu.index', compact('menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $menu = Menu::get();
        return view('admin.menu.create', compact('menu'));
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
        // dd($data);
        $menu = new Menu();
        $menu->user_id = Auth::User()->id;
        $menu->parent = $data['parent'];
        $menu->name = $data['name'];
        if($data['slug']==''){
            $menu->slug = Str::slug($data['name'], '-');
        }else{
            $menu->slug = $data['slug'];
        }

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = $file->getClientOriginalName();
            while(file_exists("data/menu/".$filename)){$filename = rand(0,99)."_".$filename;}
            $img = Image::make($file)->resize(800, 800, function ($constraint) {$constraint->aspectRatio();})->save(public_path('data/menu/800/'.$filename));
            $menu->img = $filename;
        }

        $menu->save();
        return redirect('admin/menu')->with('success','updated successfully');
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
        $data = Menu::find($id);
        $menu = Menu::get();
        return view('admin.menu.edit', compact('data', 'menu'));
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
        $menu = Menu::find($id);
        if ($id != $data['parent']) {
            $menu->parent = $data['parent'];
        }
        $menu->name = $data['name'];
        if($data['slug']==''){
            $menu->slug = Str::slug($data['name'], '-');
        }else{
            $menu->slug = $data['slug'];
        }
        // thêm ảnh
        if ($request->hasFile('img')) {
            if(File::exists('data/menu/800/'.$menu->img)) { File::delete('data/menu/800/'.$menu->img); } // xóa ảnh cũ
            $file = $request->file('img');
            $filename = $file->getClientOriginalName();
            while(file_exists("data/menu/".$filename)){$filename = rand(0,99)."_".$filename;}
            $img = Image::make($file)->resize(800, 800, function ($constraint) {$constraint->aspectRatio();})->save(public_path('data/menu/800/'.$filename));
            $menu->img = $filename;
        }
        // thêm ảnh
        $menu->save();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = Menu::find($id);
        if(File::exists('data/menu/800/'.$menu->img)) { File::delete('data/menu/800/'.$menu->img); } // xóa ảnh cũ
        $menu->delete();
        return redirect()->back();
    }
}
