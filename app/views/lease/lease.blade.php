@extends('layouts.erp')
@section('content')

<br><div class="row">
	<div class="col-lg-12">
  <h3>Lease Item</h3>

<hr>
</div>	
</div>
<font color="red"><i>* NB::Items out of stock will not be displayed!</i></font>

<div class="row">
	<div class="col-lg-5">

    
		
		 @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>        
            @endforeach
        </div>
        @endif

		 <form method="POST" action="{{{ URL::to('stock/lease') }}}" accept-charset="UTF-8">
   
    <fieldset>
        <div class="form-group">
                        <label>Client Name <font style="color:red">*</font></label>
                        <select name="client" class="form-control">
                            <option value="">---Select Client---</option>
                            <option value="">==================================</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

        <div class="form-group">
                        <label>Item Name <font style="color:red">*</font></label>
                        <select name="item" class="form-control">
                            <option value="">---Please Select an Item---</option>
                            <option value="">==================================</option>
                            @foreach($items as $item)
                                @if(Stock::getStockAmount($item)  > 0)
                                    <option value="{{ $item->id }}">{{ $item->item_make }} - ({{ Stock::getStockAmount($item) }} items)</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

          <div class="form-group">
                        <label>Location <font style="color:red">*</font></label>
                        <select name="location" class="form-control">
                            <option value="">---Please Select a Store---</option>
                            <option value="">==================================</option>
                            @foreach($location as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Item Quantity <font style="color:red">*</font></label>
                        <input class="form-control" type="text" name="lease_qty" placeholder="---Quantity to be leased---">
                    </div>

                    <hr>
                   <div class="form-actions form-group">
        
          <button type="submit" class="btn btn-primary btn-sm">Lease Item</button>
        </div>

    

    </fieldset>
</form>
		

  </div>

</div>

@stop