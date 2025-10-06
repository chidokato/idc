<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Image;
use File;

use App\Models\Category;
use App\Models\Section;
use App\Models\Post;
use App\Models\Images;

use App\Models\Street;
use App\Models\Ward;
use App\Models\Province;
use App\Models\District;


class NewsController extends Controller
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
        $category = Category::where('sort_by', 'News')->where('parent', '0')->orderBy('view', 'DESC')->get();
        $posts = Post::where('sort_by', 'News')->orderBy('id', 'DESC');
        if($key = request()->key){
            $posts->where('name', 'like', '%' . $key . '%');
        }
        if($cid = request()->cid){
            $posts->where('category_id', $cid);
        }
        $posts = $posts->paginate(30);

        return view('admin.news.index', compact(
            'posts',
            'category'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $category = Category::where('sort_by', 'News')->orderBy('view', 'DESC')->get();
        return view('admin.news.create')->with(compact(
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
        $post->sort_by = 'News';
        $post->name = $data['name'];
        $post->slug = Str::slug($data['name'], '-');
        $post->category_id = $data['category_id'];
        
        $post->detail = $data['detail'];
        $post->content = $data['content'];

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

        return redirect('admin/news')->with('Success','Success');
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
        $category = Category::where('sort_by', 'News')->get();
        $data = Post::find($id);
        $images = Images::where('post_id', $data->id)->get();
        return view('admin.news.edit')->with(compact(
            'category',
            'data',
            'images',
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
        $post->detail = $data['detail'];
        $post->content = $data['content'];
        $post->category_id = $data['category_id'];
        
        $post->title = $data['title'];
        $post->description = $data['description'];

        if ($request->hasFile('img')) {
            if(File::exists('data/images/'.$post->img)) { File::delete('data/images/'.$post->img);} // xóa ảnh cũ
            $file = $request->file('img');
            // $filename = saveImage($file); // Gọi hàm saveImage từ helper
            $filename = $this->saveImage($file);
            $post->img = $filename;
        }

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
