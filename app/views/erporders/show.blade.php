<?php

function asMoney($value) {
  return number_format($value, 2);
}

?>

@extends('layouts.erp')

{{ HTML::script('media/js/jquery.js') }}

<script type="text/javascript">
$(document).ready(function(){
    $('#select_all').on('click',function(){
        if(this.checked){
            $('.checkbox').each(function(){
                this.checked = true;
            });
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
        }
    });
    
    $('.checkbox').on('click',function(){
        if($('.checkbox:checked').length == $('.checkbox').length){
            $('#select_all').prop('checked',true);
        }else{
            $('#select_all').prop('checked',false);
        }
    });
});
</script>

@section('content')


<!-- MESSAGE -->
<?php 
    $message = Session::get('message');
    Session::forget('message'); 
?>
@if(isset($message))
    <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success!</strong> {{$message}}
    </div>
@endif

@if (Session::get('notice'))
            <div class="alert alert-info">{{ Session::get('notice') }}</div>
        @endif

        @if (Session::get('warning'))
            <div class="alert alert-danger">{{ Session::get('warning') }}</div>
        @endif


<br><div class="row">
    <div class="col-lg-12">
  <h4><font color='green'>Sales Order : {{$order->order_number}} &emsp;| &emsp;&emsp;Client: {{$order->client->name}}  &emsp; |&emsp; Date: {{$order->date}} &emsp; |&emsp; Status: {{$order->status}}<br><br>Credit Period: {{$order->credit_period}} </font> </h4>

<hr>
</div>  
</div>
 
<div class="row">
    <div class="col-lg-12">

        <form target="_blank" class="form-inline" action="{{URL::to('erpReports/receipt/'.$order->id)}}" method="POST">
            
          @if($order->status != "cancelled")
            <div class="form-group">
                <select name="driver" id="driver" class="form-control input-sm" style="width: 250px;" required>
                    <option value="">Please select a driver</option>
                    @if(count($driver) > 0)
                    @foreach($driver as $driver)
                        <option>{{ $driver->surname }} {{ $driver->first_name }}</option>
                    @endforeach
                    @endif
            @endif
                </select> &emsp;
            </div>
            <div class="form-group">

                @if($order->status != "cancelled")
                @if($order->payment_type === "credit")
                    <button type="submit" class="btn btn-primary input-sm"><i class="fa fa-file fa-fw"></i> Delivery Note/Invoice</button>
                @else
                    <button type="submit" class="btn btn-primary input-sm"><i class="fa fa-file fa-fw"></i> Delivery Note/Invoice</button>
                @endif
                @endif
                @if($order->is_pending == null && $order->status != "cancelled")
                 <a href="{{URL::to('approve/cancel/'.$order->id)}}" class="btn btn-danger">Cancel Sale Order </a>
                 @endif
                 @if(Entrust::can('approve_cancel_sale_order') && $order->is_pending != null)
                 <a href="{{URL::to('approve/cancel/'.$order->id)}}" class="btn btn-danger">Approve Cancel Sale Order </a>
                 @endif
          
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>        
            @endforeach
        </div>
        @endif

    <table class="table table-condensed table-bordered table-hover" >

    <thead>
        <!-- <th><input type="checkbox" id="select_all" value=""></th> -->
        <th>Item</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Discount</th>
        <!-- <th>Amount</th>
        <th>Duration</th> -->
        <th>Total Amount</th>
        <th>Total Payable</th>
       
    </thead>

    <tbody>

   
        <?php $total = 0; ?>

        @foreach($order->erporderitems as $orderitem)

            <?php
            $discount_amount = $orderitem['discount_amount'];            
            $total_amount = $orderitem['price'] * $orderitem['quantity'];
            $amount = $orderitem['price'] * $orderitem['quantity']-$discount_amount * $orderitem['quantity'];
            /*$total_amount = $amount * $orderitem['duration'];*/
            $total = $total + $amount;
            ?>
        <tr>
            <!-- <td><input type="checkbox" class="checkbox" name="{{$orderitem->item->id}}" value=""></td> -->
            <td>{{$orderitem->item->item_make}}</td>
            <td>{{$orderitem['quantity']}}</td>
            <td>{{asMoney($orderitem['price'])}}</td>
            <td id="tid">{{asMoney($discount_amount * $orderitem['quantity'])}}</td>
            <!-- <td>{{$amount}}</td>
            <td>{{$orderitem['duration']}}</td> -->
            <td>{{asMoney($amount) }}</td>
            <td>{{asMoney($amount-$orders->discount_amount)}}</td>
            
        </tr>

        @endforeach

        <!-- <tr>
           <td></td>
            <td></td>
            <td></td>
            <td></td> -->
            <!-- <td></td>
            <td><strong>Grand Total</strong></td>
            <td><strong>{{asMoney($total)}}</strong></td>
          
        </tr> --> 
    </tbody>
        
    </table>
        

  </div>

