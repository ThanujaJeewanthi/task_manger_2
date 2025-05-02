@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Role</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Role Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>


<div class="d-com-flex justify-content-start mb-4">
    <label class="d-label-text me-2">Status</label>
    <label class="d-toggle position-relative">
        <input type="checkbox" class="form-check-input d-section-toggle" name="active" {{ old('active', $role->active) ? 'checked' : '' }} />
        <span class="d-slider">
            <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
            <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
        </span>
    </label>
</div>

                        <div class="form-group">

                            <button type="submit" class="btn btn-primary">Update Role</button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
