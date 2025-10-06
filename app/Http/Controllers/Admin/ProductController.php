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
use App\Models\Post;
use App\Models\Images;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use App\Models\Section;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::where('sort_by', 'Product')->orderBy('id', 'DESC')->paginate(10);
        return view('admin.product.index', compact('posts'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::where('sort_by', 'Product')->get();
        return view('admin.product.create')->with(compact('category'));
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
        $post = new Post();
        $post->user_id = Auth::User()->id;
        $post->status = 'true';
        $post->sort_by = 'Product';
        $post->slug = Str::slug($data['name:vi'], '-');

        // thêm ảnh
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = $file->getClientOriginalName();
            while(file_exists("data/product/".$filename)){$filename = rand(0,99)."_".$filename;}
            $file->move('data/product', $filename);
            $post->img = $filename;
        }
        // thêm ảnh

        $post->fill([
          'en' => [
            'category_tras_id' => $data['category_id:en'],
            'name' => $data['name:en'],
            'content' => $data['content:en'],
            'price' => $data['price'],
            'unit' => $data['unit'],
            'address' => $data['address:en'],
            'title' => $data['title:en'],
            'description' => $data['description:en'],
            'province_id' => $data['province_id:en'],
            'district_id' => $data['district_id:en'],
            'ward_id' => $data['ward_id:en'],
          ],
          'vi' => [
            'category_tras_id' => $data['category_id:vi'],
            'name' => $data['name:vi'],
            'content' => $data['content:vi'],
            'price' => $data['price'],
            'unit' => $data['unit'],
            'address' => $data['address:vi'],
            'title' => $data['title:vi'],
            'description' => $data['description:vi'],  
            'province_id' => $data['province_id:vi'],
            'district_id' => $data['district_id:vi'],
            'ward_id' => $data['ward_id:vi'], 
          ],
          'cn' => [
            'category_tras_id' => $data['category_id:cn'],
            'name' => $data['name:cn'],
            'content' => $data['content:cn'],
            'price' => $data['price'],
            'unit' => $data['unit'],
            'address' => $data['address:cn'],
            'title' => $data['title:cn'],
            'description' => $data['description:cn'],   
            'province_id' => $data['province_id:cn'],
            'district_id' => $data['district_id:cn'],
            'ward_id' => $data['ward_id:cn'],
          ]
        ]);
        $post->save();

        if($request->hasFile('imgdetail')){
            foreach ($request->file('imgdetail') as $file) {
                if(isset($file)){
                    $Images = new Images();
                    $Images->post_id = $post->id;
                    $filename = $file->getClientOriginalName();
                    while(file_exists("data/product/detail/".$filename)){$filename = rand(0,99)."_".$filename;}
                    $file->move('data/product/detail', $filename);
                    $Images->img = $filename;
                    $Images->save();
                }
            }
        }

        foreach ($data['name_section:vi'] as $key => $value) {
          $Section = new Section();
          $Section->user_id = Auth::User()->id;
          $Section->fill([
            'vi' => [
              'name' => $value,
              'post_id' => $post->id,
              'view' => $key+1,
            ],
            'en' => [
              'name' => $data['name_section:en'][$key],
              'post_id' => $post->id,
              'view' => $key+1,
            ],
            'cn' => [
              'name' => $data['name_section:cn'][$key],
              'post_id' => $post->id,
              'view' => $key+1,
            ]
          ]);
          $Section->save();
        }
        

        return redirect('admin/product')->with('Success','Success');
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
        $locale = Session::get('locale');
        $data = PostTranslation::find($id);
        $category = CategoryTranslation::join('categories', 'categories.id', '=', 'category_translations.category_id')
            ->select('category_translations.*')
            ->where('sort_by', 'Product')
            ->where('parent', '0')
            ->where('locale', $data->locale)->orderBy('category_id', 'DESC')->get();
        $Images = Images::where('post_id', $data->post->id)->get();
        $province = ProvinceTranslation::where('locale', $data->locale)->get();
        $district = DistrictTranslation::where('province_id', $data->province_id)->where('locale', $data->locale)->get();
        $ward = WardTranslation::where('district_id', $data->district_id)->where('locale', $data->locale)->get();
        $section = SectionTranslation::where('locale', $data->locale)->where('post_id', $data->post->id)->orderBy('view', 'ASC')->get();
        return view('admin.product.edit')->with(compact('category', 'data', 'Images', 'province', 'district', 'ward', 'locale', 'section'));
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
        
        $PostTranslation = PostTranslation::find($id);
        $PostTranslation->name = $data['name'];
        
        $PostTranslation->address = $data['address'];
        $PostTranslation->content = $data['content'];
        $PostTranslation->title = $data['title'];
        $PostTranslation->description = $data['description'];
        $PostTranslation->price = $data['price'];
        $PostTranslation->unit = $data['unit'];
        $PostTranslation->save();

        $Post = Post::find($PostTranslation->post_id);
        $Post->slug = $data['slug'];
        if (isset($data['category_id:en'])) {
          $Post->fill([
            'en' => ['category_tras_id' => $data['category_id:en']],
            'vi' => ['category_tras_id' => $data['category_id:vi']],
            'cn' => ['category_tras_id' => $data['category_id:cn']]
          ]);
        }
        if (isset($data['province_id:en'])) {
          $Post->fill([
            'en' => ['province_id' => $data['province_id:en']],
            'vi' => ['province_id' => $data['province_id:vi']],
            'cn' => ['province_id' => $data['province_id:cn']]
          ]);
        }
        if (isset($data['district_id:en'])) {
          $Post->fill([
            'en' => ['district_id' => $data['district_id:en']],
            'vi' => ['district_id' => $data['district_id:vi']],
            'cn' => ['district_id' => $data['district_id:cn']]
          ]);
        }
        if (isset($data['ward_id:en'])) {
          $Post->fill([
            'en' => ['ward_id' => $data['ward_id:en']],
            'vi' => ['ward_id' => $data['ward_id:vi']],
            'cn' => ['ward_id' => $data['ward_id:cn']]
          ]);
        }
        // thêm ảnh
        if ($request->hasFile('img')) {
            if(File::exists('data/product/'.$Post->img)) { File::delete('data/product/'.$Post->img);} // xóa ảnh cũ
            $file = $request->file('img');
            $filename = $file->getClientOriginalName();
            while(file_exists("data/product/".$filename)){$filename = rand(0,99)."_".$filename;}
            $file->move('data/product', $filename);
            $Post->img = $filename;
        }
        // thêm ảnh
        $Post->save();

        if($request->hasFile('imgdetail')){
            foreach ($request->file('imgdetail') as $file) {
                if(isset($file)){
                    $Images = new Images();
                    $Images->post_id = $Post->id;
                    $filename = $file->getClientOriginalName();
                    while(file_exists("data/product/detail/".$filename)){$filename = rand(0,99)."_".$filename;}
                    $file->move('data/product/detail', $filename);
                    $Images->img = $filename;
                    $Images->save();
                }
            }
        }
        if (isset($data['name_section:vi'])) {
            foreach ($data['name_section:vi'] as $key => $value) {
            $Section = new Section();
            $Section->user_id = Auth::User()->id;
            $Section->fill([
              'vi' => [
                'name' => $value,
                'post_id' => $Post->id,
              ],
              'en' => [
                'name' => $data['name_section:en'][$key],
                'post_id' => $Post->id,
              ],
              'cn' => [
                'name' => $data['name_section:cn'][$key],
                'post_id' => $Post->id,
              ]
            ]);
            $Section->save();
          }
        }

        foreach ($data['edit_id_section'] as $key => $id) {
          
          $SectionTranslation = SectionTranslation::find($id);
          $SectionTranslation->name = $data['edit_name_section'][$key];
          $SectionTranslation->header = $data['edit_header_section'][$key];
          $SectionTranslation->content = $data['edit_content_section'][$key];
          $SectionTranslation->save();
          $view = $data['edit_view_section'][$key];
          $Section = Section::find($SectionTranslation->section_id);
          $Section->fill([
            'vi' => [
              'view' => $view,
            ],
            'en' => [
              'view' => $view,
            ],
            'cn' => [
              'view' => $view,
            ]
          ]);
          $Section->save();

          if($request->hasFile('img_section'.$key.'')){
              foreach ($request->file('img_section'.$key.'') as $file) {
                  if(isset($file)){
                      $Images = new Images();
                      $Images->section_id = $SectionTranslation->section_id;
                      $filename = $file->getClientOriginalName();
                      while(file_exists("data/product/detail/".$filename)){$filename = rand(0,99)."_".$filename;}
                      $file->move('data/product/detail', $filename);
                      $Images->img = $filename;
                      $Images->save();
                  }
              }
          }
        }

        


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
      $SectionTranslation = SectionTranslation::where('post_id', $id)->get();
      foreach ($SectionTranslation as $key => $value) {
        SectionTranslation::find($value->id)->delete();
        $Section = Section::find($value->section_id);
        if ($Section) {
          $Section->delete();
        }
      }
      $PostTranslation = PostTranslation::where('post_id', $id)->get();
      foreach ($PostTranslation as $key => $value) {
          PostTranslation::find($value->id)->delete();
      }

      $Images = Images::where('post_id', $id)->get();
      foreach ($Images as $key => $val) {
          $Image = Images::find($val->id);
          if(File::exists('data/product/detail'.$Image->img)) { File::delete('data/product/detail'.$Image->img);} // xóa ảnh cũ
          $Image->delete();
      }

      $Post = Post::find($id);
      if(File::exists('data/product/'.$Post->img)) { File::delete('data/product/'.$Post->img);} // xóa ảnh cũ
      $Post->delete();

      return redirect()->back()->with('Success','Success');
    }
}
