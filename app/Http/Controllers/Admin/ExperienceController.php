<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperienceController extends Controller
{
    public function index()
    {
        $experience = Experience::all();
        return ExperienceResource::collection($experience);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'experience' => 'required|string|unique:experience',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $experience = Experience::create($request->all());
        }
        if ($experience) {
            return response()->json(['status' => 'success', 'message' => 'Thêm thành công'], 200);
        } else {
            return response()->json(['status' => 'fail', 'message' => 'error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $experience = Experience::find($id);
        if ($experience) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kinh nghiệm "]);
        }
        return new ExperienceResource($experience);
    }


    public function update(Request $request, string $id)
    {
        $experience = Experience::find($id);
        if ($experience) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kinh nghiệm "]);
        }
        $experience->update($request->all());
        return response()->json(['status' => 200, 'message' => 'sửa kinh nghiệm thành công']);
    }


    public function destroy(string $id)
    {
        $experience = Experience::find($id);
        if ($experience) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kinh nghiệm "]);
        }
        $experience->delete();
        return response()->json(['status' => 204, 'message' => 'xóa kinh nghiệm thành công']);
    }
}
