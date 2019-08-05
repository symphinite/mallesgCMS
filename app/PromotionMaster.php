<?php

namespace App;

use App\Http\Resources\PromotionMasterResource;
use DB;

use Illuminate\Database\Eloquent\Model;

class PromotionMaster extends Model
{

    public $timestamps = false;
    protected $table = 'promotions_master';
    protected $primaryKey = 'promo_id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'promo_id',
        'merchant_id',
        'promo_name',
        'description',
        'amount',
        'other_offer',
        'dated',
        'start_on',
        'ends_on',
        'no_end_date',
        'active',
        'dm_id',
        'user_id',
        'redeemable',
        'promo_active'
    ];

    public function merchant(){
        return $this->hasOne('App\MerchantMaster', 'merchant_id', 'merchant_id');
    }

    public function creator(){
        return $this->hasOne('App\User', 'user_id', 'user_id');
    }

    public function promotion_tags(){
        return $this->hasMany('App\PromotionTag', 'promo_id', 'promo_id');
    }


	
}
