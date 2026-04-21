<?php

namespace App\Services\API\General;

use App\Http\Resources\API\PageResource;
use App\Models\Page;
use App\Traits\ApiResponse;

class pageService
{
    use ApiResponse;
    public function getPages()
    {
        $pages = Page::all();
        if($pages->isEmpty())
        {
            return[
                "status"=>false,
                "message"=>__('messages.page_not_found'),
                'data'=>[]
            ];
        }
         return[
                "status"=>true,
                "message"=>__('messages.page_recived_successfully'),
                'data'=> PageResource::collection($pages)
            ];
    }
}
