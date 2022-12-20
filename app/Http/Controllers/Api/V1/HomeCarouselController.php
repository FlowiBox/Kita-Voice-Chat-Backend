<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\HomeCarouselResource;
use App\Models\HomeCarousel;
use Illuminate\Http\Request;

class HomeCarouselController extends Controller
{
    public function index(){
        $items = HomeCarousel::query ()->where ('enable',1)->orderBy ('sort')->get ();
        $data = HomeCarouselResource::collection ($items);
        return Common::apiResponse (1,'',$data);
    }
}
