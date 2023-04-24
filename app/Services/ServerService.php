<?php
namespace App\Services;

use App\Repository\ExcelDataRepository;
class ServerService {
    private $_serverListData;
    private $_excelDataRepository;

    public function __construct(ExcelDataRepository $excelDataRepository) { 
        $this->_excelDataRepository = $excelDataRepository;
    }

    public function getServers($pageNum, $limit, $storage, $memory, $model, $location, $hardDiskType) {
        $dataFilters = array(
            "pageNumber" => $pageNum,
            "limit" => $limit,
            "storage" => $storage,
            "memory" => $memory,
            "model" => $model,
            "location" => $location,
            "hardDiskType" => $hardDiskType
        );
        $this->_serverListData = $this->_excelDataRepository->all($dataFilters);
  
        return $this->_serverListData;
    }

}
