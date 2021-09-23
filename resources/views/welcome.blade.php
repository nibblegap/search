<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <title>Wonoly - Search with out being tracked</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- AOS -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="bg-gray-100 relative h-screen max-w-full">
        <div class="flex items-center justify-center w-full min-h-screen">
            <form data-aos="fade-up" method="GET" action="/search" class="flex item-center p-3 rounded-md min-w-input shadow hover:shadow-md relative bg-white">
                <input type="text" name="q" class="h-full w-full focus:outline-none" placeholder="Search with out being tracked">
                <button class="focus:outline-none" type="submit">
                    <svg style="opacity: .5;" class="cursor-pointer ml-1 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <div class="w-10 h-10 absolute" style="bottom: 9%; left: 50%; transform: translate(-50%, -50%)">
            <a href="#intro" data-aos-delay="500" class="w-full h-full rounded-md cursor-pointer bg-white shadow flex items-center justify-center" data-aos="zoom-in">
                <svg style="opacity: .5;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3"></path></svg>
            </a>
        </div>

        <section class="bg-white w-full text-center pt-20" id="intro">
            <h1 class="text-7xl opacity-75" data-aos="zoom-in">
                Very customizeable
            </h1>

            <div class="flex w-full item-center justify-between"></div>
        </section>

        <br>
        <br>
        <br>
        <br>
        <br>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init();
        </script>
    </body>
</html>
