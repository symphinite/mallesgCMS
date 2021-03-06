<?php

namespace App\Http\Controllers;

use App\CountryMaster;
use App\MallMaster;
use App\ManageAge;
use App\Mealgroup;
use App\MerchantLocation;
use App\PreferenceMaster;
use App\PromotionMaster;
use App\PromotionPreference;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as sRequest;
use Illuminate\Support\Facades\Validator;

use App\Repositories\PromotionRepository;
use App\Repositories\MerchantRepository;
use App\MerchantPromoImage;

use Carbon\Carbon;
use Auth;

class PromotionController extends Controller
{

    /**
     * @var MallRepository
     *
     */
    protected $promotion;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantRepository $merchant, PromotionRepository $promotion)
    {
        $this->promotion = $promotion;
        $this->merchant = $merchant;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promotion = $this->promotion->all()->pluck('promo_name', 'promo_id');
        $countrys = CountryMaster::all();
        $promotions = PromotionMaster::all();
//return $promotions;
        $data = [
            'promoOptions' => $promotion->toJson(),
            'countrys' => $countrys,
            'promotions' => $promotions
        ];

        return view('main.promotions.index', $data);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [

        ];

        // Start Validation
        $validator = Validator::make($request->all(), [
            'promo_name' => 'required',
            'merchant_id' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()->first()
            ], 200);
        }

        $insert = $this->promotion->create([
            'user_id' => Auth::user()->user_id,
            'promo_name' => $request->promo_name,
            'merchant_id' => $request->merchant_id,
            'description' => "",
            'dated' => "",
            'start_on' => "",
            'ends_on' => "",
            'no_end_date' => "",
            'active' => "N",
            'promo_active' => "N",
            'dm_id' => 0,
            'redeemable' => "N",
        ]);

        return response()->json([
            'status' => 'success',
            'promo_name' => $request->promo_name,
            'id' => $insert->id
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $merchantOptions = $this->merchant->all()->pluck('merchant_name', 'merchant_id')->toJson() ?? [];
        $current_merchant = $this->merchant->find($id) ?? [];
        $promotions = $current_merchant->promotions;
        $current_promo = $this->promotion->find(request()->promo_id) ?? [];
        $daysofweek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $mall_id_lists = MerchantLocation::with('mall')->where('merchant_id', $id)->distinct()->pluck('mall_id');
        $sub_categoryies = \DB::table('sub_category_master')->select('sub_category_id', 'Sub_Category_Name')->get();
        $preference_master_lists = PreferenceMaster::all();
        $manage_age_lists = ManageAge::all();
        $manage_meal_lists = Mealgroup::all();

        // $preference_list = PromotionPreference::all();
        //return $sub_categoryies;
        $mall_list = [];
        if (!empty($mall_id_lists)) {
            foreach ($mall_id_lists as $key => $list) {
                $mall_name = MallMaster::find($list);
                $mall_list[$key]['mall_id'] = $list;
                $mall_list[$key]['mall_name'] = $mall_name['mall_name'];
            }
        }
        //return $current_promo->promotion_category;
        $data = [
            'merchantOptions' => $merchantOptions,
            'current_merchant' => $current_merchant,
            'promotions' => $promotions,
            'id' => $id,
            'promo_id' => request()->promo_id ?? null,
            'current_promo' => $current_promo,
            'daysofweek' => $daysofweek,
            'promotion_days' => $current_promo->promotion_days ?? [],
            'promotion_categorys' => $current_promo->promotion_category ?? [],
            'promotion_images' => $current_promo->images ?? [],
            'promotion_tags' => $current_promo->promotion_tags ?? [],
            'live_url' => env('LIVE_URL') . 'images/promos/',
            'mall_lists' => $mall_list,
            'sub_category_lists' => $sub_categoryies,
            'preference_lists' => $current_promo->promotion_preference ?? [],
            'preference_master_lists' => $preference_master_lists,
            'manage_age_lists' => $manage_age_lists,
            'promo_age_groups' => $current_promo->promotion_age_group ?? [],
            'live_url_age' => env('LIVE_URL') . 'images/stock/',
            'manage_meal_lists' => $manage_meal_lists,
            'promo_meal_groups' => $current_promo->promotion_meal ?? [],
        ];

        //return $current_promo->promotion_category->rajat;
        //return $data;
        return view('main.promotions.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        // try {
        $request = request();
        $messages = [

        ];

        // Start Validation
        $validator = Validator::make($request->all(), [
            'promo_id' => 'required',
            'merchant_id' => 'required',
            'promo_name' => 'required',
            'description' => 'required',
            'start_on' => 'required',
            'ends_on' => $request->no_end_date=='Y' ? "" : 'required'
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()->first()
            ], 200);
        }


        $update = PromotionMaster::find($request->promo_id);
        $update->merchant_id = $request->merchant_id;
        $update->promo_name = $request->promo_name;
        $update->description = $request->description;
        $update->amount = $request->amount;
        $update->was_amount = $request->was_amount;
        $update->start_on = $request->start_on;
        $update->ends_on = $request->no_end_date ? "" : $request->ends_on;
        $update->other_offer = $request->other_offer ?? null;
        $update->no_end_date = $request->no_end_date ?? "";
//        $update->active = $request->active_txt ?? "";
//        $update->promo_active = $request->active_txt ?? "";
        $update->redeemable = $request->redeemable_txt ?? 1;
//        $update->dine_in = $request->dine_in ?? null;
//        $update->dine_in_service = $request->dine_in_service ?? null;
//        $update->dine_in_gst = $request->dine_in_gst ?? null;
//        $update->take_out = $request->take_out ?? null;
//        $update->take_out_service = $request->take_out_service ?? null;
//        $update->deliver = $request->deliver ?? null;
//        $update->deliver_service = $request->deliver_service ?? null;
//        $update->deliver_gst = $request->deliver_gst ?? null;
        $update->save();

        return response()->json([
            'status' => 'success',
            'message' => __('succesfully updated!'),
            'promo_name' => $request->promo_name,
            'id' => $request->promo_id
        ], 200);
        // } catch (QueryException $e) {
        //     throw new \InvalidArgumentException('Erro inserting', 500, $e);
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = $this->promotion->destroy($id);
        return response()->json([
            'status' => $delete ? 'success' : 'error',
            'message' => $delete ? __('succesfully deleted') : __('error deleting')
        ], 200);
    }

    /**
     *
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function uploadimage(sRequest $request)
    {

        $file = $request->files->get('file');

        try {

            if ($file->getMimeType() != "image/png" && $file->getMimeType() != "image/jpeg" && $file->getMimeType() != "image/jpg") {
                throw new \Exception("invalid file", 500);
            }


            $newfilename = md5($request->promo_id . "_" . $request->merchant_id . "_" . round(microtime(true))) . '.png';

            if (env('APP_ENV') == 'live')
                $file->move('../../admin/images/promos/', $newfilename);
            else
                $file->move('../storage/app/public/', $newfilename);

            MerchantPromoImage::create([
                'promo_id' => $request->promo_id,
                'merchant_id' => $request->merchant_id,
                'image_name' => $newfilename,
                'image_count' => $request->image_count,
                'date_added' => Carbon::now()
            ]);

        } catch (QueryException $e) {
            throw new \Exception($e->getMessage(), 500, $e);
        }

        return response()->json(['success', 'succesfully uploaded']);

        return response()->json([
            'status' => 'success',
            'message' => __('succesfully uploaded'),
            'file' => env("LIVE_URL") . $newfilename
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteimage($id)
    {

        $image = MerchantPromoImage::find($id);

        if (env('APP_ENV') == 'live')
            unlink('../../admin/images/promos/' . $image->image_name);
        else
            unlink('../storage/app/public/' . $image->image_name);

        $delete = MerchantPromoImage::destroy($id);
        return response()->json([
            'status' => $delete ? 'success' : 'error',
            'message' => $delete ? __('succesfully deleted') : __('error deleting')
        ], 200);
    }


    public function getLocation(Request $request)
    {
        //return $request->mall_id;
        if ($request->ajax()) {
            if ($request->mall_id != NULL && $request->merchent_id != NULL) {
                $mall_id = $request->mall_id;
                $merchent_id = $request->merchent_id;

                $locations = MerchantLocation::where('merchant_id', $merchent_id)->where('mall_id', $mall_id)->get();
                $loc = "";
                if (count($locations) > 0) {

                    if (count($locations) > 1) {
                        $loc .= "<option value=''>--- Select ----</option>";
                    }
                    foreach ($locations as $location) {
                        $loc .= '<option value="' . $location->merchantlocation_id . '">' . $location->merchant_location . '</option>';
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => __('succesfully uploaded'),
                        'location' => $loc
                    ], 200);

                } else {

                    $loc .= "<option value=''>--- Select ----</option>";
                    $loc .= "<option value=''>NO Data Found</option>";

                    return response()->json([
                        'status' => 'success',
                        'message' => __('succesfully uploaded'),
                        'location' => $loc
                    ], 200);
                }
            } else {
                return response()->json([
                    'location' => "No Data Found"
                ], 200);

            }
        }

        return response()->json([
            'location' => "No Data Found"
        ], 200);
    }

    public function activeUp(Request $request)
    {

        $name = $request->name;
        $id = $request->promo_id;
        $promo_master = PromotionMaster::find($id);
        $promo_master->$name = $request->value;

        if ($name == "no_end_date" && $request->value == 'Y') {
            $promo_master->ends_on = null;
        }

        $promo_master->save();

        return response()->json([
            'status' => 'success',
            'message' => __('successfully updated !'),
            'id' => $id
        ], 200);
    }


}
