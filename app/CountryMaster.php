<?php

namespace App;

use App\Http\Resources\PromotionMasterResource;
use DB;

use Illuminate\Database\Eloquent\Model;

class CountryMaster extends Model
{

    public $timestamps = false;
    protected $table = 'country_master';
    protected $primaryKey = 'country_id';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    public static function totalCountryMerchant($cid){
        if(!empty($cid)){
            $total = MerchantMaster::where('country_id',$cid)->where('merchant_active','Y')->count();
            return $total;
        }
        return 0;
    }

    public static function totalCountryMall($cid){
        if(!empty($cid)){
            $total = MallMaster::where('country_id',$cid)->where('mall_active','Y')->count();

            return $total;
        }
        return 0;
    }

    public static function totalCountryPromotionMerchant($cid){
        if(!empty($cid)){
            //$total = MerchantMaster::where('country_id',$cid)->where('merchant_active','Y')->count();
            $total = PromotionMaster::leftJoin('merchant_master', function ($join){
                $join->on('promotions_master.merchant_id','=','merchant_master.merchant_id');
            })->where('country_id',$cid)->count();

            return $total;
        }
        return 0;
    }

    public static function getCountryName($id){

        if(!empty($id)){
            $country = CountryMaster::find($id);

            return $country->country_name;
        }

        return '---';


    }

}
