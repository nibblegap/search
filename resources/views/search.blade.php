<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <title>Wonoly search: <?php

use Symfony\Component\Mime\Header\Headers;

echo $_GET['q'] ?></title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="relative h-screen max-w-full">

        <!-- check if query parameter is blank -->
        <?php
            if (empty($_GET['q'])) {
                header('Location: /');
                die();
            }
        ?>

        Search site : ?q = <?php echo $_GET['q'] ?>
    </body>
</html>
