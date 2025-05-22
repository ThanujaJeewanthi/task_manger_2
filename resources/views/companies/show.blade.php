@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Company Details</span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-component-container">


                        <div class="mb-4">
                            <label class="d-label-text">Company Name</label>
                            <p>{{ $company->name }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Address</label>
                            <p>{{ $company->address }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Phone</label>
                            <p>{{ $company->phone ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Email</label>
                            <p>{{ $company->email ?? 'N/A' }}</p>
                        </div>



                        <div class="mb-4">
                            <label class="d-label-text">Has Clients</label>
                            <span class="badge {{ $company->has_clients ? 'bg-success' : 'bg-secondary' }}">
                                {{ $company->has_clients ? 'Yes' : 'No' }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Status</label>
                            <span class="badge {{ $company->active ? 'bg-success' : 'bg-danger' }}">
                                {{ $company->active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('companies.edit', $company) }}" class="btn btn-info">Edit Company</a>
                            <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this company?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger ms-2">Delete Company</button>
                            </form>
                            <a href="{{ route('companies.index') }}" class="btn btn-secondary ms-2">Back to Companies</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
