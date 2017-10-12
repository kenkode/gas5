<?php

class PaymentsController extends \BaseController {

	/**
	 * Display a listing of payments
	 *
	 * @return Response
	 */
	public function index()
	{
		
		/*
		$payments = DB::table('payments')
		          ->join('erporders', 'payments.erporder_id', '=', 'erporders.id')
		          ->join('erporderitems', 'payments.erporder_id', '=', 'erporderitems.erporder_id')
		          ->join('clients', 'erporders.client_id', '=', 'clients.id')
		          ->join('items', 'erporderitems.item_id', '=', 'items.id')
		          ->select('clients.name as client','items.name as item','payments.amount_paid as amount','payments.date as date','payments.erporder_id as erporder_id','payments.id as id','erporders.order_number as order_number')
		          ->get();
		          */

		$erporders = Erporder::all();
		
		$erporderitems = Erporderitem::all();		
		$paymentmethods = Paymentmethod::all();
		$payments = Payment::where("is_approved",1)->orderBy("id","DESC")->get();

		Audit::logaudit('Payments', 'viewed payments', 'viewed payments in the system');

		return View::make('payments.index', compact('erporderitems','erporders','paymentmethods','payments'));
	}

	/**
	 * Show the form for creating a new payment
	 *
	 * @return Response
	 */
	public function create()
	{
		$erporders = Erporder::all();
		$accounts = Account::all();
		$erporderitems = Erporderitem::all();
		$paymentmethods = Paymentmethod::all();
		$clients = DB::table('clients')
		         ->join('erporders','clients.id','=','erporders.client_id')
		         ->select( DB::raw('DISTINCT(name),clients.id,clients.type') )
		         ->get();
		
		return View::make('payments.create',compact('erporders','clients','erporderitems','paymentmethods','accounts'));
	}

	public function payable()
	{
		$erporders = Erporder::all();
		$accounts = Account::all();
		$erporderitems = Erporderitem::all();
		$paymentmethods = Paymentmethod::all();
		$clients = DB::table('clients')
		         ->join('erporders','clients.id','=','erporders.client_id')
		         ->select( DB::raw('DISTINCT(name),clients.id,clients.type') )
		         ->get();
		
		return View::make('payments.payable',compact('erporders','clients','erporderitems','paymentmethods','accounts'));
	}

	/**
	 * Store a newly created payment in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Payment::$rules, Payment::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		//$erporder = Erporder::find(Input::get('order'));

		$payment = new Payment;
        if(Input::get('type') === 'Customer'){
		$client = Client::findOrFail(Input::get('order'));
     	}else{
     	$client = Client::findOrFail(Input::get('client'));	
     	}
		if(Input::get('type') === 'Customer'){
		$payment->client_id = Input::get('order');
	    }else{
        $payment->client_id = Input::get('client');
	    }
		if(Input::get('type') === 'Customer'){
		$payment->erporder_id = Input::get('invoice');
	    }else{
        $payment->erporder_id = Input::get('order');
	    }
		$payment->amount_paid = Input::get('amountdue');
		$payment->paymentmethod_id = Input::get('paymentmethod');
		$payment->account_id = Input::get('account');
		$payment->prepared_by = Confide::user()->id;
		$payment->payment_date = date("Y-m-d",strtotime(Input::get('pay_date')));
		$prepared_by = Confide::user()->id;

		$payment->save();

		$id= $payment->id;

        if(Input::get('type') === 'Customer'){
        $erporderitem = Erporderitem::where('erporder_id',Input::get('invoice'))->first();
        $item = Item::find($erporderitem->item_id);
        $payment = Payment::where('erporder_id',Input::get('invoice'))->sum('amount_paid');
        $rem = ($erporderitem->price * $item->item_size) - $payment;

		if($rem == 0){
			$erporder = Erporder::find(Input::get('invoice'));
			$erporder->is_paid = 1;
			$erporder->update();
		}
	    }else{
        $erporderitem = Erporderitem::where('erporder_id',Input::get('order'))->first();
        $item = Item::find($erporderitem->item_id);
        $payment = Payment::where('erporder_id',Input::get('order'))->sum('amount_paid');
        $rem = ($erporderitem->price * $item->item_size) - $payment;

		if($rem == 0){
			$erporder = Erporder::find(Input::get('order'));
			$erporder->is_paid = 1;
			$erporder->update();
		}
	    }

	    $order_number = '';

	    if(Input::get('type') === 'Customer'){
        $erporder = Erporder::find(Input::get('invoice'));
        $order_number = $erporder->order_number;
	    }else{
        $erporder = Erporder::find(Input::get('order'));
        $order_number = $erporder->order_number;
	    }

		
		if(Input::get('type') === 'Customer'){
			Account::where('id', Input::get('paymentmethod'))->increment('balance', Input::get('amountdue'));	
		} else{
			Account::where('id', Input::get('paymentmethod'))->decrement('balance', Input::get('amountdue'));
		}

		if (! Entrust::can('confirm_payments') ) // Checks the current user
        {

        $users = DB::table('roles')
		->join('assigned_roles', 'roles.id', '=', 'assigned_roles.role_id')
		->join('users', 'assigned_roles.user_id', '=', 'users.id')
		->join('permission_role', 'roles.id', '=', 'permission_role.role_id') 
		->select("users.id","email","username")
		->where("permission_id",29)->get();

		$key = md5(uniqid());

		

		foreach ($users as $user) {

        if(Input::get('type') === 'Customer'){
		Notification::notifyUser($user->id,"Hello, Approval to receive payment is required","payment","notificationshowpayment/".$prepared_by."/".$user->id."/".$key."/".$id,$key);
        }else{
        	Notification::notifyUser($user->id,"Hello, Approval for purchase payment is required","payment","notificationshowpayment/".$prepared_by."/".$user->id."/".$key."/".$id,$key);
        }
        }

        Audit::logaudit('Payments', 'created payment', 'created payment for client '.$client->name.', order number '.$order_number.', amount '.Input::get('amountdue').' but awaiting approval in the system');
        return Redirect::to('payments')->with('notice', 'Admin approval is needed for this payment');
        }else{

        $p = Payment::find($id);
        $p->confirmed_id = Confide::user()->id;
        $p->is_approved = 1;
        $p->update();

        Audit::logaudit('Payments', 'created payment', 'created payment for client '.$client->name.', order number '.$order_number.', amount '.Input::get('amountdue').' in the system');

		return Redirect::route('payments.index')->withFlashMessage('Payment successfully created!');
        }
         
       /* if($client->type=='Customer'){
         DB::table('accounts')
            ->join('payments','accounts.id','=','payments.account_id')
            ->join('erporders','payments.client_id','=','erporders.client_id')
            ->where('accounts.id', Input::get('account'))
            ->where('erporders.type','sales')
            ->increment('accounts.balance', Input::get('amount'));*/


