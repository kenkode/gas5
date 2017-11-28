<html >

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style type="text/css">

table {
  max-width: 100%;
  background-color: transparent;
}

table, tr, td, th, tbody, thead, tfoot {
    page-break-inside: avoid !important;
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

<?php

function asMoney($value) {
  return number_format($value, 2);
}

?>
</head>

<body>
    <!-- <img src="{{ asset('public/uploads/logo/ADmzyppq2eza.png') }}" class="watermark"> -->

  <div class="header">
       <table >

      <tr>


       
        <td style="width:150px">

            <img src="{{asset('public/uploads/logo/'.$organization->logo)}}" alt="logo" width="50%">
    
        </td>

        <td style="font-size: 20px;">
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


  <div class="content" style='margin-top:40px;'>
   <div align="center" style="font-size: 20px;"><strong>Overdue Report for period {{$from}} to {{$to}}</strong></div>

   <br>

     <?php
      $totaloverdue = $overdue->total_sales - $overdue->total_discount;
      $totalreceivable = $receivable->total_sales - $receivable->total_discount;
      ?>
      
       <h1 style="font-size: 36px" align="center"><strong>Overdue</strong></h1>
       @if($totalreceivable == 0)
       <h1 style="font-size: 36px" align="center">{{number_format(0,2)}}</h1>
       @else
       <h1 style="font-size: 36px" align="center">{{number_format($totaloverdue/$totalreceivable,2)}}</h1>
       @endif
<br><br>

   
</div>


</body>

</html>



