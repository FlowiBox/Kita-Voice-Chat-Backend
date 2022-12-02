<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Models\RoomCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function allCountries(){
        $countries = Country::query ()->where ('status',1)->get ();
        return Common::apiResponse (1,'',CountryResource::collection ($countries));
    }
    public function getCountry($id){
        $country = Country::find($id);
        if ($country){
            return Common::apiResponse (1,'',new CountryResource($country));
        }
        return Common::apiResponse (0,__ ('not found'));
    }

    public function allClasses(){
        return Common::apiResponse (1,'',RoomCategory::query ()->whereDoesntHave ('parent')->select ('id','name','img')->get ());
    }

    public function getClassChildren($id){
        $class = RoomCategory::query ()->find ($id);
        if ($class){
            return Common::apiResponse (1,'',$class->children);
        }
        return Common::apiResponse (0,'not found');
    }

    public function getTypes(){
        return Common::apiResponse (1,'',RoomCategory::query ()->whereHas ('parent')->select ('id','name','img')->get ());
    }
}
