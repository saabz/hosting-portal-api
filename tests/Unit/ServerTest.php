<?php

namespace Tests\Unit;

use App\Repository\ExcelDataRepository;
use App\Services\ServerService;
use Mockery;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{    
    public function _setup() {

    }
    public function testIfServiceReturnsDataForMatchingFilters()
    {
        
        $pageNum =1;
        $memory = '32GB';
        $storage = '';
        $limit = 3;
        $model = '';
        $location = '';
        $hardDiskType = '';

        $mockData = [
            "resultCount"=> 107,
            "results"=> array(                      
                "model"=> "HP DL180G62x Intel Xeon E5620",
                "ram"=> "32GBDDR3",
                "hdd"=> "8x2TBSATA2",
                "location"=> "AmsterdamAMS-01",
                "price"=> "€119.00"
            ),
            array(    
                "model"=> "HP DL380eG82x Intel Xeon E5-2420",
                "ram"=> "32GBDDR3",
                "hdd"=> "8x2TBSATA2",
                "location"=> "AmsterdamAMS-01",
                "price"=> "€131.99"
            ),
            array(
                "model"=> "IBM X36302x Intel Xeon E5620",
                "ram"=> "32GBDDR3",
                "hdd"=> "8x2TBSATA2",
                "location"=> "AmsterdamAMS-01",
                "price"=> "€106.99"
            )];
        $excelDataRepoMock = $this->createMock(ExcelDataRepository::class);
        $excelDataRepoMock->method('all')->with([
            "pageNumber"=>$pageNum,
            "memory"=>$memory,
            "storage"=>$storage,
            "limit"=>$limit,
            "model"=> $model,
            "location"=> $location,
            "hardDiskType"=> $hardDiskType
        ])->willReturn($mockData);

        $serverService = new ServerService($excelDataRepoMock);
        $results = $serverService->getServers($pageNum, $limit, $storage, $memory, $model, $location, $hardDiskType);
        
        $this->assertEquals($mockData, $results);
    }
    
    public function testIfServiceReturnEmptyForUnmatchedFilters()
    {
        $pageNum =1;
        $memory = '43GB';
        $storage = '';
        $limit = 3;
        $model = '';
        $location = '';
        $hardDiskType = '';

        $mockData = [
            "resultCount"=> 0,
            "results"=> []
        ];
        $excelDataRepoMock = $this->createMock(ExcelDataRepository::class);
        $excelDataRepoMock->method('all')->with([
            "pageNumber"=>$pageNum,
            "memory"=>$memory,
            "storage"=>$storage,
            "limit"=>$limit,
            "model"=> $model,
            "location"=> $location,
            "hardDiskType"=> $hardDiskType
        ])->willReturn($mockData);

        $serverService = new ServerService($excelDataRepoMock);
        $results = $serverService->getServers($pageNum, $limit, $storage, $memory, $model, $location, $hardDiskType);
        
        $this->assertEquals($mockData, $results);
    }
}
