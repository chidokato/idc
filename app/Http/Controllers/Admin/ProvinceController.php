<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Str;
use Image;
use File;

use App\Models\Province;

class ProvinceController extends Controller
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

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // Mặc định là 20 nếu không có lựa chọn
        $key = $request->get('key', '');
        $sort = $request->get('sort', 'desc'); // Mặc định là sắp xếp giảm dần
        
        $query = Province::query();

        if ($key) {
            $query->where('name', 'like', '%' . $key . '%');
        }

        // Thêm logic sắp xếp
        $query->orderBy('status', $sort);

        $provinces = $query->paginate($perPage);

        // foreach($provinces as $val){
        //     $data = Province::find($val->id);
        //     $data->slug = Str::slug($data['name'], '-');
        //     $data->save();
        // }

        return view('admin.province.index', compact('provinces'));
    }




    // public function index()
    // {
    //     $province = Province::orderBy('id', 'desc');
    //     if($key = request()->key){
    //         $province->where('name', 'like', '%' . $key . '%');
    //     }
    //     $province = $province->paginate(15);
    //     return view('admin.province.index', compact('province'));
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.province.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datas = $request->all();
        $data = new Province();
        $data->user_id = Auth::User()->id;
        $data->status = 'true';
        $data->fill([
            'en' => [
                'name' => $datas['name:en'],
            ],
            'vi' => [
                'name' => $datas['name:vi'],
            ],
            'cn' => [
                'name' => $datas['name:cn'],
            ]
        ]);
        $data->save();
        return redirect('admin/province')->with('success','updated successfully');
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
        $data = Province::find($id);
        return view('admin.province.edit', compact('data'));
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
        $data = Province::find($id);
        // dd($request->hasFile('img'));
        if ($request->hasFile('img')) {
            if(File::exists('data/images/'.$data->img)) { File::delete('data/images/'.$data->img);} // xóa ảnh cũ
            $file = $request->file('img');
            $filename = $this->saveImage($file);
            $data->img = $filename;
        }
        $data->save();

        return redirect('admin/province')->with('success','updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = ProvinceTranslation::where('province_id', $id)->get();
        foreach ($data as $key => $value) {
            ProvinceTranslation::find($value->id)->delete();
        }
        Province::find($id)->delete();
        return redirect()->back();
    }
}
