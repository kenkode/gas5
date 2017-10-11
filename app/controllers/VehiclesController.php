<?php

class VehiclesController extends \BaseController {

	/**
	 * Display a listing of cars
	 *
	 * @return Response
	 */
	public function index()
	{
		$vehicles = Vehicle::all();

		if (! Entrust::can('view_vehicle') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{

        Audit::logaudit('Vehicles', 'viewed vehicles', 'viewed vehicles in the system');

		return View::make('vehicles.index', compact('vehicles'));
	}
	}

	/**
	 * Show the form for creating a new car
	 *
	 * @return Response
	 */
	public function create()
	{
		if (! Entrust::can('create_vehicle') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{
		return View::make('vehicles.create');
	}
	}

	/**
	 * Store a newly created car in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Vehicle::$rules,Vehicle::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$vehicle = new Vehicle;

		
		$vehicle->date = date('Y-m-d');
		$vehicle->reg_no = Input::get('reg_no');
		$vehicle->model = Input::get('model');
		$vehicle->tank_capacity = Input::get('tank_capacity');			
		$vehicle->save();

		Audit::logaudit('Vehicles', 'created a vehicle', 'created vehicle registration number '.Input::get('reg_no').' model '.Input::get('model').' in the system');

		return Redirect::route('vehicles.index');
	}

	/**
	 * Display the specified car.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$vehicle = Vehicle::findOrFail($id);

        if (! Entrust::can('view_vehicle') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{
        Audit::logaudit('Vehicles', 'viewed vehicle details', 'created vehicle details for registration number '.$vehicle->reg_no.' model '.$vehicle->model.' in the system');
		return View::make('vehicles.show', compact('vehicle'));
	}
	}

	/**
	 * Show the form for editing the specified car.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$vehicle = Vehicle::find($id);

        if (! Entrust::can('update_vehicle') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{
		return View::make('vehicles.edit', compact('vehicle'));
	}
	}

	/**
	 * Update the specified car in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$vehicle = Vehicle::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Vehicle::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$vehicle->date = date('Y-m-d');
		$vehicle->reg_no = Input::get('reg_no');
		$vehicle->model = Input::get('model');
		$vehicle->tank_capacity = Input::get('tank_capacity');	
		$vehicle->update();

		Audit::logaudit('Vehicles', 'updated a vehicle', 'updated vehicle registration number '.Input::get('reg_no').' model '.Input::get('model').' in the system');

		return Redirect::route('vehicles.index');
	}

	/**
	 * Remove the specified car from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		

		if (! Entrust::can('delete_vehicle') ) // Checks the current user
        {
        return Redirect::to('dashboard')->with('notice', 'you do not have access to this resource. Contact your system admin');
        }else{

        $vehicle = Vehicle::find($id);
        Vehicle::destroy($id);
        Audit::logaudit('Vehicles', 'deleted a vehicle', 'deleted vehicle registration number '.$vehicle->reg_no.' model '.$vehicle->model.' from the system');

		return Redirect::route('vehicles.index');
	}
	}

}
