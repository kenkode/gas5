{{HTML::script('media/jquery-1.8.0.min.js') }}
@extends('layouts.erp')
<script type="text/javascript">
$(document).ready(function() {
  
    $('#item').change(function(){
      
        $.get("{{ url('api/getsellingprice')}}", 
        { option: $(this).val() }, 
        function(data) {
            /*console.log('hi');*/
                $('#selling_price').val(data);
            });
        });
   });
</script>
@section('content')

<br><div class="row">
	<div class="col-lg-12">
  <h4>Approve Client Discount Update</h4>

<hr>
</div>	
</div>


<div class="row">
	<div class="col-lg-5">

    
		
		 @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>        
            @endforeach
        </div>
        @endif

		 <form method="POST" action="{{{ URL::to('notificationapprovepriceupdate') }}}" accept-charset="UTF-8">

         <input type="hidden" name="id" value="{{$id}}">
         <input type="hidden" name="key" value="{{$key}}">
         <input type="hidden" name="confirmer" value="{{$confirmer}}">
         <input type="hidden" name="receiver" value="{{$receiver}}">
         <input type="hidden" name="client" value="{{$client}}">
         <input type="hidden" name="item" value="{{$item}}">
   
    <fieldset>
        <div class="form-group">
            <label for="username">Client:</label>
            <input class="form-control" placeholder="" type="text" name="clientname" id="selling_price" value="{{$clientname}}" readonly>
        </div>

         <div class="form-group">
            <label for="username">Item</label>
            <input class="form-control" placeholder="" type="text" name="itemmake" id="item" value="{{$itemmake}}" readonly>
        </div>

        
        <div class="form-group">
            <label for="username">Selling Price:</label>
            <input class="form-control" placeholder="" type="text" name="selling_price" id="selling_price" value="{{Price::sprice($item)}}" readonly>
        </div>

        <div class="form-group">
            <label for="username">Discount:</label>
            <input class="form-control" placeholder="" readonly type="text" name="discount" id="discount" value="{{$discount}}">
        </div>

        <!-- <div class="form-group">
            <label for="username">Customer Price:</label>
            <input class="form-control" placeholder="" type="text" name="customer_price" id="customer_price" value="">
        </div> -->
        
        <div class="form-actions form-group">
        
          <button type="submit" class="btn btn-success btn-sm">Approve Update</button>
        </div>

    </fieldset>
</form>
		

  </div>

</div>

@stop
