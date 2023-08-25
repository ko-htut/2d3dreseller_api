<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use VanOns\Laraberg\Traits\RendersContent;
class Ads extends Model
{
    use HasFactory;

    

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function boot()
    {   
        
        parent::boot();
        Ads::saving(function (Ads $ads) {
            $ads->admin_id=Auth::id();
        });

        Ads::updating(function (Ads $ads) {
            //
        });
    }
}