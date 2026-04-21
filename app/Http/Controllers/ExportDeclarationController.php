<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FormTemplate;
use App\Models\ProfileTemplate;

class ExportDeclarationController extends Controller
{
    public function index()
    {
        $profileTemplates = ProfileTemplate::where('profile_id', Auth::user()->profile_id)->get();
        return view('ex_declaration.index', compact('profileTemplates'));
    }

    // public function create()
    // {
    //     return view('ex_declaration.create');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'profile_template_name' => 'required',
            'status' => 'required'
        ]);

        $profileTemplates = ProfileTemplate::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $profileTemplates
        ]);
    }

    // public function show($id)
    // {
    //     $ProfileTemplate = ProfileTemplate::find($id);
    //     return view('ex_declaration.show', compact('ProfileTemplate'));
    // }

    // public function edit($id)
    // {
    //     $ProfileTemplate = ProfileTemplate::find($id);
    //     return view('ex_declaration.edit', compact('ProfileTemplate'));
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'profile_template_name' => 'required',
            'status' => 'required'
        ]);

        $profileTemplates = ProfileTemplate::find($id);

        if (!$profileTemplates) {
            return response()->json(['success' => false, 'message' => 'Not found'], 200);
        }

        $profileTemplates->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $profileTemplates
        ]);
    }

    public function destroy($id)
    {
        $profileTemplates = ProfileTemplate::find($id);

        if (!$profileTemplates) {
            return response()->json(['success' => false, 'message' => 'Not found'], 200);
        }

        // Uses Soft Delete because the ProfileTemplate model uses the SoftDeletes trait
        $profileTemplates->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
