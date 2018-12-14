<?php
?>
<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{csrf_token()}}">
        <title>Check Availability</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href=" {{ mix('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="wrapper" id="app">
            <availability-component></availability-component>
        </div>
        <script src="/js/bootstrap.js"></script>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
    </html>