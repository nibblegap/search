<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <title>Wonoly - Search with out being tracked</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="relative h-screen max-w-full">
        <div class="flex items-center justify-center w-full h-full">
            <form method="GET" action="/search" class="flex item-center p-3 rounded-md min-w-input shadow hover:shadow-md relative bg-white">
                <input type="text" name="q" class="h-full w-full focus:outline-none" placeholder="Search with out being tracked">
                <button class="focus:outline-none" type="submit">
                    <svg style="opacity: .7;" class="cursor-pointer ml-1 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>
    </body>
</html>
