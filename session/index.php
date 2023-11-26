<?php
session_start();
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Session</title>
    <link rel="stylesheet" href="/session/lib/css/style.css">
</head>
<body>

    <h1>Session</h1>

    <?if ($_SESSION['logged']):?>
        <div class="logout">
            <p>Здравствуйте <a href="mailto:<?=$_SESSION['email']?>"><?=$_SESSION['name']?></a></p>
            <button class="logout__submit">Разлогинится</button>
        </div>

        <?php

        ?>
    <?else:?>

    <div class="tabs">
        <div class="tabs__head">
            <div class="active" data-number="1">Регистрация</div>
            <div data-number="2">Авторизация</div>
        </div>
        <div class="tabs__body">
            <div class="active" data-number="1">
                <div class="registration">
                    <div class="input-wrap">
                        <span>Логин: </span>
                        <input data-registration="name" placeholder="введите логин">
                        <span>Пароль: </span>
                        <input data-registration="pass" placeholder="введите пароль">
                        <span>Email: </span>
                        <input data-registration="email" placeholder="введите email">
                    </div>
                    <button class="registration__submit">Зарегистрироватся</button>
                    <output></output>
                </div>
            </div>
            <div data-number="2">
                <div class="login">
                    <span>Email: </span>
                    <input data-login="email" placeholder="введите email">
                    <span>Пароль: </span>
                    <input data-login="pass" placeholder="введите пароль">
                    <button class="login__submit">Войти</button>
                </div>
            </div>

        </div>
    </div>




    <?endif;?>

    <script type="text/javascript" src="/session/lib/js/ajax.js"></script>
</body>
</html>
