<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ServerService;
use Exception;
class ServerController extends Controller
{
    private ServerService $serverService;

    public function __construct(
        ServerService $serverService
    ) {
        $this->serverService = $serverService;
    }

    public function getList(Request $request) {
        try {
            $pageNum = $request->input('_page', 0) ;
            $limit = $request->input('_limit', 10) ;
            $storage = $request->input('storage', '') ;
            $memory = $request->input('memory', '') ;
            $model = $request->input('model', '') ;
            $location = $request->input('location', '') ;
            $hardDiskType = $request->input('hardDiskType', '') ;
             
            $serverData = $this->serverService->getServers($pageNum, $limit, $storage, $memory, $model, $location, $hardDiskType);
            return response()->success("Server list retrieved successfully", $serverData);
        } catch(Exception $e) {
            // return response()->error("Error retrieving server list", 500);
            print_r($e);
            return response()->error("Error retrieving server list", 500);
        }
    }

}
