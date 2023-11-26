# htpasswd (Настройка аутентификации по паролю для Apache)


## Переменные:
создаем файлик .env и помещаем туда нужные переменные.
пример:
```bash
MYSQL_ROOT_PASSWORD=qwe123456
MYSQL_HOST=mysql
MYSQL_DATABASE=app_db
MYSQL_USER=user_app
MYSQL_PASSWORD=qwe667788
```


## Шаги для запуска:
1) в терминале в корне вызываем: ```docker-compose up --build```
2) сайт: [http://10.0.0.10](http://10.0.0.10)
3) логин и пароль для аутентификации: test

