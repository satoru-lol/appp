<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ошибка 500</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .container h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .container p {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .container a {
            color: #007bff;
            text-decoration: none;
            font-size: 18px;
        }
        .container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Ой, что-то пошло не так</h1>
    <p>На сервере произошла ошибка. Пожалуйста, попробуйте позже.</p>
    <a href="{{ url('/') }}">Вернуться на главную</a>
    <br>
    <p>Дата: {{now()}}</p>
</div>
</body>
</html>
