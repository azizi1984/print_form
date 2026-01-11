@extends('layouts.app')

@section('page-title', 'Role Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-secondary"><i class="bi bi-shield-lock me-2"></i> Roles List</h5>
                <a class="btn btn-primary btn-sm" href="{{ route('roles.create') }}">
                    <i class="bi bi-plus-lg"></i> Create New Role
                </a>
            </div>
            <div class="card-body p-0">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success m-3">{{ $message }}</div>
                @endif

                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" width="5%">No</th>
                            <th width="20%">Name</th>
                            <th>Permissions</th> <th class="text-end pe-4" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($roles) && $roles->count() > 0)
                            @foreach ($roles as $key => $role)
                            <tr>
                                <td class="ps-4">{{ ++$key }}</td>
                                <td class="fw-bold text-primary">{{ $role->name }}</td>
                                <td>
                                    @if(!empty($role->permissions))
                                        @foreach($role->permissions as $perm)
                                            <span class="badge bg-secondary mb-1">{{ $perm->name }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-outline-warning" href="{{ route('roles.edit', $role->id) }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    ไม่พบข้อมูล Role (No Roles Found)
                                </td>   
                            </tr>
                        @endif
                    </tbody>
                </table>
                
                <div class="p-3">
                    @if(isset($roles) && $roles->count() > 0)
                        {!! $roles->links('pagination::bootstrap-5') !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection