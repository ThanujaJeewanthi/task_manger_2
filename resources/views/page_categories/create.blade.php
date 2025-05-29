@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card" style="width:600px;">
                    <div class="card-header">
                        <div class="d-component-title">
                            <span>Create Page Category</span>
                        </div>
                    </div>


                    <div class="card-body">
                        <form action="{{ route('admin.page-categories.store') }}" method="POST">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Category Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
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
                                <button type="submit" class="btn btn-primary">Create Category</button>
                                <a href="{{ route('admin.page-categories.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
