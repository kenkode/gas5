@extends('layouts.erp')
@section('content')

<div class="row">
    
</div>

<div class="row">
  <div class="col-lg-12">

    <hr>
    
    @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>        
            @endforeach
        </div>
        @endif
        
        <div class="col-lg-5">
            
            <h2>Edit Credit Period:</h2>
            <form action="{{{ URL::to('erporder/updatecreditperiod/'.$order->id) }}}" method="POST" accept-charset="utf-8">
                
                <div class="form-group">
                        <label for="username">Credit Period</label>
                        <div class="right-inner-addon ">
                        <i class="glyphicon glyphicon-calendar"></i>
                        <input class="form-control datepicker22"  readonly="readonly" placeholder="" type="text" name="credit_period" id="credit_period" value="{{$order->credit_period}}">
                        </div>
               </div>
                <div class="form-actions form-group">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
            
        </div>
        

  </div>

</div>

@stop