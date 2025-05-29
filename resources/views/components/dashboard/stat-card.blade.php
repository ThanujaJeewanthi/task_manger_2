@props([
    'title',
    'value',
    'icon',
    'color' => 'primary',
    'link' => null
])

<div class="card border-left-{{ $color }} shadow h-100 py-2">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
            <div class="col mr-2">
                <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                    {{ $title }}
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    @if($link)
                        <a href="{{ $link }}" class="text-decoration-none text-gray-800">
                            {{ $value }}
                        </a>
                    @else
                        {{ $value }}
                    @endif
                </div>
            </div>
            <div class="col-auto">
                <i class="{{ $icon }} fa-2x text-gray-300"></i>
            </div>
        </div>
    </div>
</div> 