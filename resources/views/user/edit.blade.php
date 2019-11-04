@extends('layouts.app')

@section('content')
    <div class="row justify-content-center col-md-12">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block col-md-12">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <h1 class="col-md-9 text-center">Change profile data</h1>
        <div class="col-md-7">
            <form method="post" action="{{route('user.update', ['user' => $user->id ])}}">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Name" required
                           value="{{$user->name}}">
                </div>

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com"
                           required value="{{$user->email}}">
                </div>
                @if(auth()->user()->roles == 'manager')
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role">
                            <option selected disabled> Select role</option>
                            <option value="3"> Manager</option>
                            <option value="2">Shift Manager</option>
                            <option value="1">Waiter</option>
                        </select>
                    </div>
                @endif
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div class="btn-block py-4 ">
                <a class="btn btn-primary" href="{{route('user.index')}}">Back to workers</a>
            </div>
        </div>
    </div>
@endsection