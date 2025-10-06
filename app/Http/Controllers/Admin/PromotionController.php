<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

use App\Models\Promotion;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

use Image;
use File;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promotion = Promotion::where('parent',0)->orderBy('id', 'desc')->get();
        return view('admin.promotion.index', compact('promotion'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Promotion = Promotion::get();
        $posts = Post::orderBy('id', 'desc')->get();
        return view('admin.Promotion.create', compact('Promotion', 'posts'));
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
        $promotion = new Promotion();
        $promotion->user_id = Auth::User()->id;
        $promotion->parent = 0;
        $promotion->status = 'true';
        $promotion->name = $data['name'];
        $promotion->content = $data['content'];
        $promotion->title = $data['title'];
        $promotion->description = $data['description'];
        $promotion->slug = Str::slug($data['name'], '-');

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = $file->getClientOriginalName();
            while(file_exists("data/promotion/".$filename)){$filename = rand(0,99)."_".$filename;}
            $file->move('data/promotion', $filename);
            $promotion->img = $filename;
        }

        $promotion->save();

        if($request->p_id){
            foreach($request->p_id as $id){
                $promo = new Promotion();
                $promo->parent = $promotion->id;
                $promo->product_id = $id;
                $promo->save();
            }
        }

        return redirect('admin/promotion')->with('success','updated successfully');
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
        $data = Promotion::find($id);
        $p_id = [];
        foreach(Promotion::where('parent', $data->id)->get() as $val){
            $p_id[] = $val->product_id;
        }
        $data_post = Post::whereIn('id', $p_id)->orderBy('id', 'desc')->get();
        $posts = Post::orderBy('id', 'desc')->get();
        return view('admin.promotion.edit', compact('data', 'posts', 'data_post'));
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
        $promotion = Promotion::find($id);
        $promotion->name = $data['name'];
        $promotion->content = $data['content'];
        $promotion->title = $data['title'];
        $promotion->description = $data['description'];
        $promotion->slug = $data['slug'];

        // thêm ảnh
        if ($request->hasFile('img')) {
            if(File::exists('data/promotion/'.$promotion->img)) { File::delete('data/Promotion/'.$promotion->img);} // xóa ảnh cũ
            $file = $request->file('img');
            $filename = $file->getClientOriginalName();
            while(file_exists("data/promotion/".$filename)){$filename = rand(0,99)."_".$filename;}
            $file->move('data/promotion', $filename);
            $promotion->img = $filename;
        }
        // thêm ảnh
        $promotion->save();
        
        if($request->p_id){
            foreach($request->p_id as $id){
                $promo = new Promotion();
                $promo->parent = $promotion->id;
                $promo->product_id = $id;
                $promo->save();
            }
        }
        
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
        Promotion::find($id)->delete();
        return redirect()->back();
    }
}
