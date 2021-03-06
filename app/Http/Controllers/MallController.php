<?php

namespace App\Http\Controllers;

use App\CityMaster;
use App\CountryMaster;
use App\EventMaster;
use App\LevelActivity;
use App\LevelMalls;
use App\LevelMaster;
use App\MallImage;
use App\MallType;
use App\MerchantLocation;
use App\MerchantMaster;
use App\MerchantType;
use App\OfferMaster;
use App\PreferenceMaster;
use App\PromotionMaster;
use App\TownMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\MallRepository;
use App\MallMaster;
use Illuminate\Support\Facades\Storage;

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
         $current_malls = MallMaster::orderBy('mall_active','desc')->get() ?? [];
        $total_mall = MallMaster::count();
        $countrys = CountryMaster::all();
        $citymaster = CityMaster::where('country_id',1)->first();
        $townmasters = TownMaster::where('city_id',1)->get();
        $city_total_by_country = CityMaster::where('country_id',1)->first();
        $mall_types = MallMaster::with('malltype')
            ->where('country_id',1)
            ->where('mall_active','Y')
            ->distinct('mt_id')->get(['mt_id','country_id','city_id']);


//return $current_malls;
        $data = [
            'malls' => $malls->toJson(),
            'current_mallss' => $current_malls,
            'total_mall' => $total_mall,
            'countrys' => $countrys,
            'mall_types' => $mall_types,
            'citymaster' => $citymaster,
            'townmasters' => $townmasters

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
        $messages = [
            'mall_name.required'    => 'Mall name field is required'
        ];

        // Start Validation
        $validator = \Validator::make($request->all(), [
            'mall_name' => 'required|unique:mall_master',
        ],$messages);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()->first()
            ],200);
        }

        $mall = new MallMaster();
        $mall->mall_name = $request->mall_name;
        $mall->managed_by = '';
        $mall->mt_id = 1;
        $mall->city_id = 1;
        $mall->country_id = 1;
        $mall->town_id = 1;
        $mall->postal_code = 0;
        $mall->mall_active = 'Y';
        $mall->featured = 'N';
        $mall->telephone = '';
        $mall->business_address = '';
        $mall->website = '';
        $mall->youtube = '';
        $mall->user_id = \Auth::user()->user_id;
        $mall->save();

        return response()->json([
            'status' => 'success',
            'message' => __('successfully added mall'),
            //'tag_name' => $request->time_name,
            //'id' => $time_master->time_id
        ],200);
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
        $mall = MallMaster::find($id);
        $malltypes = MallType::all();
        $countrys = CountryMaster::all();
        $cities = CityMaster::where('country_id',$mall->country_id)->get();
        $towns = TownMaster::where('city_id',$mall->city_id)->get();


        $data = [
            'mall' => $mall,
            'countries' => $countrys,
            'malltypes' => $malltypes,
            'cities' => $cities,
            'towns' => $towns
            //'companys' => $companys
        ];
        return view('main.mall_list.mall_info',$data);
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
        $messages = [
            'mall_name.required'    => 'Mall name field is required',
            'mt_id.required' => 'Mall type field is required',
        ];

        // Start Validation
        $validator = \Validator::make($request->all(), [
            'mall_name' => 'required',
            'mt_id' => 'required',
        ],$messages);

        if($validator->fails()){
            /* return response()->json([
                 'status' => 'error',
                 'message' => $validator->messages()->first()
             ],200);*/

            return redirect()->back()->withInput()->withErrors($validator->errors());
        }


        $mall = MallMaster::find($id);
        $mall->mall_name = $request->mall_name ? $request->mall_name : '';
        $mall->managed_by = $request->managed_by ? $request->managed_by : '';
        $mall->mt_id = $request->mt_id ? $request->mt_id : 1;
        $mall->country_id = $request->country_id ? $request->country_id : 1;
        $mall->city_id = $request->city_id ? $request->city_id : 1;
        $mall->town_id = $request->town_id ? $request->town_id :1;
        $mall->postal_code = $request->postal_code ? $request->postal_code : 0;
        $mall->telephone = $request->telephone ? $request->telephone : '';
        $mall->business_address = $request->business_address ? $request->business_address : '';
        $mall->gps_street = $request->gps_street ? $request->gps_street : '';
        $mall->website = $request->website ? $request->website : '';
        $mall->lat = $request->lat ? $request->lat :'';
        $mall->long = $request->long ? $request->long : '';
        $mall->facebook = $request->facebook ? $request->facebook : '';
        $mall->instagram = $request->instagram ? $request->instagram : '';
        $mall->twitter = $request->twitter ? $request->twitter : '';
        $mall->youtube = $request->youtube ? $request->youtube : '';
        $mall->opening_hour = $request->opening_hour ? $request->opening_hour : '';
        $mall->about_us = $request->about_us ? $request->about_us : '';
        $mall->save();

        /*return response()->json([
            'status' => 'success',
            'message' => __('successfully updated mall'),
            //'tag_name' => $request->time_name,
            //'id' => $time_master->time_id
        ],200);*/

        return redirect()->route('malls.edit',[$id])->with('success','Updated successfully!.');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //$delete = $this->mall->destroy($id);

        $mallmaster = $this->mall->find($id);
        $mall = MallMaster::deleteMallData($id);

        $mallmaster->delete();
        return response()->json([
            'status' => $mall ? 'success' : 'error',
            'message' => $mall ? __('succesfully deleted') : __('error deleting')
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


    public function getTown(Request $request){
        //return $request->id;


        $citys = CityMaster::where('country_id',$request->id)->pluck('city_id');

        if(isset($request->city_id)){
            $towns = TownMaster::where('city_id',$request->city_id)->get(['town_id','town_name','city_id']);
            $total_town = MallMaster::where('country_id',$request->id)->where('city_id',$request->city_id)->where('mall_active','Y')->count();
        }else{
            $towns = TownMaster::whereIn('city_id',$citys)->get(['town_id','town_name','city_id']);
            $total_town = MallMaster::where('country_id',$request->id)->whereIn('city_id',$citys)->where('mall_active','Y')->count();
        }
//return $total_town;
        $tow ='';
        if(count($towns) > 1 ){
            $tow.='<option value="all">All ('.$total_town.')</option>';
        }

        foreach ($towns as $town){
            $total = MallMaster::where('country_id',$request->id)->where('city_id',$town->city_id)->where('town_id',$town->town_id)->where('mall_active','Y')->count();
            $tow.='<option value="'.$town->town_id.'" title="'.$town->town_name.'">'.$town->town_name.' ('.$total.')</option>';
        }

        return response()->json([
            'status' => 'success',
            'message' => __('successfully updated mall'),
            'town' => $tow
        ],200);


    }



    public function getType(Request $request){

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

    public function mallMerchantInfo($mall_id){
        //return $id;
        $mall = MallMaster::find($mall_id);
        $total_merchant = MallMaster::total_merchant($mall_id);

        $locations = MallMaster::locationByMallId($mall_id);

        $levels = LevelMaster::all();
        //$merchant_types = MerchantType::all();
        //return $locations;

        $data = [
            'mall' => $mall,
            'total_merchant' => $total_merchant,
            'locations' => $locations,
            'levels' => $levels
        ];
        return view('main.mall_list.mall_merchant_info',$data);


    }

    public function mallImages($id)
    {

        $mall = MallMaster::find($id);

        //return $mall->mallImage;
        $data = [
            'mall' => $mall,
            'live_url' => env('LIVE_URL').'images/mall_images/',
            'logo_url' => env('LIVE_URL').'images/mall_logo/'
        ];

        return view('main.mall_list.mall_images',$data);
    }


    public function uploadimage(Request $request)
    {
        //return $request->files->get('file');
        //return $request->all();

        $file = $request->files->get('file');

        //return $file->getMimeType();
        try{

            if($file->getMimeType()!="image/png" && $file->getMimeType()!="image/jpeg" && $file->getMimeType()!="image/jpg"){
                throw new \Exception("invalid file", 500);
            }

            $newfilename = md5($request->mall_id."_".round(microtime(true))) . '.png';

            if(isset($request->image_count)){

                $file->move('../../admin/images/mall_images/', $newfilename);

                $mall = new MallImage();
                $mall->mall_id = $request->mall_id;
                $mall->image_name = $newfilename;
                $mall->image_count = $request->image_count;
                $mall->date_added = Carbon::now()->format('Y-m-d');
                $mall->save();

            }else{
                $mall = MallMaster::find($request->mall_id);

                if(isset($request->logo_image)) {
                    $file->move('../../admin/images/mall_logo/', $newfilename);
//                    $mall->main_image = $newfilename;
                    $mall->mall_logo = $newfilename;
                }else{
                    $file->move('../../admin/images/mall_images/', $newfilename);
                    $mall->web_image = $newfilename;
                }
                $mall->save();

            }

        } catch (QueryException $e) {
            throw new \Exception($e->getMessage(), 500, $e);
        }

        //return redirect()->route('malls.images',['mall__id'=>$request->mall_id])->with('success','Uploaded successfully!.');

        //$request->session()->flash('status', 'Document(s) has been uploaded.');
        //return "succeesss upaldsa";
        return response()->json(['success' ,'succesfully uploaded']);

    }


    public function webdeleteimage($id)
    {

        $image = MallMaster::find($id);

        if(!empty($image->web_image)) {
           $file_name = '../../admin/images/mall_images/' . $image->web_image;
            Storage::delete($file_name);

            $image->web_image = Null;
            $image->save();
        }

        return response()->json([
            'status' => $image ? 'success' : 'error',
            'message' => $image ? __('succesfully deleted') : __('error deleting')
        ],200);
    }

    public function deletemallimage($id){

        $image = MallImage::find($id);

        if(!empty($image->image_name)) {
            $file_name = '../../admin/images/mall_images/' . $image->image_name;
            Storage::delete($file_name);
            $delete = MallImage::destroy($id);
        }
        return response()->json([
            'status' => $delete ? 'success' : 'error',
            'image_count' => @$image->image_count,
            'message' => $delete ? __('succesfully deleted') : __('error deleting')
        ],200);
    }

    public function logodeleteimage($id)
    {

        $image = MallMaster::find($id);
        if(!empty($image->mall_logo)) {

            $file_name = '../../admin/images/mall_logo/' . $image->mall_logo;
            Storage::delete($file_name);
//            $image->main_image = Null;
            $image->mall_logo = Null;
            $image->save();
        }

        return response()->json([
            'status' => $image ? 'success' : 'error',
            'image_count' => 9,
            'message' => $image ? __('succesfully deleted') : __('error deleting')
        ],200);
    }

    public function getCityMall(Request $request){


        $citys = CityMaster::where('country_id',$request->id)->get();

        $cit ='';

        $cit.='<option value="">--- Select ----</option>';

        foreach ($citys as $city){

            $cit.='<option value="'.$city->city_id.'" title="'.$city->city_name.'">'.$city->city_name.'</option>';
        }
        //$city = ''
        return response()->json([
            'status' => 'success',
            'message' => __('successfully updated mall'),
            'city' => $cit
        ],200);
    }

    public function getTownMall(Request $request){


        $towns = TownMaster::where('city_id',$request->id)->get(['town_id','town_name','city_id']);
        $tow ='';
        $tow.='<option value="all">--- Select ----</option>';

        foreach ($towns as $town){
            $tow.='<option value="'.$town->town_id.'" title="'.$town->town_name.'">'.$town->town_name.'</option>';
        }

        return response()->json([
            'status' => 'success',
            'message' => __('successfully updated mall'),
            'town' => $tow
        ],200);
    }

    public function mallLevel($id){

        $levels = LevelMaster::all();
        $level_activitys = LevelActivity::all();
        $level_malls = LevelMalls::where('mall_id',$id)->get();

        $data = [
            'levels' => $levels,
            'level_activitys' => $level_activitys,
            'level_malls' => $level_malls,
            'mall_id' => $id,
            'live_url' => env('LIVE_URL').'images/stock/'
        ];

        return view('main.mall_list.level_mall',$data);

    }

    public function storeMallLevel(Request $request){

        $messages = [
            'level_id.required'    => 'Level field is required',
            'level_activity_id.required'    => 'Level Activity field is required'
        ];

        // Start Validation
        $validator = \Validator::make($request->all(), [
            'level_id' => 'required',
            'level_activity_id' => 'required',


        ],$messages);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()->first()
            ],200);
        }


        $malllevel = new LevelMalls();
        $malllevel->mall_id = $request->mall_id;
        $malllevel->level_id = $request->level_id;
        $malllevel->la_id = $request->level_activity_id;
        $malllevel->created_on = Carbon::now()->format('d/m/Y');
        $malllevel->created_by = \Auth::user()->user_id;
        $malllevel->save();

        return response()->json([
            'status' => 'success',
            'message' => __('successfully added!'),
            'id' => $malllevel->mo_id
        ],200);

    }


    public function mallLevelDestroy($id)
    {
        //$delete = $this->mall->destroy($id);

        $mallmaster = LevelMalls::find($id);

        $mallmaster->delete();
        return response()->json([
            'status' => $mallmaster ? 'success' : 'error',
            'message' => $mallmaster ? __('succesfully deleted') : __('error deleting')
        ],200);
    }


}
