@props(['snow' => true])

<div class="christmas-atmosphere" aria-hidden="true">
    <div class="christmas-lights">
        @foreach (range(1, 12) as $light)
            <span class="christmas-light" style="--light-index: {{ $light }}"></span>
        @endforeach
    </div>

    @if ($snow)
        <div class="christmas-snow">
            @foreach (range(1, 16) as $flake)
                <span class="christmas-snowflake" style="--snow-index: {{ $flake }}"></span>
            @endforeach
        </div>
    @endif
</div>
