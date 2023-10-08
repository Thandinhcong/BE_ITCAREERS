<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Models\Experiences;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index()
    {
        $experience = Experiences::all();
        return ExperienceResource::collection($experience);
    }

    public function store(Request $request)
    {
        $experience = Experiences::create($request->all());
        if ($experience) {
            return response()->json(['status' => 201, 'message' => "thêm kinh nghiệm thành công"]);
        }
        return new ExperienceResource($experience);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $experience = Experiences::find($id);
        if ($experience) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kinh nghiệm "]);
        }
        return new ExperienceResource($experience);
    }


    public function update(Request $request, string $id)
    {
        $experience = Experiences::find($id);
        if ($experience) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kinh nghiệm "]);
        }
        $experience->update($request->all());
        return response()->json(['status' => 200, 'message' => 'sửa kinh nghiệm thành công']);
    }


    public function destroy(string $id)
    {
        $experience = Experiences::find($id);
        if ($experience) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kinh nghiệm "]);
        }
        $experience->delete();
        return response()->json(['status' => 204, 'message' => 'xóa kinh nghiệm thành công']);
    }
}
