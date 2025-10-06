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

class HomeSystemController extends Controller
{
    public function sendmail(){
        $name = 'Nguyễn Văn Tuấn';
        Mail::send('emails.test', compact('name'), function($email) use($name){
            $email->subject('demo test mail');
            $email->to('tuan.pn92@gmail.com', 'web rinlisa');
        });
    }

    public function question(Request $request){
        $Customer = new Customer();
        $Customer->name = $request->name;
        $Customer->phone = $request->phone;
        $Customer->email = $request->email;
        $Customer->title = $request->url;
        $Customer->content = $request->content;
        $Customer->save();
        return redirect()->route('home')->with('success','Gửi thành công');
    }


    public function filterPosts(Request $request)
    {
        $categoryIds = $request->get('categories', []);
        $provinceIds = $request->get('provinces', []);

        $posts = Post::query()
            ->when($categoryIds || $provinceIds, function ($query) use ($categoryIds, $provinceIds) {
                if ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                }
                if ($provinceIds) {
                    $query->whereIn('province_id', $provinceIds);
                }
            })
            ->orderBy('id', 'DESC')->get();

        return view('pages.iteam.loadproduct', compact('posts'))->render();
    }

   
}
