<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Services\API\General\pageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    use ApiResponse;

    protected $pageService;

    public function __construct(pageService $pageService)
    {
        $this->pageService = $pageService;
    }
    public function getPages()
    {
        $result = $this->pageService->getPages();
        if($result)
        {
            return $this->success($result['data'],$result['message'],200);
        }
        return $this->error($result['message'],404);
    }
}
