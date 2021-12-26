<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <title>Wonoly search: @php echo $_GET['q'] @endphp</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="relative h-screen max-w-full">

        @php
            // check if query parameter is blank
            if (empty($_GET['q'])) {
                header('Location: /');
                die();
            }

            include(app_path().'/Scraper/main.php');
            $results = parseResults();
        @endphp

        <div>

        </div>

        <div class="bg-gray-100 w-full h-full">

            <div class="ml-60">

                @foreach ($results['results'] as $result)

                    <div class="bg-white result">
                        <a href="{{ $result['url'] }}" class="text-staleBlue flex items-center">
                            <img src="/api/favicon?url={{ urlencode($result['url']) }}" class="mr-2 w-5 h-5">
                            <div class="w-full cursor-pointer text-title whitespace-nowrap overflow-hidden block no-underline hover:underline">
                                {{ $result['title'] }}
                            </div>
                        </a>
                        <a href="{{ $result['url'] }}" style="color: #7885bf; font-size: 13px;" class="bold font-bold whitespace-nowrap overflow-hidden block">
                            @php
                                $url = parse_url($result['url']);
                                echo $url['host'];
                            @endphp
                        </a>
                        <div style="color: #5a626d; font-size: 14px; result_desc">
                            @php echo $result['description']; @endphp
                        </div>
                    </div>

                @endforeach

            </div>

        </div>

        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
