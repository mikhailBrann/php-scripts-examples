<?php
namespace Lib\Classes;

class User
{
    private $dbConnect = null;
    private const TABLE_NAME = 'Users';

    function __construct($dbObj)
    {
        $this->dbConnect = $dbObj->getConnection();
        return $this->dbConnect;
    }

    public function createUser($name, $password, $email)
    {
        $checkResult = $this->checkUser($email);

        if (empty($checkResult)) {
            $addedUser = $this->dbConnect->prepare("INSERT INTO " . self::TABLE_NAME . " SET `name` = :name, `password` = :password, `email` = :email");
            $addedUser->execute(array('name' => $name, 'password' => $password, 'email' => $email));

            return array(
                'name' => $name,
                'email' => $email,
                'registr_status' => 'ok'
            );

        } else {
            return array(
                'registr_status' => 'error',
                'error_message' => 'Пользователь с таким email уже существует'
            );
        }
    }

    public function checkUser($email)
    {
        //проверяем есть ли пользователь с таким емайл в базе
        $checkUser = $this->dbConnect->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE `email` =:email");
        $checkUser->execute(array('email' => $email));
        $result = $checkUser->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function loginUser($email, $password)
    {
        //проверяем есть ли пользователь с таким емайл и паролем в базе
        $checkUser = $this->dbConnect->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE `email` =:email AND `password` =:password");
        $checkUser->execute(array('email' => $email, 'password' => $password));
        $result = $checkUser->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }
}