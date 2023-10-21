<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateCvController extends Controller
{
    private $data;
    public function __construct()
    {
        $this->data = [];
    }
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $this->data['major'] = Major::all();
    }
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::guard('candidate')->check()) {
            $id = Auth::guard('candidate')->user()->id;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
