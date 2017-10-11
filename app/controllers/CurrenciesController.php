<?php

class CurrenciesController extends \BaseController {

	/**
	 * Display a listing of currencies
	 *
	 * @return Response
	 */
	public function index()
	{
		$currencies = Currency::all();

		Audit::logaudit('Currency', 'viewed currencies', 'viewed currencies in the system');

		return View::make('currencies.index', compact('currencies'));
	}

	/**
	 * Show the form for creating a new currency
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('currencies.create');
	}

	/**
	 * Store a newly created currency in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Currency::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$currency = new Currency;

		$currency->name = Input::get('name');
		$currency->shortname = Input::get('shortname');
		$currency->save();

		Audit::logaudit('Currency', 'created a currency', 'created currency '.$currency->name.' in the system');


		return Redirect::route('currencies.index');
	}

	/**
	 * Display the specified currency.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$currency = Currency::findOrFail($id);

		Audit::logaudit('Currency', 'viewed a currency details', 'viewed currency '.$currency->name.' in the system');

		return View::make('currencies.show', compact('currency'));

	}

	/**
	 * Show the form for editing the specified currency.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$currency = Currency::find($id);

		return View::make('currencies.edit', compact('currency'));
	}

	/**
	 * Update the specified currency in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$currency = Currency::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Currency::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$currency->name = Input::get('name');
		$currency->shortname = Input::get('shortname');
		$currency->update();

		Audit::logaudit('Currency', 'updated a currency', 'updated currency '.$currency->name.' in the system');

		return Redirect::route('currencies.index');
	}

	/**
	 * Remove the specified currency from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$currency = Currency::findOrFail($id);
		Currency::destroy($id);

		Audit::logaudit('Currency', 'deleted a currency', 'deleted currency '.$currency->name.' from the system');

		return Redirect::route('currencies.index');
	}

}
