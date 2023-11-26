<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/lib/config.php';

$db = new Lib\Classes\DBClass(DB_NAME, DB_USER, DB_PASS);
$loginUser = new Lib\Classes\User($db);
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if ($data["responseType"] == "login") {

    if (!empty($data["inputData"])) {
        $inputData = $data["inputData"];
        $requestUserData = $loginUser->loginUser($inputData["email"], $inputData["pass"]);

        if(!empty($requestUserData)) {
            session_start();
            $_SESSION['logged'] = true;
            $_SESSION['name'] = $requestUserData[0]["name"];
            $_SESSION['email'] = $requestUserData[0]["email"];

            echo json_encode(array(
                'registr_status' => 'ok',
                'name' => $requestUserData[0]["name"],
                'email' => $requestUserData[0]["email"]
            ));
        } else {
            echo json_encode(array(
                'registr_status' => 'error',
                'error_message' => 'Неверно указаны данные ввода'
            ));
        }
    }
}
