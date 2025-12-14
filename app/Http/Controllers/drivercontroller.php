<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DriverRepository;

class DriverController extends Controller
{
    public function __construct(private DriverRepository $driverRepository)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers=$this->driverRepository->index();
        return view('pages.drivers.index',compact('drivers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $driver=$this->driverRepository->getById($id);
        return view('pages.drivers.show',compact('driver'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
