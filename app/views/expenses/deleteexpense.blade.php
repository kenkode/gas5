@extends('layouts.accounting')
@section('content')

<br><div class="row">
    <div class="col-lg-12">
  <h3>Delete Expense</h3>

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

         <form method="POST" action="{{{ URL::to('notificationapprovedeleteexpense') }}}" accept-charset="UTF-8">
   
    <fieldset>
        <div class="form-group">
            <label for="username">Expense Name <span style="color:red">*</span> :</label>
            <input class="form-control" placeholder="" type="text" name="name" id="name" value="{{ $name }}" required>
        </div>

        <div class="form-group">
            <label for="username">Amount <span style="color:red">*</span> :</label>
            <input class="form-control" placeholder="" type="text" name="amount" id="amount" value="{{$amount}}" required>
        </div>

       <div class="form-group">
            <label for="username">Type <span style="color:red">*</span> :</label>
            <input class="form-control" placeholder="" type="text" name="type" id="type" value="{{$type}}" required>
        </div>


        <input class="form-control" placeholder="" type="hidden" name="receiver" id="receiver" value="{{$receiver}}" required>
        <input class="form-control" placeholder="" type="hidden" name="confirmer" id="confirmer" value="{{$confirmer}}" required>
        <input class="form-control" placeholder="" type="hidden" name="key" id="key" value="{{$key}}" required>
        <input class="form-control" placeholder="" type="hidden" name="id" id="id" value="{{$id}}" required>

        <div class="form-actions form-group">
        
          <button type="submit" class="btn btn-primary btn-sm">Delete Expense</button>
        </div>

    </fieldset>
</form>
        

  </div>

</div>

@stop