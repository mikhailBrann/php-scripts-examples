## Сессии

для работы с скрипта сначала:
1. создайте таблицу в базе данных:
```sql
CREATE DATABASE lern_session;
```

2.Выберите ее:
```sql
use lern_session
```

3.Создайте в ней таблицу с пользователями:
```sql
CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(160) NOT NULL,
    password VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    reg_date TIMESTAMP NOT NULL DEFAULT NOW()
);
```