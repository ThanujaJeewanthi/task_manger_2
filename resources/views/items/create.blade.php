@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-component-title">
                            <span>Create New Item</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('items.store') }}" method="POST">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Item Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    id="description" name="description" rows="3" placeholder="Enter item description">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="sku">SKU</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                    id="sku" name="sku" value="{{ old('sku') }}" placeholder="e.g. ITM-001">
                                <small class="form-text text-muted">Stock Keeping Unit (optional)</small>
                                @error('sku')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="unit">Unit</label>
                                <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                    id="unit" name="unit" value="{{ old('unit') }}" placeholder="e.g. pcs, kg, lbs">
                                <small class="form-text text-muted">Unit of measurement (optional)</small>
                                @error('unit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="unit_price">Unit Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control @error('unit_price') is-invalid @enderror"
                                        id="unit_price" name="unit_price" value="{{ old('unit_price') }}" 
                                        step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="form-text text-muted">Price per unit (optional)</small>
                                @error('unit_price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <div class="d-com-flex justify-content-start mb-4">
                                    <label class="d-label-text me-2">Active</label>
                                    <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                        <input type="checkbox" class="form-check-input d-section-toggle" id="active"
                                            name="active" checked />
                                        <span class="d-slider ">
                                            <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                            <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary">Create Item</button>
                                <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush