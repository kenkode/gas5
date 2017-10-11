@extends('layouts.system')
@section('content')

<br/>

<div class="row">

    @if (Session::has('success'))
            <div class="alert alert-error alert-success">
                
                    {{ Session::get('success') }}
               
            </div>
        @endif

	<div class="col-lg-5">

		<table class="table table-condensed table-bordered">

            <tr>
                <td>username</td><td>{{ $user->username}}</td>
            </tr>
            <tr>
                <td>email</td><td>{{ $user->email}}</td>
            </tr>
            <tr>
                <td>password</td><td><a class="btn btn-info btn-xs" href="{{URL::to('users/password/'.$user->id)}}">change</a></td>

            </tr>
            <tr>
                <td><a class="btn btn-info btn-xs" href="{{URL::to('users/edit/'.$user->id)}}">update</a></td>

            </tr>

        </table>
		

  </div>
</div>










@stop