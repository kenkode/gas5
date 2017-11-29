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
                        <label for="username">Date</label>
                        <div class="right-inner-addon ">
                        <i class="glyphicon glyphicon-calendar"></i>
                        <input class="form-control datepicker"  readonly="readonly" placeholder="" type="text" name="date" id="date" value="{{$order->date}}">
                        </div>
               </div>

               <?php
                $datetime1 = strtotime($order->date);
                $datetime2 = strtotime($order->credit_period);

                $secs = $datetime2 - $datetime1;// == <seconds between the two times>
                $days = $secs / 86400;
               ?>

               <div class="form-group">
               <label for="username">Credit Period:</label>
               <input type="text" name="period" id="period" value="{{$days}}" class="form-control">
               </div>

               <div class="form-group">
                        <label for="username">Credit Period</label>
                        <div class="right-inner-addon ">
                        <i class="glyphicon glyphicon-calendar"></i>
                        <input class="form-control"  readonly="readonly" placeholder="" type="text" name="credit_period" id="credit_period" value="{{$order->credit_period}}">
                        </div>
               </div>
                
                <div class="form-actions form-group">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
            
        </div>
        

  </div>

</div>

<script type="text/javascript">


$(document).ready(function(){

    $('#period').keyup(function(){
    //alert($('#weekends').val());
    
       var date = new Date($("#date").val()),
           days = parseInt($("#period").val(), 10);

        date.setDate(date.getDate());

        if(!isNaN(date.getTime())){
            date.setDate(date.getDate() + days);

            $("#credit_period").val(formatDate(date));
        } 
         
      });

    $('#date').change(function(){
    //alert($('#weekends').val());
    
       var date = new Date($("#date").val()),
           days = parseInt($("#period").val(), 10);

        date.setDate(date.getDate());

        if(!isNaN(date.getTime())){
            date.setDate(date.getDate() + days);

            $("#credit_period").val(formatDate(date));
        } 
         
      });

    function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}
      
       

    });

    </script>

@stop