@extends('layouts.erp')

{{HTML::script('media/jquery-1.8.0.min.js') }}

@section('content')

<br><div class="row">
	<div class="col-lg-12">
  <h3>New Sales Order</h3>

<hr>
</div>	
</div>

<script type="text/javascript">
// Get the <datalist> and <input> elements.
var dataList = document.getElementById('json-datalist');
var input = document.getElementById('ajax');

// Create a new XMLHttpRequest.
var request = new XMLHttpRequest();

// Handle state changes for the request.
request.onreadystatechange = function(response) {
  if (request.readyState === 4) {
    if (request.status === 200) {
      // Parse the JSON
      var jsonOptions = JSON.parse(request.responseText);
  
      // Loop over the JSON array.
      jsonOptions.forEach(function(item) {
        // Create a new <option> element.
        var option = document.createElement('option');
        // Set the value using the item in the JSON array.
        option.value = item;
        // Add the <option> element to the <datalist>.
        dataList.appendChild(option);
      });
      
      // Update the placeholder text.
      input.placeholder = "e.g. datalist";
    } else {
      // An error occured :(
      input.placeholder = "Couldn't load datalist options :(";
    }
  }
};

// Update the placeholder text.
input.placeholder = "Loading options...";

// Set up and make the request.
request.open('GET', 'https://s3-us-west-2.amazonaws.com/s.cdpn.io/4621/html-elements.json', true);
request.send();

</script>

<div class="row">
	<div class="col-lg-5">

    
		
		 @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>        
            @endforeach
        </div>
        @endif

		 <form method="POST" action="{{{ URL::to('erporders/create') }}}" accept-charset="UTF-8">
   
    <fieldset>
        <font color="red"><i>All fields marked with * are mandatory</i></font>
         <div class="form-group">
            <label for="username">Order Number:</label>
            <input type="text" name="order_number" value="{{$order_number}}" class="form-control" readonly>
        </div>

        <div class="form-group">
                        <label for="username">Date</label>
                        <div class="right-inner-addon ">
                        <i class="glyphicon glyphicon-calendar"></i>
                        <input class="form-control datepicker"  readonly="readonly" placeholder="" type="text" name="date" id="date" value="{{{Input::old('date')}}}">
                        </div>
          </div>


          <div class="form-group" id="default">
            <label for="username">Client <span style="color:red">*</span> :</label>
            <select name="client" class="form-control" id="languages" list="clients" required>
                @foreach($clients as $client)
                @if($client->type == 'Customer')
                    <option value="{{$client->id}}">{{$client->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>


        <div class="form-group">
            <label for="username">Sale Type <span style="color:red">*</span> :</label>
            <select name="payment_type" class="form-control" required>
                
                    <option value="cash">Cash</option>
                    <option value="credit">Credit</option>
                    
            </select>
        </div>

        <div class="form-group">
            <label for="username">Credit Period:</label>
            <input type="text" name="period" id="period" value="{{{Input::old('period')}}}" class="form-control">
        </div>

        <div class="form-group">
                        <label for="username">End Credit Period</label>
                        <div class="right-inner-addon ">
                        <i class="glyphicon glyphicon-calendar"></i>
                        <input class="form-control"  readonly="readonly" placeholder="" type="text" name="credit_period" id="credit_period" >
                        </div>
          </div>

        <div class="form-actions form-group">
        
          <button type="submit" class="btn btn-primary btn-sm">Create</button>
        </div>

    </fieldset>
</form>
		

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