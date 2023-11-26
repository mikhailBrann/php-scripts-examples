<?php
require $_SERVER["DOCUMENT_ROOT"] . '/oop/vendor/autoload.php';
use Bin\Classes;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['send_file'])) {
    //устанавливаем заголовок и получаем тело запроса
    header('Content-type: application/json');
    $jsonInputData = file_get_contents('php://input');
    $inputData = json_decode($jsonInputData, true);

    if ($inputData['diskType'] == 'yandex') {
        $yandexDisk = new Classes\YandexCloudClass('yandex', API_CONFIG[$inputData['diskType']]['api_path']);

        switch ($inputData['responseType']) {
            case 'getList':
                $requestPath = !empty($inputData['responsePath']) ? $inputData['responsePath'] : '/';
                $requestPath = substr($requestPath, -1) != '/' ? $requestPath . '/' : $requestPath;

                //задаем лимит и пагинацию для запроса данных о файлах с api
                $requestPathParam = '';
                if (!empty(API_CONFIG[$inputData['diskType']]['limit'])) {
                    $requestPathParam = '&limit=' . API_CONFIG[$inputData['diskType']]['limit'];
                }

                if (!empty($inputData['offset'])) {
                    $requestPathParam .= '&offset=' . $inputData['offset'];
                }

                $resultPath =  $requestPath . $requestPathParam;

                $dataResult = $yandexDisk->getList($resultPath);
                $result = [];

                http_response_code($dataResult['response']['status']);

                if ($dataResult['response']['status'] <= 299) {
                    $itemsJson = json_decode($dataResult['response']["data"], true);
                    $items = $itemsJson["_embedded"]['items'];

                    foreach ($items as $key => $file) {
                        $result[$key] = [
                            'name' => $file['name'],
                            'type' => $file['type'],
                            'path' => $file['path']
                        ];
                    }

                    echo json_encode([
                        'getList' => $result,
                        'currentPath' => $requestPath,
                        'status' => $dataResult['response']['status'],
                        'limit' => $itemsJson["_embedded"]["limit"],
                        'total' => $itemsJson["_embedded"]["total"],
                        'offset' => !empty($inputData['offset']) ? $inputData['offset']: 0
                    ]);
                } else {
                    echo json_encode([
                        'getList' => false,
                        'currentPath' => $requestPath,
                        'status' => $dataResult['response']['status']
                    ]);
                }
            break;

            case 'addFolder':
                $dataResult = $yandexDisk->addFolder($path=$inputData['responsePath']);
                http_response_code($dataResult['response']['status']);

                echo json_encode([
                    'result' => $dataResult
                ]);
            break;

            case 'removeItem':
                $dataResult = $yandexDisk->removeElem($path=$inputData['responsePath']);
                http_response_code($dataResult['response']['status']);

                echo json_encode([
                    'result' => $dataResult
                ]);
            break;

            case 'renameItem':
                $yandexDisk = new Classes\YandexCloudClass('yandex', API_CONFIG[$inputData['diskType']]['api_path'], $urlModificate='/move');
                $responseParams = '?from=' . $inputData['from'] . '&path=' . $inputData['path'] . '&overwrite=true';
                $dataResult = $yandexDisk->renameElem($path=$responseParams);
                http_response_code($dataResult['response']['status']);

                echo json_encode([
                    'result' => $dataResult
                ]);
            break;

            case 'getItem':
                $yandexDisk = new Classes\YandexCloudClass('yandex', API_CONFIG[$inputData['diskType']]['api_path'], $urlModificate='/download?path=');
                $dataResult = $yandexDisk->downloadElem($path=$inputData['responsePath']);
                http_response_code($dataResult['response']['status']);

                echo json_encode([
                    'result' => $dataResult
                ]);
            break;

        }
    }
}

if (isset($_GET['send_file']) && $_GET['send_file']) {
    if (isset($_GET['disk_type'])) {
        $buferFolder = $_SERVER["DOCUMENT_ROOT"] . '/oop/upload/';

        switch ($_GET['disk_type']) {
            case 'yandex':
                $dirWorker = new Classes\WorkDirClass($buferFolder);
                $dirWorker->clearDir();

                $safeFilePath = $dirWorker->saveFile($_FILES['send_file']['tmp_name'], $_GET['file_name']);

                if($safeFilePath && !empty($_GET['cur_dir'])) {
                    $yandexDisk = new Classes\YandexCloudClass(
                        $_GET['disk_type'],
                        API_CONFIG[$_GET['disk_type']]['api_path'],
                        $urlModificate='/upload?overwrite=true&'
                    );
                    $folderPAth = substr($_GET['cur_dir'], -1) != '/' ? $_GET['cur_dir'] . '/' : $_GET['cur_dir'];
                    $path = 'path=' . $folderPAth . $_GET['file_name'];

                    $dataResult = $yandexDisk->uploadFileLink($path);


                    if ($dataResult['response']['status'] < 299) {
                        $requestData = json_decode($dataResult['response']['data']);
                        $sendFile = $yandexDisk->toSendFile($requestData->href, $requestData->method, $safeFilePath, $_GET['file_name']);

                        http_response_code($dataResult['response']['status']);
                        echo json_encode([
                            'result' => $sendFile
                        ]);
                    } else {
                        http_response_code($dataResult['response']['status']);

                        echo json_encode([
                            'result' => $dataResult
                        ]);
                    }
                }
            break;
        }
    }
}



