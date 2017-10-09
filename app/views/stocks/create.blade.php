
{{HTML::script('media/jquery-1.8.0.min.js') }}
@extends('layouts.erp')

<script type="text/javascript">
$(document).ready(function() {
  
    $('#item').change(function(){
      
        $.get("{{ url('api/getpurchased')}}", 
        { option: $(this).val() }, 
        function(data) {
            /*console.log('hi');*/
                $('#sup').val(data);
            });
        });
   });
</script>

<script type="text/javascript">
$(document).ready(function() {
  
    $('#client').change(function(){
        //alert($(this).val());


    $.ajax({
    type: "GET",
    url: "{{ url('api/getpurchaseorders')}}",
    data:{ option: $(this).val() },
    dataType: 'json',
    cache: false,
    success: function(data)
    {
        //alert(data.hasstock[0].erporder );
        console.log(data);
            $('#item').empty(); 
            $('#item').append("<option value=''>---Please Select Stock---</option>");
            $('#item').append("<option value=''>==================================</option>");
            var i=0;
            for (var j = 0; j < data.hasstock.length; j++) {
            $('#item').append("<option value='" + data.hasstock[j].id +"'>" + data.hasstock[j].erporder + "</option>");
            };
            
            for (var i = 0; i < data.nostock.length; i++) {
            $('#item').append("<option value='" + data.nostock[i].id +"'>" + data.nostock[i].erporder + "</option>");
            };
    },
    error: function(xhr, status, error) {
    alert(xhr.responseText);
   }
   });
      
        /*$.get("{{ url('api/getpurchaseorders')}}", 
        { option: $(this).val() }, 
        function(data) {
            console.log(data);
            $('#item').empty(); 
            $('#item').append("<option value=''>---Please Select Stock---</option>");
            $('#item').append("<option value=''>==================================</option>");
            $.each(data, function(key, element) {
            $('#item').append("<option value='" + key +"'>" + element + "</option>");
            });
        });*/
        });
   });
</script>

 {{ HTML::style('jquery-ui-1.11.4.custom/jquery-ui.css') }}
 {{ HTML::script('jquery-ui-1.11.4.custom/jquery-ui.js') }}

<script type="text/javascript">
$(document).ready(function(){
$('#').hide();

$('#item').change(function(){
if($(this).val()){
    $('#').show();
}else if($(this).val() == "EXPENSE"){
    $('#expensecategory').show();
    $('#assetcategory').hide();
    $('#liabilitycategory').hide();
    $('#incomecategory').hide();
    $('#assetcat').val('');
    $('#liabilitycat').val('');
    $('#incomecat').val('');
}else if($(this).val() == "LIABILITY"){
    $('#liabilitycategory').show();
    $('#assetcategory').hide();
    $('#expensecategory').hide();
    $('#incomecategory').hide();
    $('#assetcat').val('');
    $('#expensecat').val('');
    $('#incomecat').val('');
}else if($(this).val() == "INCOME"){
    $('#incomecategory').show();
    $('#assetcategory').hide();
    $('#expensecategory').hide();
    $('#liabilitycategory').hide();
    $('#assetcat').val('');
    $('#expensecat').val('');
    $('#liabilitycat').val('');
}else{
    $('#assetcategory').hide();
    $('#expensecategory').hide();
    $('#liabilitycategory').hide(); 
    $('#incomecategory').hide(); 
    $('#assetcat').val('');
    $('#expensecat').val('');
    $('#liabilitycat').val('');
    $('#incomecat').val('');
}

});
});
</script>




@section('content')

<br><div class="row">
    <div class="col-lg-12">
  <h3>Receive Stock</h3>

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

         <form method="POST" action="{{{ URL::to('stocks') }}}" accept-charset="UTF-8">
        <font color="red"><i>All fields marked with * are mandatory</i></font>

         <div class="form-group">
                        <label for="username">Date<span style="color:red">*</span> :</label>
                        <div class="right-inner-addon ">
                        <i class="glyphicon glyphicon-calendar"></i>
                        <input class="form-control datepicker"  readonly="readonly" placeholder="" type="text" name="date" id="date" value="{{date('Y-m-d')}}" required>
                        </div>
          </div>

   
    <fieldset>
        <div class="form-group">
            <label for="username">Supplier <span style="color:red">*</span> :</label>
            <select required name="client" class="form-control" id="client" required>
            <option> select Supplier ... </option>
                @foreach($clients as $client)
                @if($client->type == 'Supplier')
                <option value="{{$client->id}}">{{$client->name}}</option>
                @endif
                @endforeach 
            </select>
        </div>


     <div class="form-group">
                        <label>Select Purchase Order <font style="color:red">*</font></label>
                        <select required name="item" id="item" class="form-control">
                            <option value="">---Please Select Stock---</option>
                            <option value="">==================================</option>
                            <!-- @foreach($items as $item)
                                @if(Stock::getStockAmount($item)  > 0)
                                    <option value="{{ $item->id }}">{{ $item->item_make }} - ({{ Stock::getStockAmount($item) }} items)</option>
                                @endif
                            @endforeach -->
                        </select>
                    </div>


         <div class="form-group">
                        <label>Receive Quantity <font style="color:red">*</font></label>
                        <input required class="form-control" type="text" name="lease_qty" placeholder="---Quantity to be Received---">
                    </div>

        <!--<div class="form-group" id="sup">
            <label for="username">Select Stock to recieve</label><span style="color:red">*</span> :
           <select name="order" id="sup" class="form-control" required>
                           <option></option>
                           <option>..................................Select Stock....................................</option>
                          
                            <option value=""></option>
                          
                        </select>
                </div>-->

        <div class="form-group">
            <label for="username">Store <span style="color:red">*</span> :</label>
            <select name="location" class="form-control" required>
            <option> select store ... </option>
                @foreach($locations as $location)
                <option value="{{$location->id}}">{{$location->name}}</option>
                @endforeach
                
            </select>
        </div>

        

        

        <div class="form-actions form-group">
        
          <button type="submit" class="btn btn-primary btn-sm">Receive/Generate Code</button>
        </div>

    </fieldset>
</form>
        

  </div>

</div>

@stop