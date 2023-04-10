<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RealsController extends Controller
{
    public function index(Request $request){
        $t = $request->by_interests | false;
        $user = $request->user ();
        $tags = $user->tags()->pluck('tags.id')->toArray();
        $videos = Video::query ();

        if ($t){
            $videos = $videos->whereHas ('tags',function ($q) use ($tags){
                $q->whereIn('tags.id',$tags);
            });
        }

        $videos = $videos->orderBy('views_num','desc')
            ->orderBy('likes_num','desc')
            ->orderBy('comments_num','desc')
            ->orderBy('shares_num','desc')
            ->paginate(50)
        ;

        return Common::apiResponse (1,'ok',VideoResource::collection ($videos),200,Common::getPaginates ($videos));
    }

    public function store(Request $request){
        if (!$request->title || !$request->file ('video')){
            return Common::apiResponse (0,'missing params',null,422);
        }
        $user = $request->user ();
        $tags = $user->tags()->pluck('tags.id')->toArray();
        try {
            DB::beginTransaction ();
            $video = Video::query ()->create (
                [
                    'title'=>$request->title,
                    'description'=>$request->description,
                    'duration'=>$request->duration,
                    'author_id'=>$user->id,
                ]
            );
            $video->tags()->attach($tags);
            if ($request->hasFile ('video')){
                set_time_limit(120);
                $url = Common::upload ('videos',$request->file ('video'));
                $video->url = $url;
                $video->save();
            }
            DB::commit ();
            return Common::apiResponse (1,'ok',new VideoResource($video),200);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'fail',null,400);
        }
    }
}
