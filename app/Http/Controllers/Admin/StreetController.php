<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

use App\Models\Street;
use App\Models\Ward;
use App\Models\Province;
use App\Models\District;

class StreetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $province = Province::get();
        $district = District::get();
        $ward = Ward::get();
        $street = Street::orderBy('id', 'DESC');

        if($key = request()->key){
            $street->where('name', 'like', '%' . $key . '%');
        }
        if($cid = request()->province_id){
            $ward->where('province_id', $cid);
        }
        if(request()->district_id){
            $street->where('district_id', request()->district_id);
        }
        $street = $street->paginate(50);

        return view('admin.street.index', compact(
            'street',
            'ward',
            'province',
            'district',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.Street.create');
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
        $data = new Street();
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
        return redirect('admin/Street')->with('success','updated successfully');
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
        $data = StreetTranslation::find($id);
        return view('admin.Street.edit', compact('data'));
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
        $data = StreetTranslation::find($id);
        $data->name = $request->name;
        $data->save();
        return redirect('admin/Street')->with('success','updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
