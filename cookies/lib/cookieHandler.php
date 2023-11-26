<?php
include_once 'CookieHelperClass.php';
header('Content-type: application/json');

$cookieHendler = new CookieHelperClass;
$jsonInputData = file_get_contents('php://input');
$inputData = json_decode($jsonInputData, true);

switch ($inputData['responseType']) {
    case 'error':
        $errorResult = $cookieHendler->errRequest();

        $result = [
            'status' => 'error',
            'result' => $errorResult
        ];

        echo json_encode($result);

        break;
    case 'getList':
        $cookieList = $cookieHendler->getClientCookieList();

        if (!empty($cookieList)) {
            $result = [
                'status' => 'ok',
                'result' => $cookieList
            ];
    
            echo json_encode($result);
        } else {
            echo json_encode([
                'result' => 'error'
            ]);
        }

        break;
    case 'addItemList':
        if (!empty($inputData['userlist'])) {
            $result = false;
    
            foreach ($inputData['userlist'] as $key => $value) {
                $result = $cookieHendler->setClientCookie($key, $value);
            }
    
            echo json_encode(['result' => $result]);
        } else {
            echo json_encode(['result' => 'error']);
        }

        break;
    case 'removeList':
        if (!empty($inputData['removeCount'])) {
            $result = $cookieHendler->clearClientList($inputData['removeCount']);
    
            echo json_encode([
                'result' => $result,
                'removeCount' => $inputData['removeCount']
            ]);
        } else {
            $cookieHendler->clearClientList();
            echo json_encode(['result' => 'clear']);
        }

        break;
    default:
        $errorResult = $cookieHendler->errRequest();

        $result = [
            'status' => 'error',
            'result' => $errorResult
        ];

        echo json_encode($result);

        break;     
}

