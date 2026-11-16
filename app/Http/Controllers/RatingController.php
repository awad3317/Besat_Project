<?php

namespace App\Http\Controllers;

class RatingController extends Controller
{
    /**
     * Display the customer ratings page.
     */
    public function index()
    {
        return view('pages.ratings.index');
    }
}