           /* $data = array(
			'date' => date("Y-m-d",strtotime(Input::get('paydate'))), 
			'debit_account' => Input::get('account'),
			'credit_account' => Input::get('credit_account'),
			'description' => Input::get('description'),
			'amount' => Input::get('amount'),
			'initiated_by' => Input::get('received_by')
			);
		
		$journal = new Journal;

		$journal->journal_entry($data);
        }else{
        	DB::table('accounts')
            ->join('payments','accounts.id','=','payments.account_id')
            ->join('erporders','payments.client_id','=','erporders.client_id')
            ->where('accounts.id', Input::get('account'))
            ->where('erporders.type','purchases')
            ->decrement('accounts.balance', Input::get('amount'));

            $data = array(
			'date' => date("Y-m-d",strtotime(Input::get('paydate'))), 
			'debit_account' => Input::get('account'),
			'credit_account' => 3,
			'description' => Input::get('description'),
			'amount' => Input::get('amount'),
			'initiated_by' => Input::get('received_by')
			);
		
		$journal = new Journal;

		$journal->journal_entry($data);
        }*/


        
		
	}

	/**
	 * Display the specified payment.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$payment = Payment::findOrFail($id);
		$erporderitem = Erporderitem::findOrFail($id);
		$erporder = Erporder::findOrFail($id);

		return View::make('payments.show', compact('payment','erporderitem','erporder'));
	}

	/**
	 * Show the form for editing the specified payment.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$payment = Payment::find($id);
		$erporder = Erporder::join('erporderitems','erporders.id','=','erporderitems.erporder_id')
                             ->join('items','erporderitems.item_id','=','items.id')
                             ->where('client_id',$payment->client_id)
                             ->where('erporders.status','new')
                             ->select("erporders.id","item_make","order_number")
                             ->first();
		//$erporders = Erporder::all();
		$erporderitems = Erporderitem::all();

		return View::make('payments.edit', compact('payment','erporder','erporderitems'));
	}

	/**
	 * Update the specified payment in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$payment = Payment::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Payment::$rules, Payment::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		//$payment->erporder_id = Input::get('order');
		$payment->amount_paid = Input::get('amount');
		//$payment->balance = Input::get('balance');
		//$payment->paymentmethod_id = Input::get('paymentmethod');
		//$payment->received_by = Input::get('received_by');
		//$payment->payment_date = date("Y-m-d",strtotime(Input::get('pay_date')));
		$payment->update();

		$erporder = Erporder::find(Input::get('order'));
		$client = Client::find($erporder->client_id);

		Audit::logaudit('Payments', 'updated payment', 'updated payment for client '.$client->name.', order number '.$$erporder->order_number.', amount '.Input::get('amount').' in the system');

		return Redirect::route('payments.index')->withFlashMessage('Payment successfully updated!');
	}

	/**
	 * Remove the specified payment from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$payment = Payment::findOrFail($id);
		$erporder = Erporder::find($payment->erporder_id);
		$client = Client::find($erporder->client_id);
		Payment::destroy($id);

		Audit::logaudit('Payments', 'deleted payment', 'deleted payment for client '.$client->name.', order number '.$$erporder->order_number.', amount '.$payment->amount_paid.' in the system');

		return Redirect::route('payments.index')->withDeleteMessage('Payment successfully deleted!');
	}


	/**
	 * Daily Payments Received in form of cash, mpesa or cheque
	 */
	public function dailyPayments(){
		$payments = DB::table('payments')
							->join('clients', 'payments.client_id', '=', 'clients.id')
							->join('paymentmethods', 'payments.paymentmethod_id', '=', 'paymentmethods.id')
							->where('clients.type', 'Customer')
							->where('payments.payment_date', date('Y-m-d'))
							->selectRaw('clients.name as client_name, amount_paid, paymentmethods.name as payment_method')
							->get();

							//return $payments;

	    Audit::logaudit('Payments', 'viewed daily payments', 'viewed daily payments in the system');

		return View::make('payments.dailyPayments', compact('payments'));
	}

}
