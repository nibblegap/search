<div>

    @php
        // Use the second parameter of json_decode to make it return an array
        $results = json_decode($results, true);
    @endphp

    @foreach ($results['results'] as $result)

        @php
            $url = parse_url($result['url']);
        @endphp

        <div class="bg-white result">
            <a href="{{ $result['url'] }}" class="text-staleBlue flex items-center">
                <img src="//external-content.duckduckgo.com/ip3/{{ $url['host'] }}.ico" class="mr-2 w-5 h-5">
                <div class="w-full cursor-pointer text-title whitespace-nowrap overflow-hidden block no-underline hover:underline underline-offset-4">
                    {{ $result['title'] }}
                </div>
            </a>
            <a href="{{ $result['url'] }}" style="color: #7885bf; font-size: 13px;" class="bold font-bold whitespace-nowrap overflow-hidden block">
                @php
                    echo $url['host'];
                @endphp
            </a>
            <div class="result_desc" onauxclick="event.button != 1 ? window.location.href='{{ $result['url'] }}' : window.open('{{ $result['url'] }}', '_blank')">
                @php echo $result['description']; @endphp
            </div>
        </div>

    @endforeach
</div>