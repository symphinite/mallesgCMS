<?php

namespace App;

use App\Http\Resources\PromotionMasterResource;
use DB;

use Illuminate\Database\Eloquent\Model;

class MerchantLocation extends Model
{

    public $timestamps = false;
    protected $primaryKey = 'merchantlocation_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mall_id',
        'merchant_id',
        'level_id',
        'merchant_location',
        'location_details'
    ];

    public function floor()
    {
        return $this->hasOne('App\LevelMaster', 'level_id', 'level_id');
    }

    public function mall()
    {
        return $this->hasOne('App\MallMaster', 'mall_id', 'mall_id');
    }

    public function merchant()
    {
        return $this->hasOne(MerchantMaster::class, 'merchant_id', 'merchant_id');
    }
}
