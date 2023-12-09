<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeJobPostResource;
use App\Models\TypeJobPost;
use Illuminate\Http\Request;

class TypeJobPostController extends Controller
{
    public function index()
    {
        $typejob = TypeJobPost::all();
        return TypeJobPostResource::collection($typejob);
    }
}
