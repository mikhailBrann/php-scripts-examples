<?php
class CookieHelperClass
{
    public function getClientCookieList()
    {
        if (!empty($_COOKIE["USER_LIST"])) {
            return $_COOKIE["USER_LIST"];
        } else {
            return [];
        }

    }

    public function setClientCookie($cookieKey, $cookieVal)
    {
        $result = setcookie('USER_LIST[' . $cookieKey . ']', $cookieVal, time() + 3600, '/cookies/');
        if ($result) {
            return [
                'added' => $result,
                'value' => $cookieVal,
                'count' => $cookieKey
            ];
        } else {
            header("HTTP/1.1 400 Bad Request");
            return [];
        }
    }

    public function clearClientList($count=false)
    {
        if ($count) {
            $result = setcookie('USER_LIST[' . $count . ']', '', time() - 3600, '/cookies/');

            return $result;
        } else {
            if (!empty($_COOKIE["USER_LIST"])) {
                foreach ($_COOKIE["USER_LIST"] as $key => $val) {
                    setcookie('USER_LIST[' . $key . ']', $val, time() - 3600, '/cookies/');
                }
            }

            return [];
        }

    }

    public function errRequest() {
        header("HTTP/1.1 500 Server error");
        return [];
    }
}

