<?php


function asMoney($value) {
  return number_format($value, 2);
}

?>

<html >

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style type="text/css">

table {
  max-width: 100%;
  background-color: transparent;
}


th,td{
  padding: 2px 7px !important;
}

th {
  text-align: left;
}
.table {
  width: 100%;
   margin-bottom: 150px;
}
hr {
  margin-top: 1px;
  margin-bottom: 2px;
  border: 0;
  border-top: 2px dotted #eee;
}

body {
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 12px;
  line-height: 1.428571429;
  color: #333;
  background-color: #fff;


 @page { margin: 50px 30px; }
 .header { position: top; left: 0px; top: -150px; right: 0px; height: 100px;  text-align: center; }
 .content {margin-top: -100px; margin-bottom: -150px}
 .footer { position: fixed; left: 0px; bottom: -60px; right: 0px; height: 50px;  }
 .footer .page:after { content: counter(page, upper-roman); }


img#watermark{
  position: fixed;
  width: 100%;
  z-index: 10;
  opacity: 0.1;
}

</style>


</head>

<body>
    <!-- <img src="{{ asset('public/uploads/logo/ADmzyppq2eza.png') }}" class="watermark"> -->

  <div class="header">
       <table >

      <tr>


       
        <td style="width:150px">

            <img src="{{asset('public/uploads/logo/'.$organization->logo)}}" alt="logo" width="75%">
    
        </td>

        <td>
        <strong>
          {{ strtoupper($organization->name)}}
          </strong><br>
          {{ $organization->phone}}<br> 
          {{ $organization->email}}<br>
          {{ $organization->website}}<br>
          {{ $organization->address}}
       

        </td>
        

      </tr>


      <tr>

        <hr>
      </tr>



    </table>
   </div>



<div class="footer">
     <p class="page">Page <?php $PAGE_NUM ?></p>
   </div>


	<div class="content" style='margin-top:0px;'>   

   <div align="center"><strong>Stock Movement Schedule as at {{$from}} to {{$to}}</strong></div><br>

    <table class="table table-bordered" border='1' cellspacing='0' cellpadding='0'>

      <tr>
        


        <th width='20'><strong># </strong></th>
        
        <th><strong>Item Name </strong></th>
        <th><strong>Opening Stock </strong></th>
        <!-- <th><strong>Cost price </strong></th>
        <th><strong>Selling Price</strong></th> -->
        <!-- <td><strong>Opening Stock </strong></td> -->
        <td><strong>Stock In </strong></td>
        <!-- <td><strong>Total</strong></td> -->
        <td><strong>Stock Out</strong></td>
        <!-- <td><strong>Amount Sold</strong></td> -->                
        <!-- <th><strong>Store Keeping Unit </strong></th> -->
        <th align="right"><strong>Closing Stock</strong></th>
        <!-- <th align="right"><strong>Stock Value </strong></th> -->
      </tr>
      <?php $i =1; 
       $openstock = 0;
       $stockin = 0;
       $stockout = 0;
       $closingout = 0;
       $profit_margin = 0;
       $totalSales = 0;
       $totalCostprice = 0;
      ?>
      @foreach($items as $item)

      <?php

      $openstock = $openstock + Stock::dailyOpeningStock($item->id,$from,$to);
      $stockin = $stockin + Stock::dailyStockIn($item->id,$from,$to);
      $stockout = $stockout + Stock::dailyStockOut($item->id,$from,$to);
      $closingout = $closingout + Stock::dailyClosingStock($item->id,$from,$to);

      /*$totalSales = $totalSales + (Stock::totalSales($item->itm_id)) * Stock::getItem($item->itm_id)->selling_price;
      $totalCostprice = $totalCostprice + (Stock::totalSales($item->itm_id)) * Stock::getItem($item->itm_id)->purchase_price;
      $profit_margin = $totalSales -$totalCostprice;*/
      ?>
      <tr>

        <td td width='20' valign="top">{{$i}}</td>        
        <td valign="top"> {{ $item->item_make }}</td>
        <td valign="top"> {{ Stock::dailyOpeningStock($item->id,$from,$to) }}</td>
        <td valign="top" align="right"> {{ Stock::dailyStockIn($item->id,$from,$to) }}</td>
        <td valign="top" align="right"> {{ Stock::dailyStockOut($item->id,$from,$to) }}</td>
        <td valign="top" align="center"> {{ Stock::dailyClosingStock($item->id,$from,$to) }}</td>
       

        </tr>
      <?php $i++; ?>
   
    @endforeach

     <tr>
        <td></td>        
        <td valign="top"><strong>Totals</strong></td>
        <td valign="top"> <strong>{{$openstock}}</strong></td>
        <td valign="top" align="right"> <strong>{{$stockin}}</strong></td>
        <td valign="top" align="right"> <strong>{{$stockout}}</strong></td>
        <td valign="top" align="center"> <strong>{{$closingout}}</strong></td>
     </tr>

    </table>

<!-- <br><br>
<table  border='0' align="center">
<tr><th colspan="2">SUMMARY</th></tr>
<tr><td><b>Total Sales:</b></td><td><b>{{asMoney($totalSales)}}</b></td></tr>
<tr><td><b>Total Cost Price:</b><td><b>{{asMoney($totalCostprice)}}</b></td></td></tr>
<hr>
<tr><td><b>Profit Margin:</b></td> <td><b>{{asMoney($profit_margin)}}</b></td></tr> -->

</table>

   
</div>


</body>

</html>



