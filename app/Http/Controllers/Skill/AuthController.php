<?php

namespace App\Http\Controllers\Skill;

use App\Http\Controllers\Controller;
use App\Models\Skills;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // index 
    public function index()
    {
        $skill = Skills::all();
        return view('skill.index', compact('skill'));
    }

    // thêm kỹ năng
    public function store(Request $request)
    {
        if ($request->isMethod('POST')) {
            $params = $request->except('_token');
            $skill = Skills::create($params);
            if ($skill->id) {
                return redirect()->route('skill.add')->with('success', 'them thanh cong');
            }
        }
        return view('skill.add');
    }



    // sửa kỹ năng
    public function edit(Request $request, string $id)
    {
        $skill = Skills::find($id);
        if ($request->isMethod('POST')) {
            $params = $request->except('_token');
            $result = $skill->update($params);
            if ($result) {
                return redirect()->route('skill.edit', ['id' => $id])->with('success', 'sua thanh cong');
            }
        }
        return view('skill.edit', compact('skill'));
    }


    // xóa kỹ năng
    public function destroy(string $id)
    {
        $skill = Skills::find($id);
        $skill->delete();
        return redirect()->back()->with('success', 'xoa thanh cong');
    }
}