</div>

<br><br><br>

<div id="leaseModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Lease Item</h4>
            </div>
            <div class="modal-body">
                <form class="" role="form" action="{{{ URL::to('stock/lease') }}}" method="POST">
                    <div class="form-group">
                        <font style="color:red">* NB::Items out of stock will not be displayed!</font>
                    </div>
                    <div class="form-group">
                        <label>Client Name <font style="color:red">*</font></label>
                        <input class="form-control" type="hidden" name="client" readonly="" value="{{$client->id}}">
                        <input class="form-control" type="hidden" name="erporderid" readonly="" value="{{$order->id}}">
                        <input class="form-control" type="text" name="name" readonly="" value="{{$client->name}}">
                    </div>
                    
                    <div class="form-group">
                        <label>Item Name <font style="color:red">*</font></label>
                        <select name="item" class="form-control" required="">
                            <option value="">---Please Select an Item---</option>
                            <option value="">==================================</option>
                            @foreach($items as $item)
                                @if(Stock::getStockAmountNew($item)  > 0)
                                    <option value="{{ $item->id }}">{{ $item->item_make }} - ({{ Stock::getStockAmountNew($item) }} items)</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Location <font style="color:red">*</font></label>
                        <select name="location" class="form-control" required="">
                            <option value="">---Please Select a Store---</option>
                            <option value="">==================================</option>
                            @foreach($location as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Item Quantity <font style="color:red">*</font></label>
                        <input required="" class="form-control" type="text" name="lease_qty" placeholder="---Quantity to be leased---">
                    </div>

                    <hr>
                    <div class="form-group text-right">
                        <input class="btn btn-primary btn-sm" type="submit" name="leaseBtn" value="Lease">
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- END LEASE ITEM MODAL -->

<div class="row">
    <div class="col-lg-12">
        @if (Session::has('flash_message'))
        <div class="alert alert-success">
            {{ Session::get('flash_message') }}
        </div>
        @endif

        @if (Session::has('delete_message'))
        <div class="alert alert-danger">
            {{ Session::get('delete_message') }}
        </div>
        @endif

        <div class="panel panel-default">
            @if($order->status != "cancelled")
            @if (Entrust::can('lease_item') ) 
            <div class="panel-heading">
                <a class="btn btn-info btn-sm" href="#leaseModal" data-toggle="modal">Lease Item </a> &emsp;
            </div>
            @else
            <h4>Lease Items</h4>
            @endif
            @endif
            <div class="panel-body">
                <table id="users" class="table table-condensed table-bordered table-responsive table-hover">
                    <thead>
                        <th>#</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php $count=1; ?>
                        @foreach($leased as $leased)
                            <?php
                                $item_name = ItemTracker::getItem($leased->item_id);
                                $client_name = ItemTracker::getClient($leased->client_id);
                                $items_remaining = $leased->items_leased - $leased->items_returned;
                            ?>
                            @if($items_remaining > 0)
                            <tr>
                                <td>{{ $count }}</td>
                                <td>{{ $item_name }}</td>
                                <td>{{ $items_remaining }}</td>
                                <td>{{ $leased->status }}</td>
                                <td>
                                    <a href="#modalReturnItem{{$count}}" role="button" class="btn btn-info btn-sm" data-toggle="modal">Return Item(s)</a>
                                </td>
                            </tr>
 
                            <!-- MODAL RETURN ITEM -->
                            <div id="modalReturnItem{{$count}}" class="modal fade">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Return Item(s)</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form role="form" action="{{{ URL::to('stock/return') }}}" method="POST">
                                                <input type="hidden" name="client_id" value="{{$order->client->id}}">
                                                <input type="hidden" name="erporder_id" value="{{$id}}">
                                                <input type="hidden" name="track_id" value="{{$leased->id}}">
                                                <div class="form-group">
                                                    <label>Client:</label>
                                                    <input class="form-control" type="text" name="client_name" value="{{ $client_name }}" readonly="readonly">
                                                </div>
                                                <div class="form-group">
                                                    <label>Item Name:</label>
                                                    <input class="form-control" type="text" name="item_name" value="{{ $item_name }}" readonly="readonly">
                                                </div>
                                                <div class="form-group">
                                                    <label>Quantity <strong>({{ $items_remaining }} leased)</strong></label>
                                                    <input id="itemQty{{$count}}" class="form-control" type="text" max="{{ $items_remaining }}" name="qty_returned" value="{{ $items_remaining }}">
                                                </div>
                                                <hr>
                                                <div class="form-group text-right">
                                                    <input class="btn btn-primary btn-sm" type="submit" name="returnBtn" value="Return Item(s)">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END RETURN ITEM MODAL -->

                          <?php $count++ ?>
                          @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@stop