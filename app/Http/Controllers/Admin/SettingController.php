<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;
use Image;
use File;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
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

    public function index()
    {
        $data = Setting::find('1');
        return view('admin.setting.index', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        // dd($data);
        $setting = Setting::find($id);
        $setting->name = $data['name'];
        $setting->address = $data['address'];
        $setting->title = $data['title'];
        $setting->description = $data['description'];
        $setting->footer = $data['footer'];
        $setting->hotline = $data['hotline'];
        $setting->email = $data['email'];
        $setting->facebook = $data['facebook'];
        $setting->youtube = $data['youtube'];
        $setting->maps = $data['maps'];
        $setting->header = $data['header'];

        // thêm ảnh
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
            $filename = $this->saveImage($file);
            $setting->img = $filename;
        }

        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
            $filename = $this->saveImage($file);
            $setting->favicon = $filename;
        }

        $setting->save();

        return redirect()->back();
    }
    
}
