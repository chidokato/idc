<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Image;
use File;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = Cart::orderBy('id', 'desc')->get();
        return view('admin.cart.index', compact('cart'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $setting = Setting::find('1');
        $cart = Cart::find($id);
        $order = Order::where('cart_id', $id)->get();
        return view('admin.cart.edit', compact('cart', 'order', 'setting'));
    }

    public function update(Request $request, $id)
    {
        $Setting = Setting::find('1');
        $cart = Cart::find($id);
        $cart->status = $request->status;
        $cart->save();

        if ($cart->status == 'Thành công') {
            $user = User::find($request->uid);
            $user->total = $user->total + $request->total;
            if ($Setting->copper < $user->total && $user->total < $Setting->silver) {
                $user->rank = 'Đồng';
            } elseif ($Setting->silver < $user->total && $user->total < $Setting->gold) {
                $user->rank = 'Bạc';
            } elseif ($Setting->gold < $user->total && $user->total < $Setting->diamond) {
                $user->rank = 'Vàng';
            } elseif ($user->total >= $Setting->diamond) {
                $user->rank = 'Kim cương';
            }
            $user->save();
        }
        return redirect()->route('cart.index');
    }

    public function destroy($id)
    {
        Cart::find($id)->delete();
        return redirect()->back();
    }
}
