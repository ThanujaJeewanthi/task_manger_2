@props([
    'title',
    'headers',
    'items',
    'viewAllRoute' => null,
    'emptyMessage' => 'No items found'
])

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
        @if($viewAllRoute)
            <a href="{{ $viewAllRoute }}" class="btn btn-primary btn-sm">
                <i class="fas fa-list"></i> View All
            </a>
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            {{ $slot }}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="text-center py-4">
                                <i class="fas fa-info-circle text-info mb-2"></i>
                                <p class="mb-0">{{ $emptyMessage }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div> 