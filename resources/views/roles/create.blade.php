@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Create New Role</span>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mt-3">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf

                        <div class="d-component-container">
                            <!-- Role Name Field -->
                            <div class="form-group mb-4">
                                <label for="name">Role Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Role Status Toggle -->
                            <div class="d-com-flex justify-content-start mb-4">
                                <label class="d-label-text me-2">Active</label>
                                <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                    <input type="checkbox" class="form-check-input d-section-toggle" name="is_active" checked />
                                    <span class="d-slider " >
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>

                            </div>

                            <!-- Page Permissions Section -->
                            {{-- <div class="container">
                                <div class="row" style="display: flex; flex-wrap: wrap;">
                                    @foreach($pageCategories as $category)

                                    <div class="col-md-4 col-lg-3 col-sm-12" style=" padding: 10px; box-sizing: border-box;">
                                            <div class="card-header">
                                                <h3 class="card-title">{{ $category->name }}</h3>
                                            </div>
                                            <div class="card-body">
                                                @foreach($category->pages as $page)
                                                <div class="page-item border-bottom py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-com-flex justify-content-start">
                                                            <label class="d-label-text  ">{{ $page->name }}</label>
                                                            <label class="d-toggle  float-right">
                                                                <input type="checkbox" class="form-check-input d-section-toggle" name="page_status[{{ $page->id }}]" checked />
                                                                <span class="d-slider">
                                                                    <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                                                    <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    @endforeach
                                </div>
                            </div> --}}

                            <!-- Submit Button -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create Role</button>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>






            </div>
        </div>
    </div>
</div>
@endsection
