<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>cookies</title>
</head>
<body>
<h1>Todo list</h1>
<div class="todo-list">
    <div class="todo-list__wrap">
    </div>

    <div class="todo-list__control">
        <input type="text" class="todo-list__control-field">
        <button class="todo-list__control-submit">Добавить пункт</button>
    </div>

    <div class="todo-list__err-control">
        <input type="checkbox" id="err_control">
        <label for="err_control">неудачный запрос</label>
    </div>

    <div class="todo-list__clear">
        <button class="todo-list__clear-btn">Очистить список</button>
    </div>

</div>

<script type="text/javascript" src="/cookies/lib/ajax.js"></script>
</body>
</html>
