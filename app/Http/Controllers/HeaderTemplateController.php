<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HeaderTemplate;

class HeaderTemplateController extends Controller
{
    public function index()
    {
        $headerTemplates = HeaderTemplate::whereIn('profile_id', [Auth::user()->profile_id, 'ZZ00'])->get();
        return view('ex_declaration.header_templates.index', compact('headerTemplates'));
    }

    public function show($id)
    {
        $headerTemplate = HeaderTemplate::findOrFail($id);
        return view('ex_declaration.header_templates.manage', compact('headerTemplate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'header_template_name' => 'required',
            'status' => 'required'
        ]);

        $data = $request->all();
        if (empty($data['profile_id'])) {
            $data['profile_id'] = Auth::user()->profile_id ?? 'ZZ00';
        }
        $data['created_by'] = Auth::id();

        $headerTemplate = HeaderTemplate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $headerTemplate
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'header_template_name' => 'required',
            'status' => 'required'
        ]);

        $headerTemplate = HeaderTemplate::find($id);

        if (!$headerTemplate) {
            return response()->json(['success' => false, 'message' => 'Not found'], 200);
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $headerTemplate->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $headerTemplate
        ]);
    }

    public function destroy($id)
    {
        $headerTemplate = HeaderTemplate::find($id);

        if (!$headerTemplate) {
            return response()->json(['success' => false, 'message' => 'Not found'], 200);
        }

        $headerTemplate->deleted_by = Auth::id();
        $headerTemplate->save();
        $headerTemplate->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
