<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\HomeCarouselResource;
use App\Models\HomeCarousel;
use Illuminate\Http\Request;

class HomeCarouselController extends Controller
{
    public function index(Request $request){
        $items = HomeCarousel::query ()->where ('enable',1)->orderBy ('sort');
        if ($request->type){
            $items = $items->where('type',$request->type);
        }
        $items = $items->get ();
        $data = HomeCarouselResource::collection ($items);
        return Common::apiResponse (1,'',$data);
    }
}
