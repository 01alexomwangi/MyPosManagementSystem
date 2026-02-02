@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        <!-- LEFT SIDE: USERS TABLE -->
        <div class="col-md-9">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center"> 
                    <h4>Users</h4>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addUser">
                                <i class="fa fa-plus"></i> Add New User
                            </a>
                        @endif
                    @endauth
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Location</th>
                                <th>Role</th>  
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($users as $key => $user)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->location->name ?? '-' }}</td>
                                <td>{{ $user->role ?? ($user->is_admin ? 'Admin' : 'Cashier') }}</td>
                                <td>
                                    <div class="btn-group">
                                        @auth
                                        @if(auth()->user()->isAdmin())
                                            <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editUser{{ $user->id }}">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteUser{{ $user->id }}">
                                                <i class="fa fa-trash"></i> Del
                                            </a>
                                        @endif
                                        @endauth
                                    </div>
                                </td>
                            </tr>

                            {{-- Edit User Modal --}}
                            <div class="modal right fade" id="editUser{{ $user->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 class="modal-title">Edit User</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  </div>
                                  <div class="modal-body">
                                      <form action="{{ route('users.update', $user->id) }}" method="post">
                                        @csrf
                                        @method('put')

                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                                        </div>
                                        <div class="form-group">
                                            <label>Role</label>
                                            <select name="role" class="form-control" required>
                                                <option value="cashier" {{ $user->role=='cashier' ? 'selected' : '' }}>Cashier</option>
                                                <option value="manager" {{ $user->role=='manager' ? 'selected' : '' }}>Branch Manager</option>
                                                <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Location</label>
                                            <div class="input-group">
                                                <select name="location_id" class="form-control" required>
                                                    <option value="">-- Select Location --</option>
                                                    @foreach($locations as $location)
                                                        <option value="{{ $location->id }}" {{ $user->location_id == $location->id ? 'selected' : '' }}>
                                                            {{ $location->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addLocationModal">
                                                        + Add Location
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-warning btn-block">Update User</button>
                                        </div>
                                      </form>
                                  </div>
                                </div>
                              </div>
                            </div>

                            {{-- Delete User Modal --}}
                            <div class="modal right fade" id="deleteUser{{ $user->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 class="modal-title">Delete User</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  </div>
                                  <div class="modal-body">
                                    <form action="{{ route('users.destroy', $user->id) }}" method="post">
                                      @csrf
                                      @method('delete')
                                      <p>Are you sure you want to delete {{ $user->name }}?</p>
                                      <div class="modal-footer">
                                          <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                                          <button type="submit" class="btn btn-danger">Delete</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- RIGHT SIDE: SEARCH USER -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>Search User</h4>
                </div>
                <div class="card-body">
                    <!-- Add search form if needed -->
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Add User Modal --}}
<div class="modal right fade" id="addUser" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="cashier">Cashier</option>
                            <option value="manager">Branch Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <select name="location_id" class="form-control" required>
                            <option value="">-- Select Location --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @endauth

            <div class="modal-footer">
                <button class="btn btn-primary btn-block">Save User</button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>

{{-- Add Location Modal --}}
<div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addLocationLabel">Add Location</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form action="{{ route('locations.store') }}" method="POST">
          @csrf
          <div class="form-group">
            <label>Location Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter location name" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Add Location</button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.modal.right .modal-dialog{
    top: 0;
    right: 0;
    margin-right: 19vh;
}
.modal.fade:not(.in).right .modal-dialog{
    transform: translate3d(25%,0,0);
}
</style>
@endsection
