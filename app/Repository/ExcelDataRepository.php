<?php

namespace App\Repository;

use App\Repository\Interfaces\DataRepositoryInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelDataRepository implements DataRepositoryInterface {
    private $_resultCount;
    private $_serverListData;
    private $_reader;
    private $_inputFileType;
    private $_inputFilePath;

    public function __construct() { 
        $this->_inputFileType = 'Xlsx';
        $this->_inputFilePath = '../public/servers.xlsx';

        $this->_reader = IOFactory::createReader($this->_inputFileType);       
    }

    public function all($filters): array {
        $this->_serverListData = $this->loadData();
        $this->transformData();
        $this->applyFilters($filters['storage'], $filters['hardDiskType'], $filters['memory'], $filters['model'], $filters['location']);
        $this->applyPagination($filters['pageNumber'], $filters['limit']);

        return array("resultCount"=>$this->_resultCount, "results" => $this->_serverListData);
    }

    private function applyFilters(string $storage, string $hardDiskType, string$memory, $model, $location): void {
        $this->_serverListData = ($model !== '') ? $this->stringSearch('model', $model) : $this->_serverListData;
        $this->_serverListData = ($location !== '') ? $this->stringSearch('location', $location) : $this->_serverListData;
        $this->_serverListData = ($hardDiskType !== '') ? $this->stringSearch('hddType', $hardDiskType) : $this->_serverListData;
        $this->_serverListData = ($memory !== '') ? $this->arraySearch($memory) : $this->_serverListData;
        $this->_serverListData = ($storage !== '') ? $this->rangeSearch($storage) : $this->_serverListData;

        $this->_resultCount = count($this->_serverListData);
    }

    private function rangeSearch(string $storageRange): array {
        list($min, $max) = explode(",", $storageRange);
        $searchResults = [];
        foreach($this->_serverListData as $server) { 
            if ( $server['hdd']['value'] >= $min && $server['hdd']['value'] <= $max ) {
                array_push($searchResults, $server);
            }
        }
        return $searchResults;
    }

    private function arraySearch(string $memory): array {
        $selectedMemory = explode("," , $memory);
        $searchResults = [];
        foreach($this->_serverListData as $server) { 
            if(in_array($server['ram']['value'], $selectedMemory) !== false) {
                array_push($searchResults, $server);
            }
        }
        return $searchResults;
    }

    private function stringSearch(string $filterItem, string $searchValue): array {
        $searchResults = [];
        foreach($this->_serverListData as $server) {
            if($filterItem !== 'hddType') {
                if(strpos($server[$filterItem], $searchValue) !== FALSE) {
                    array_push($searchResults, $server);
                }
            } else {
                if(strpos($server['hdd']['type'], $searchValue) !== FALSE) {
                    array_push($searchResults, $server);
                }
            }
            
        }
        return $searchResults;       
    }

    private function applyPagination(int $pageNum, int $limit): void {
        $offset = ($pageNum-1) * $limit;
        $this->_serverListData = array_slice($this->_serverListData, $offset, $limit );
    }

    private function loadData(): array {
        $this->_reader->setReadDataOnly(true);
        $spreadsheet = $this->_reader->load($this->_inputFilePath);
        $worksheet = $spreadsheet->getSheet(0);

        $lastDataRow = $worksheet->getHighestRow();
        $lastDataColumn = 'E';
        $lastDataColumnIndex = Coordinate::columnIndexFromString($lastDataColumn);

        $excelData = array();
        $rowData = []; 

        for ($row = 2; $row <= $lastDataRow; ++$row) {            
            $rowData = [];
            for ($col = 1; $col <= $lastDataColumnIndex; ++$col) {  
                $columnKey = strtolower($worksheet->getCell(array($col, 1))->getValue());
                $columnValue = $worksheet->getCell(array($col, $row))->getValue();
                
                $rowData[$columnKey] = $columnValue;
            }

            $excelData[] = $rowData;
        }
        
        return $excelData;
    }

    private function transformData(): void {
        $tempArr = [];
        foreach($this->_serverListData as $server) {
            $serverInfo = [];
            $serverInfo['model'] = $server['model'];
            $serverInfo['location'] = $server['location'];
            $serverInfo['price'] = $server['price'];
            $serverInfo['hdd'] = $this->transformStorageData($server['hdd'], 'hdd');
            $serverInfo['ram'] = $this->transformStorageData($server['ram'], 'ram');
            array_push($tempArr, $serverInfo);
        }

        $this->_serverListData = $tempArr;
    }

    private function transformStorageData(string $rawData, string $type): array {
        $tranformedData = [];
        $tranformedData['title'] = $rawData; 
        $pattern = "/([0-9x]+)(TB|GB)(.*)/";
        if (preg_match($pattern, $rawData, $matches) > 0 ) {
            $tranformedData['type'] = $matches[3];
            if($type === 'hdd') {
                $tranformedData['value'] = $this->calculateStorageVolume($matches[1], $matches[2]);
            } else {
                $tranformedData['value'] = strval($matches[1]);
            }
            
        } 
        
        return $tranformedData;
    }

    private function calculateStorageVolume(string $storageRawValue, string $storageUnit): string {
        $value = '';
        $valuesToMulitply = explode('x',$storageRawValue);
        $value = $valuesToMulitply[0] * $valuesToMulitply[1];
        if($storageUnit === 'TB') {
            $value = $value * 1000;
        }
        return $value;
    }
}
