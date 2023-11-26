# Yandex disk

Копируем функционал Я.диска

## Запуск проекта
1. На сайте [Яндекс полигон](https://yandex.ru/dev/disk/poligon/) получаем токен для работы с api
2. Создаем файл с название .env и помещаем в него токен из первого пункта
```bash
    TOKEN="You token"
```
3. В терминале запускаем composer
```bash
    composer install
```

## Файл config
Config-файл расположен по пути ./config/config.php
в нем указан тип диска, url api и лимит на вывод файлов для пагинации

