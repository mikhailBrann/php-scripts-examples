<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/lib/config.php';

$db = new Lib\Classes\DBClass(DB_NAME, DB_USER, DB_PASS);
$createUser = new Lib\Classes\User($db);
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if ($data["responseType"] == "registration") {
    $inputData = $data["inputData"];

    if (!empty($inputData)) {
        $createUser = $createUser->createUser($inputData["name"], $inputData["pass"], $inputData["email"]);
        echo json_encode($createUser);
    }
}
