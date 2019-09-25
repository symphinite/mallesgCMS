<?php

namespace App\Http\Controllers;

use App\CityMaster;
use App\CountryMaster;
use App\EventMaster;
use App\MallType;
use App\MerchantLocation;
use App\MerchantType;
use App\OfferMaster;
use Illuminate\Http\Request;
use App\Repositories\MallRepository;
use App\MallMaster;

class MallController extends Controller
{

    /**
     * @var MallRepository
     *
     */
    protected $mall;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MallRepository $mall)
    {
        $this->mall =  $mall;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $malls = $this->mall->all()->pluck('mall_name', 'mall_id');
        $current_malls = MallMaster::where('mall_active','Y')->get() ?? [];
        $total_mall = MallMaster::where('mall_active','Y')->count();
        $countrys = CountryMaster::all();
        $citymaster = CityMaster::where('country_id',1)->first();
        $city_total_by_country = CityMaster::where('country_id',1)->first();
        $mall_types = MallMaster::with('malltype')
            ->where('country_id',1)
            ->where('mall_active','Y')
            ->distinct('mt_id')->get(['mt_id','country_id','city_id']);


//return $mall_types;
        $data = [
            'malls' => $malls->toJson(),
            'current_mallss' => $current_malls,
            'total_mall' => $total_mall,
            'countrys' => $countrys,
            'mall_types' => $mall_types,
            'citymaster' => $citymaster,

        ];

        return view('main.mall_list.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //$merchantOptions = $this->merchant->all()->pluck('merchant_name', 'merchant_id')->toJson() ?? [];
       // $mallOptions = $this->mall->all()->pluck('mall_name', 'mall_id')->toJson() ?? [];
        $current_malls = MallMaster::where('mall_id',$id)->get() ?? [] ;
        $total_mall = MallMaster::where('mall_active','Y')->count();
        //$locations = $current_merchant->locations;
        //$floors = LevelMaster::all();
//return $current_malls;
        $data = [
           // 'merchantOptions' => $merchantOptions,
            'current_mallss' => $current_malls,
            'total_mall' => $total_mall,
           /*'total_merchant' => $total_merchant,
            'total_event' => $total_event,
            'total_promos' => $total_promos,*/
           // 'floors' => $floors,
           'id' => $id
        ];


        //return $total_promos;

        return view('main.mall_list.index',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = $this->mall->destroy($id);
        return response()->json([
            'status' => $delete ? 'success' : 'error',
            'message' => $delete ? __('succesfully deleted') : __('error deleting')
        ],200);
    }


    /**
     * 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        return $this->mall->search($name);
    }

    public function searchWith($name)
    {
        return MallMaster::with('merchantLocations:mall_id,merchant_location,merchantlocation_id')->where('mall_name', 'LIKE', "%$name%")
            ->orderBy('mall_name')->get(['mall_name', 'mall_id']);
    }

    public function columnUpdate(Request $request,$id){

        /*$this->mall->update($id, [
            request()->name => request()->value
        ]);*/
                   $name =  $request->name;
        $mall = MallMaster::find($id);
        $mall->$name = $request->value;
        $mall->save();

        return response()->json([
            'status' => 'success',
            'message' => __('successfully updated mall'),
            'id' => $id
        ],200);
    }

    public function getCity(Request $request){
            //return $request->id;

            $citys = CityMaster::where('country_id',$request->id)->get();
            $mall_count = MallMaster::where('country_id',$request->id)->count();
            $cit ='';
            if(count($citys) > 1 ){
                $cit.='<option value="all">All ('.$mall_count.')</option>';
            }

            foreach ($citys as $city){
                $mall_by_city = MallMaster::where('city_id',$city->city_id)->where('mall_active','Y')->count();
                $cit.='<option value="'.$city->city_id.'" title="'.$city->city_name.'">'.$city->city_name.' ('.$mall_by_city.')</option>';
            }
            //$city = ''
        return response()->json([
            'status' => 'success',
            'message' => __('successfully updated mall'),
            'city' => $cit
        ],200);

    }

    public function getType(Request $request){
        //return $request->id;

        //$citys = CityMaster::where('country_id',$request->id)->get();
        //$mall_count = MallMaster::where('country_id',$request->id)->count();

        $mall_types = MallMaster::with('malltype')
            ->where('country_id',$request->country_id);

        if(isset($request->city_id)){
            $mall_types = $mall_types->where('city_id',$request->city_id);
        }

        $mall_types =$mall_types->where('mall_active','Y')->distinct('mt_id')->get(['mt_id','country_id','city_id']);

        //return $mall_types;

        $cit ='';
        if(count($mall_types) > 1 ){
            $cit.='<option value="all">All </option>';
        }

       foreach($mall_types as $mall_type){

           $total = MallMaster::where('country_id',$mall_type->country_id);
           if(isset($request->city_id)){
               $total = $total->where('city_id',$mall_type->city_id);
           }
           $total = $total->where('mt_id',$mall_type->mt_id)->where('mall_active','Y')->count();

            $cit.='<option value="'.@$mall_type->malltype->type_name.'" title="'.@$mall_type->malltype->type_name.'">'.@$mall_type->malltype->type_name.' ('.$total.')</option>';

       }

        //$city = ''
        return response()->json([
            'status' => 'success',
            'message' => __('successfully updated mall'),
            'city' => $cit
        ],200);

    }

    public function mallInfo($mall_id){
        //return $id;
        $mall = MallMaster::find($mall_id);
        $total_merchant = MallMaster::total_merchant($mall_id);

        $locations = MallMaster::locationByMallId($mall_id);

        //$merchant_types = MerchantType::all();
       // return $locations;

        $data = [
            'mall' => $mall,
            'total_merchant' => $total_merchant,
            'locations' => $locations
        ];
        return view('main.mall_list.mall_info',$data);


    }
}
