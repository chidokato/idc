<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\Menu;
use App\Models\Category;
use App\Models\Post;
use App\Models\Section;
use App\Models\Images;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Province;
use Mail;
use Image;
use File;

class HomeController extends Controller
{
    public function index()
    {
        $slider = Slider::orderBy('id', 'desc')->get();
        $product = Post::where('hot', 'true')->where('status', 'true')->where('sort_by', 'Product')->orderBy('id', 'desc')->take(8)->get();
        $news = Post::where('sort_by', 'News')->where('status', 'true')->orderBy('id', 'desc')->take(5)->get();
        $provinces = Province::where('home', 'true')->get();

        return view('pages.home', compact(
            'slider',
            'product',
            'news',
            'provinces',

        ));
    }

    public function category(Request $request, $slug)
{
    $data = Category::where('slug', $slug)->firstOrFail();

    // trang giới thiệu / liên hệ
    if ($slug == 'gioi-thieu') {
        return view('pages.about', compact('data'));
    } elseif ($slug == 'lien-he') {
        return view('pages.contact', compact('data'));
    }

    // ====== PRODUCT ======
    if ($data->sort_by === 'Product') {

        $cats = Category::where('sort_by', 'Product')
            ->where('parent', '>', 0)
            ->get();

        $provinces = Province::get();

        // default cat ids = category hiện tại + con cấp 1 (như bạn đang làm)
        $defaultCatIds = [$data->id];
        $childIds = Category::where('parent', $data->id)->pluck('id')->toArray();
        $defaultCatIds = array_merge($defaultCatIds, $childIds);

        // nếu user tick categories[] thì ưu tiên categories[]; không tick thì dùng defaultCatIds
        $selectedCatIds = $request->input('categories', []);
        $catIds = !empty($selectedCatIds) ? $selectedCatIds : $defaultCatIds;

        // provinces[]
        $provinceIds = $request->input('provinces', []);

        // key
        $key = trim((string) $request->get('key', ''));

        $query = Post::query()
            ->where('status', 'true');

        // tìm theo name (bạn có thể thêm address/slug nếu có cột)
        if ($key !== '') {
            $query->where(function ($q) use ($key) {
                $q->where('name', 'like', "%{$key}%");
                // Nếu có cột address/project thì mở comment:
                // ->orWhere('address', 'like', "%{$key}%")
                // ->orWhere('project', 'like', "%{$key}%");
            });
        }

        // lọc theo category
        if (!empty($catIds)) {
            $query->whereIn('category_id', $catIds);
        }

       if ((int) $request->get('for_sale', 0) === 1) {
    $query->where('for_sale', 1);
}

if ((int) $request->get('monopoly', 0) === 1) {
    $query->where('monopoly', 1);
}


        // lọc theo province
        if (!empty($provinceIds)) {
            $query->whereIn('province_id', $provinceIds);
        }

        // paginate + giữ query string cho links
        $perPage = (int) $request->get('per_page', 12);
        $posts = $query->orderBy('id', 'DESC')
            ->paginate($perPage)
            ->appends($request->query());

        return view('pages.category', compact(
            'data',
            'cats',
            'provinces',
            'posts',
        ));
    }

    // ====== NEWS ======
    if ($data->sort_by === 'News') {
        $cat_array = [$data->id];
        $cates = Category::where('parent', $data->id)->get();
        foreach ($cates as $cate) $cat_array[] = $cate->id;

        $posts = Post::whereIn('category_id', $cat_array)
            ->where('status', 'true')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('pages.news', compact('data', 'posts'));
    }

    abort(404);
}

    public function province($slug)
    {
        $cats = Category::where('sort_by','Product')->where('parent','>',0)->get();
        $provinces = Province::get();
        $data = Province::where('slug', $slug)->first();
        $posts = Post::where('province_id', $data->id)->where('status', 'true')->orderBy('id', 'DESC')->paginate(30);
        return view('pages.category', compact(
            'cats',
            'provinces',
            'data',
            'posts',
        ));
    }

    public function post($catslug, $slug)
    {
        $post = Post::where('slug', $slug)->first();
        $sections = Section::where('post_id', $post->id)->orderBy('stt', 'asc')->get();
        $related_post = Post::where('category_id', $post->category_id)->whereNotIn('id', [$post->id])->where('status', 'true')->orderBy('id', 'desc')->take(10)->get();
        if ($post->sort_by == 'Product') {
            return view('pages.project', compact(
                'post',
                'sections',
                'related_post',
            ));
        }elseif ($post->sort_by == 'News') {
            return view('pages.post', compact(
                'post',
                'related_post',
            ));
        }
        
    }


    public function sitemap()
    {
        $category = Category::all();
        $Post = Post::all();
        return response()->view('sitemap', [
            'category' => $category,
            'Post' => $Post,
            ])->header('Content-Type', 'text/xml');
    }


    

   
}
