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

		Notification::notifyUser($user->id,"Hello, Please approve expense inserted for item ".Input::get('name'),"expense","notificationshowexpense/".Input::get('name')."/".Input::get('type')."/".Input::get('amount')."/".date("Y-m-d",strtotime(Input::get('date')))."/".Input::get('account')."/".Confide::user()->id."/".$user->id."/".$key,$key);
     	}
        return Redirect::to('expenses')->with('notice', 'Admin approval is needed to insert this expense');
        }else{

		$expense = new Expense;

		$expense->name = Input::get('name');
		$expense->type = Input::get('type');
		$expense->amount = Input::get('amount');		
		$expense->date = date("Y-m-d",strtotime(Input::get('date')));
		$expense->account_id = Input::get('account');
		$expense->receiver_id = Confide::user()->id;
        $expense->confirmed_id = Confide::user()->id;
		$expense->save();

        DB::table('accounts')
            ->join('expenses','accounts.id','=','expenses.account_id')
            ->where('accounts.id', Input::get('account'))
            ->decrement('accounts.balance', Input::get('amount'));

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
		$expense->account_id = Input::get('account');

		$expense->update();

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
		Expense::destroy($id);

		return Redirect::route('expenses.index')->withDeleteMessage('Expense successfully deleted!');
	}
	}

}
