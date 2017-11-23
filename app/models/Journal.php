<?php

class Journal extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];



	public function branch(){

		return $this->belongsTo('Branch');
	}


	public function account(){

		return $this->belongsTo('Account');
	}




	/**
	* function fo journal entries
	*/

	public  function journal_entry($data){


		$trans_no = $this->getTransactionNumber();


		// function for crediting

		$this->creditAccount($data, $trans_no);

		// function for crediting

		$this->debitAccount($data, $trans_no);

		
	}

	public  function journal_editentry($data){


		$trans_no = $this->getTransactionNumber();


		// function for crediting

		$this->creditEditAccount($data, $trans_no);

		// function for crediting

		$this->debitEditAccount($data, $trans_no);

		
	}

    public  function journal_paymententry($data){


		$trans_no = $this->getTransactionNumber();


		// function for crediting

		$this->creditPaymentAccount($data, $trans_no);

		// function for crediting

		$this->debitPaymentAccount($data, $trans_no);

		
	}

	public  function journal_expenseentry($data){


		$trans_no = $this->getTransactionNumber();


		// function for crediting

		$this->creditExpenseAccount($data, $trans_no);

		// function for crediting

		$this->debitExpenseAccount($data, $trans_no);

		
	}

	public  function journal_editexpenseentry($data){


		$trans_no = $this->getTransactionNumber();


		// function for crediting

		$this->creditEditExpenseAccount($data, $trans_no);

		// function for crediting

		$this->debitEditExpenseAccount($data, $trans_no);

		
	}

	public function getTransactionNumber(){

		$date = date('Y-m-d H:m:s');

		$trans_no  = strtotime($date);

		return $trans_no;
	}


	public function creditAccount($data, $trans_no){

		$journal = new Journal;


		$account = Account::findOrFail($data['credit_account']);


	
		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'credit';
		$journal->description = $data['description'];
		$journal->save();
	}



	public function debitAccount($data, $trans_no){

		$journal = new Journal;


		$account = Account::findOrFail($data['debit_account']);


	
		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'debit';
		$journal->description = $data['description'];
		$journal->save();
	}

    public function creditEditAccount($data, $trans_no){

    	if($data['old_credit_account'] > 0){
    	   $j = Journal::find($data['old_credit_account']);
    	   $j->void = 1;
    	   $j->update();
    	}

		$journal = new Journal;

		$account = Account::findOrFail($data['credit_account']);

		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'credit';
		$journal->description = $data['description'];
		$journal->save();

		$payment = Payment::find($data['payment_id']);
		$payment->credit_journal_id = $journal->id;
		$payment->update();
	}



	public function debitEditAccount($data, $trans_no){

		if($data['old_debit_account'] > 0){
    	   $j = Journal::find($data['old_debit_account']);
    	   $j->void = 1;
    	   $j->update();
    	}

		$journal = new Journal;


		$account = Account::findOrFail($data['debit_account']);

		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'debit';
		$journal->description = $data['description'];
		$journal->save();

		$payment = Payment::find($data['payment_id']);
		$payment->debit_journal_id = $journal->id;
		$payment->update();
	}

	public function creditEditExpenseAccount($data, $trans_no){

    	if($data['old_credit_account'] > 0){
    	   $j = Journal::find($data['old_credit_account']);
    	   $j->void = 1;
    	   $j->update();
    	}

		$journal = new Journal;

		$account = Account::findOrFail($data['credit_account']);

		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'credit';
		$journal->description = $data['description'];
		$journal->save();

		$expense = Expense::find($data['expense_id']);
		$expense->credit_journal_id = $journal->id;
		$expense->update();
	}



	public function debitEditExpenseAccount($data, $trans_no){

		if($data['old_debit_account'] > 0){
    	   $j = Journal::find($data['old_debit_account']);
    	   $j->void = 1;
    	   $j->update();
    	}

		$journal = new Journal;


		$account = Account::findOrFail($data['debit_account']);

		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'debit';
		$journal->description = $data['description'];
		$journal->save();

		$expense = Expense::find($data['expense_id']);
		$expense->debit_journal_id = $journal->id;
		$expense->update();
	}

    public function creditPaymentAccount($data, $trans_no){

		$journal = new Journal;


		$account = Account::findOrFail($data['credit_account']);


	
		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'credit';
		$journal->description = $data['description'];
		$journal->save();

		$payment = Payment::find($data['payment_id']);
		$payment->credit_journal_id = $journal->id;
		$payment->update();
	}



	public function debitPaymentAccount($data, $trans_no){

		$journal = new Journal;


		$account = Account::findOrFail($data['debit_account']);


	
		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'debit';
		$journal->description = $data['description'];
		$journal->save();

		$payment = Payment::find($data['payment_id']);
		$payment->debit_journal_id = $journal->id;
		$payment->update();
	}

	public function creditExpenseAccount($data, $trans_no){

		$journal = new Journal;


		$account = Account::findOrFail($data['credit_account']);


	
		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'credit';
		$journal->description = $data['description'];
		$journal->save();

		$expense = Expense::find($data['expense_id']);
		$expense->credit_journal_id = $journal->id;
		$expense->update();
	}



	public function debitExpenseAccount($data, $trans_no){

		$journal = new Journal;


		$account = Account::findOrFail($data['debit_account']);


	
		$journal->account()->associate($account);

		$journal->date = $data['date'];
		$journal->trans_no = $trans_no;
		$journal->initiated_by = $data['initiated_by'];
		$journal->amount = $data['amount'];
		$journal->type = 'debit';
		$journal->description = $data['description'];
		$journal->save();

		$expense = Expense::find($data['expense_id']);
		$expense->debit_journal_id = $journal->id;
		$expense->update();
	}

}