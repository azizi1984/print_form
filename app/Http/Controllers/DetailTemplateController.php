<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailTemplate;

class DetailTemplateController extends Controller
{
    public function index()
    {
        $detailTemplates = DetailTemplate::whereIn('profile_id', [Auth::user()->profile_id, 'ZZ00'])->get();
        return view('ex_declaration.detail_templates.index', compact('detailTemplates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'detail_template_name' => 'required',
            'status' => 'required'
        ]);

        $data = $request->all();
        if (empty($data['profile_id'])) {
            $data['profile_id'] = Auth::user()->profile_id ?? 'ZZ00';
        }
        $data['created_by'] = Auth::id();

        $detailTemplate = DetailTemplate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $detailTemplate
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'detail_template_name' => 'required',
            'status' => 'required'
        ]);

        $detailTemplate = DetailTemplate::find($id);

        if (!$detailTemplate) {
            return response()->json(['success' => false, 'message' => 'Not found'], 200);
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $detailTemplate->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $detailTemplate
        ]);
    }

    public function destroy($id)
    {
        $detailTemplate = DetailTemplate::find($id);

        if (!$detailTemplate) {
            return response()->json(['success' => false, 'message' => 'Not found'], 200);
        }

        $detailTemplate->deleted_by = Auth::id();
        $detailTemplate->save();
        $detailTemplate->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
