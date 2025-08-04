<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Grade;

class Stage extends Model
{
    use HasFactory;
        protected $guarded = [];

        public static function getIdByTag($tag){
            $stage = self::query()->where('tag',$tag)->first();
            return $stage->id;
        }

}
