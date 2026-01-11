@extends('layouts.app')

@section('page-title', 'Create New Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex. Admin, Editor, Staff">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold d-block mb-3">Assign Permissions</label>
                        <div class="row">
                            @foreach($permissions as $permission)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-between border-top pt-3">
                        <a href="{{ route('roles.index') }}" class="btn btn-light">Back</a>
                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection