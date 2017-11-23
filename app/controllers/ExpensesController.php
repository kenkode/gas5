<?php

class ExpensesController extends \BaseController {

	/**
	 * Display a listing of expenses
	 *
	 * @return Response
	 */
	public function index()
	{
		if (! Entrust::can('view_expense') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{
		$expenses = Expense::all();

		Audit::logaudit('Expenses', 'viewed expenses', 'viewed expenses in the system');

		return View::make('expenses.index', compact('expenses'));
	    }
	}

	/**
	 * Show the form for creating a new expense
	 *
	 * @return Response
	 */
	public function create()
	{
		if (! Entrust::can('create_expense') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{
		$accounts = Account::all();
		return View::make('expenses.create',compact('accounts'));
	}
	}

	/**
	 * Store a newly created expense in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Expense::$rules, Expense::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		if (! Entrust::can('approve_expense') ) // Checks the current user
        {

        $username = Confide::user()->username;

		$users = DB::table('roles')
		->join('assigned_roles', 'roles.id', '=', 'assigned_roles.role_id')
		->join('users', 'assigned_roles.user_id', '=', 'users.id')
		->join('permission_role', 'roles.id', '=', 'permission_role.role_id') 
		->select("users.id","email","username")
		->where("permission_id",141)->get();

        $key = md5(uniqid());

		foreach ($users as $user) {

		Notification::notifyUser($user->id,"Hello, Please check expense inserted for item ".Input::get('name'),"check expense","notificationshowexpense/".Input::get('name')."/".Input::get('type')."/".Input::get('amount')."/".date("Y-m-d",strtotime(Input::get('date')))."/".Input::get('account')."/".Confide::user()->id."/".$user->id."/".$key,$key);
     	}
     	Audit::logaudit('Expenses', 'created an expense', 'created expense '.Input::get('name').' in the system and awaiting approval');
        return Redirect::to('expenses')->with('notice', 'Admin approval is needed to insert this expense');
        }else{

		$expense = new Expense;

		$expense->name = Input::get('name');
		$expense->type = Input::get('type');
		$expense->amount = Input::get('amount');		
		$expense->date = date("Y-m-d",strtotime(Input::get('date')));
		$expense->credit_id = Input::get('credit_account');
		$expense->debit_id = Input::get('debit_account');
		$expense->credit_journal_id = 0;
		$expense->debit_journal_id = 0;
		$expense->receiver_id = Confide::user()->id;
        $expense->confirmed_id = Confide::user()->id;
		$expense->save();

        DB::table('accounts')
            ->join('expenses','accounts.id','=','expenses.credit_id')
            ->where('accounts.id', Input::get('credit_account'))
            ->decrement('accounts.balance', Input::get('amount'));

        $data = array(
			'date' => date("Y-m-d"), 
			'debit_account' => Input::get('debit_account'),
			'credit_account' => Input::get('credit_account'),
			'expense_id' => $expense->id,
			'description' => "Expense",
			'amount' => Input::get('amount'),
			'initiated_by' => Confide::user()->username
		);

		$journal = new Journal;
		$journal->journal_expenseentry($data);

        Audit::logaudit('Expenses', 'created an expense', 'created expense '.Input::get('name').' in the system');

		return Redirect::route('expenses.index')->withFlashMessage('Expense successfully created!');
	}
	}

	/**
	 * Display the specified expense.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if (! Entrust::can('view_expense') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{
		$expense = Expense::findOrFail($id);

		Audit::logaudit('Expenses', 'viewed expense details', 'viewed expense details for expense '.$expense->name.' in the system');

		return View::make('expenses.show', compact('expense'));
	}
	}

	/**
	 * Show the form for editing the specified expense.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

		if (! Entrust::can('update_expense') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{
		$expense = Expense::find($id);
		$accounts = Account::all();

		return View::make('expenses.edit', compact('expense','accounts'));
	}
	}

	/**
	 * Update the specified expense in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$expense = Expense::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Expense::$rules, Expense::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
     
        $expense->name = Input::get('name');
		$expense->type = Input::get('type');
		$expense->amount = Input::get('amount');
		$expense->date = date("Y-m-d",strtotime(Input::get('date')));
		$expense->credit_id = Input::get('credit_account');
		$expense->debit_id = Input::get('debit_account');

		$expense->update();

        $data = array(
			'date' => date("Y-m-d"), 
			'old_debit_account' => $expense->debit_journal_id,
			'old_credit_account' => $expense->credit_journal_id,
			'debit_account' => Input::get('debit_account'),
			'expense_id' => $expense->id,
			'credit_account' => Input::get('credit_account'),
			'description' => "Expense",
			'amount' => Input::get('amount'),
			'initiated_by' => Confide::user()->username
		);

		$journal = new Journal;
		$journal->journal_editexpenseentry($data);

		Audit::logaudit('Expenses', 'updated an expense', 'updated expense '.Input::get('name').' in the system');

		return Redirect::route('expenses.index')->withFlashMessage('Expense successfully updated!');;
	}

	/**
	 * Remove the specified expense from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (! Entrust::can('delete_expense') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{
        $expense = Expense::find($id);

        if($expense->credit_journal_id > 0){
        $credit = Journal::find($expense->credit_journal_id);
		$credit->void = 1;
		$credit->update();

		$debit  = Journal::find($expense->debit_journal_id);
        $debit->void = 1;
		$debit->update();
	    }

		Expense::destroy($id);

		Audit::logaudit('Expenses', 'deleted an expense', 'deleted expense '.$expense->name.' from the system');

		return Redirect::route('expenses.index')->withDeleteMessage('Expense successfully deleted!');
	}
	}

}
