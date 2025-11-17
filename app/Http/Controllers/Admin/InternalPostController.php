<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Image;
use File;

use App\Models\InternalCategory;
use App\Models\Section;
use App\Models\InternalPost;
use App\Models\Images;

use App\Models\Street;
use App\Models\Ward;
use App\Models\Province;
use App\Models\District;


class internalPostController extends Controller
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
        $category = InternalCategory::where('parent', '0')->orderBy('view', 'DESC')->get();
        $perPage = $request->get('per_page', 10); // Mặc định là 20 nếu không có lựa chọn
        $key = $request->get('key', '');
        $sort = $request->get('sort', 'desc'); // Mặc định là sắp xếp giảm dần
        
        $query = InternalPost::query()->orderBy('id', 'DESC');

        if ($key) {
            $query->where('name', 'like', '%' . $key . '%');
        }

        // Thêm logic sắp xếp
        $query->orderBy('status', $sort);

        $posts = $query->paginate($perPage);

        return view('admin.internalpost.index', compact(
            'posts',
            'category',
        ));
    }

    public function post_up($id)
    {
        $id_max = Post::max('id');
        $data = Post::find($id);
        $data->id = $id_max+1;
        $data->save();
        return redirect()->back()->with('Success','Success');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $province = Province::get();
        $category = Category::where('sort_by', 'Product')->orderBy('view', 'DESC')->get();
        return view('admin.post.create')->with(compact(
            'province',
            'category',
        ));
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
        // dd($request->has('for_sale'));
        $post = new Post();
        $post->user_id = Auth::User()->id;
        $post->status = 'true';
        $post->sort_by = 'Product';
        $post->name = $data['name'];
        $post->slug = Str::slug($data['name'], '-');
        $post->category_id = $data['category_id'];
        
        $post->price = str_replace('.', '', $data['price']);
        $post->price_max = str_replace('.', '', $data['price_max']);
        $post->acreage = $data['acreage'];
        $post->acreage_max = $data['acreage_max'];
        $post->bedroom = $data['bedroom'];
        $post->bedroom_max = $data['bedroom_max'];
        $post->wc = $data['wc'];
        $post->wc_max = $data['wc_max'];
        $post->total_product = $data['total_product'];
        
        $post->province_id = $data['province'];
        $post->district_id = $data['district'];
        $post->ward_id = $data['ward'];
        $post->street_id = $data['street'];
        $post->address = $data['address'];

        $post->monopoly = $request->has('monopoly');
        $post->for_sale = $request->has('for_sale');
        $post->new_product = $request->has('new_product');
        $post->title = $data['title'];
        $post->description = $data['description'];
        
        // thêm ảnh
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
            $filename = $this->saveImage($file);
            $post->img = $filename;
        }
        // ---------------------
        $post->save();
        // thêm ảnh chi tiết
        if($request->hasFile('imgdetail')){
            foreach ($request->file('imgdetail') as $file) {
                if(isset($file)){
                    $Images = new Images();
                    $Images->post_id = $post->id;
                    // $filename = saveImage($file); // Gọi hàm saveImage từ helper
                    $filename = $this->saveImage($file);
                    $Images->img = $filename;
                    $Images->name = $filename;
                    $Images->save();
                }
            }
        }

        // Thêm section
        if (isset($data['tab'])) {
            foreach($data['tab'] as $key => $tab){
                $section = new Section();
                $section->post_id = $post->id;
                $section->stt = $data['stt'][$key];
                $section->tab = $tab;
                $section->heading = $data['heading'][$key];
                $section->content = $data['content'][$key];
                $section->status = 1;
                $section->save();

                if($request->hasFile('img_ss'.$key.'')){
                    foreach ($request->file('img_ss'.$key.'') as $file) {
                    if(isset($file)){
                            $Images = new Images();
                            $Images->post_id = $post->id;
                            $Images->section_id = $section->id;
                            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
                            $filename = $this->saveImage($file);
                            $Images->img = $filename;
                            $Images->name = $filename;
                            $Images->save();
                        }
                    }
                }

            }
        }

        return redirect('admin/post')->with('Success','Success');
        // return response()->json(['success' => 'Success']);
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
        $category = Category::where('sort_by', 'Product')->get();
        $data = Post::find($id);
        $images = Images::where('post_id', $data->id)->where('section_id', null)->get();
        $section = Section::where('post_id', $data->id)->orderBy('stt', 'ASC')->get();
        $province = Province::get();
        $district = District::where('province_id', $data->province_id)->get();
        $ward = Ward::where('district_id', $data->district_id)->get();
        $street = Street::where('district_id', $data->district_id)->get();
        return view('admin.post.edit')->with(compact(
            'category',
            'data',
            'images',
            'section',
            'province',
            'district',
            'ward',
            'street',
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
        $data = $request->all();
        $post = Post::find($id);
        $post->name = $data['name'];
        $post->slug = $data['slug'];
        $post->content = $data['content0'];
        $post->category_id = $data['category_id'];
        
        $post->price = str_replace('.', '', $data['price']);
        $post->price_max = str_replace('.', '', $data['price_max']);
        $post->acreage = $data['acreage'];
        $post->acreage_max = $data['acreage_max'];
        $post->bedroom = $data['bedroom'];
        $post->bedroom_max = $data['bedroom_max'];
        $post->wc = $data['wc'];
        $post->wc_max = $data['wc_max'];
        $post->total_product = $data['total_product'];
        
        $post->province_id = $data['province'];
        $post->district_id = $data['district'];
        $post->ward_id = $data['ward'];
        $post->street_id = $data['street'];
        $post->address = $data['address'];

        $post->monopoly = $request->has('monopoly');
        $post->for_sale = $request->has('for_sale');
        $post->new_product = $request->has('new_product');
        $post->title = $data['title'];
        $post->description = $data['description'];

        if ($request->hasFile('img')) {
            if(File::exists('data/images/'.$post->img)) { File::delete('data/images/'.$post->img);} // xóa ảnh cũ
            $file = $request->file('img');
            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
            $filename = $this->saveImage($file);
            $post->img = $filename;
        }




        

        // thêm ảnh chi tiết
        if($request->hasFile('imgdetail')){
            foreach ($request->file('imgdetail') as $file) {
                if(isset($file)){
                    $Images = new Images();
                    $Images->post_id = $post->id;
                    // $filename = saveImage($file); // Gọi hàm saveImage từ helper
                    $filename = $this->saveImage($file);
                    $Images->img = $filename;
                    $Images->name = $filename;
                    $Images->save();
                }
            }
        }
        // sửa section
        if (isset($data['id-edit'])) {
            foreach($data['id-edit'] as $key => $id_edit){
                $section = Section::find($id_edit);
                $section->stt = $data['stt-edit'][$key];
                $section->tab = $data['tab-edit'][$key];
                $section->heading = $data['heading-edit'][$key];
                $section->content = $data['content-edit'][$key];
                $section->status = $data['status'][$key];
                $section->save();

                if($request->hasFile('img_ss-edit'.$key.'')){
                    foreach ($request->file('img_ss-edit'.$key.'') as $file) {
                    if(isset($file)){
                        $Images = new Images();
                        $Images->post_id = $post->id;
                        $Images->section_id = $section->id;
                        // $filename = saveImage($file); // Gọi hàm saveImage từ helper
                        $filename = $this->saveImage($file);
                        $Images->img = $filename;
                        $Images->name = $filename;
                        $Images->save();
                        }
                    }
                }

            }
        }

        // Thêm section
        if (isset($data['tab'])) {
            foreach($data['tab'] as $key => $tab){
                $section = new Section();
                $section->post_id = $post->id;
                $section->stt = $data['stt'][$key];
                $section->tab = $tab;
                $section->heading = $data['heading'][$key];
                $section->content = $data['content'][$key];
                $section->status = 1;
                $section->save();

                if($request->hasFile('img_ss'.$key.'')){
                    foreach ($request->file('img_ss'.$key.'') as $file) {
                    if(isset($file)){
                        $Images = new Images();
                        $Images->section_id = $section->id;
                        // $filename = saveImage($file); // Gọi hàm saveImage từ helper
                        $filename = $this->saveImage($file);
                        $Images->img = $filename;
                        $Images->name = $filename;
                        $Images->save();
                        }
                    }
                }

            }
        }

        $post->save();
        
        return redirect()->back()->with('Success','Success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Post = Post::find($id);
        if(File::exists('data/images/'.$Post->img)) { File::delete('data/images/'.$Post->img);} // xóa ảnh cũ

        foreach($Post->Images as $val){
            $Images = Images::find($val['id']);
            if(File::exists('data/images/'.$val->img)) { File::delete('data/images/'.$val->img);} // xóa ảnh cũ
            $Images->delete();
        }

        $Post->delete();
        return redirect()->back()->with('Success','Success');
    }
}
