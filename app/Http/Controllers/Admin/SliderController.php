<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Image;
use File;

use App\Models\Slider;

class SliderController extends Controller
{
    function saveImage($file, $path = 'data/images/', $maxWidth = 2024, $maxHeight = 2024) {
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
        $data = Slider::orderBy('id', 'DESC')->get();
        return view('admin.slider.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.slider.create');
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
        $slider = new Slider();
        $slider->user_id = Auth::User()->id;
        $slider->name = $data['name'];
        $slider->note = $data['note'];
        // $slider->link = $data['link'];
        // $slider->content = $data['content'];

        // thêm ảnh
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
            $filename = $this->saveImage($file);
            $slider->img = $filename;
        }
        // thêm ảnh

        $slider->save();
        // return redirect('admin/slider')->with('Success','Thành công');
        return redirect()->back()->with('Success','Thành công');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Slider::where('note', 'like', '%' . $id . '%')->orderBy('id', 'DESC')->get();
        return view('admin.slider.index', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Slider::find($id);
        return view('admin.slider.edit')->with(compact('data'));
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
        $slider = Slider::find($id);
        $slider->name = $data['name'];
        $slider->note = $data['note'];
        // $slider->link = $data['link'];
        // $slider->content = $data['content'];
        
        // thêm ảnh
        if ($request->hasFile('img')) {
            if(File::exists('data/images/'.$slider->img)) { File::delete('data/images/'.$slider->img);} // xóa ảnh cũ
            $file = $request->file('img');
            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
            $filename = $this->saveImage($file);
            $slider->img = $filename;
        }
        // thêm ảnh
        $slider->save();
        // return redirect('admin/slider')->with('Success','Thành công');
        return redirect()->back()->with('Success','Thành công');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $slider = Slider::find($id);
        if(File::exists('data/home/'.$slider->img)) { File::delete('data/home/'.$slider->img);} // xóa ảnh cũ
        $slider->delete();
        return redirect()->back()->with('Success','Thành công');
    }
}
