<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Profile;
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
        if ($this->data['major']) {
            return response()->json([
                $this->data['major'],
            ], 200);
        } else {
            return response()->json([
                $this->data['major'],
            ], 404);
        }
    }
    public function createNewCV(Request $request)
    {
        if (Auth::guard('candidate')->check()) {
            $candidate = Auth::guard('candidate')->user();
            $candidate_id = $candidate->id;
            $cv = new Profile();
            $cv->candidate_id = $candidate_id;
            $cv->name = $candidate->name;
            $cv->email = $candidate->email;
            $cv->phone = $candidate->phone;
        }
    }
}
