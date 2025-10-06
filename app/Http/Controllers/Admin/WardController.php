<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

use App\Models\Ward;
use App\Models\Province;
use App\Models\District;

class WardController extends Controller
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
        $ward = Ward::orderBy('id', 'DESC');

        if($key = request()->key){
            $ward->where('name', 'like', '%' . $key . '%');
        }
        if($cid = request()->province_id){
            $ward->where('province_id', $cid);
        }
        if(request()->district_id){
            $ward->where('district_id', request()->district_id);
        }
        $ward = $ward->paginate(50);

        return view('admin.ward.index', compact(
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
        $locale = Session::get('locale');
        $province = ProvinceTranslation::where('locale', $locale)->get();
        return view('admin.ward.create', compact('province'));
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
        $data = new Ward();
        $data->user_id = Auth::User()->id;
        $data->status = 'true';
        $data->fill([
            'en' => [
                'name' => $datas['name:en'],
                'prefix' => $datas['prefix:en'],
                'district_id' => $datas['district_id:en'],
                'province_id' => $datas['province_id:en'],
            ],
            'vi' => [
                'name' => $datas['name:vi'],
                'prefix' => $datas['prefix:vi'],
                'district_id' => $datas['district_id:vi'],
                'province_id' => $datas['province_id:vi'],
            ],
            'cn' => [
                'name' => $datas['name:cn'],
                'prefix' => $datas['prefix:cn'],
                'district_id' => $datas['district_id:cn'],
                'province_id' => $datas['province_id:cn'],
            ]
        ]);
        $data->save();
        return redirect('admin/ward')->with('success','updated successfully');
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
        $locale = Session::get('locale');
        $Ward_locale = WardTranslation::where('locale', $locale)->where('Ward_id', $id)->first();
        $province = ProvinceTranslation::where('locale', $locale)->get();
        $District = DistrictTranslation::where('province_id', $Ward_locale->province_id)->where('locale', $locale)->get();
        $Ward = WardTranslation::where('Ward_id', $id)->get();
        return view('admin.ward.edit', compact('Ward', 'province', 'District', 'locale', 'id'));
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
        $datas = $request->all();
        $data = Ward::find($id);
        $data->fill([
            'en' => [
                'name' => $datas['name:en'],
                'prefix' => $datas['prefix:en'],
                'district_id' => $datas['district_id:en'],
                'province_id' => $datas['province_id:en'],
            ],
            'vi' => [
                'name' => $datas['name:vi'],
                'prefix' => $datas['prefix:vi'],
                'district_id' => $datas['district_id:vi'],
                'province_id' => $datas['province_id:vi'],
            ],
            'cn' => [
                'name' => $datas['name:cn'],
                'prefix' => $datas['prefix:cn'],
                'district_id' => $datas['district_id:cn'],
                'province_id' => $datas['province_id:cn'],
            ]
        ]);
        $data->save();
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
        //
    }
}
