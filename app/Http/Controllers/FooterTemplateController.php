<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FooterTemplate;
use Illuminate\Support\Facades\DB;

class FooterTemplateController extends Controller
{
    public function index()
    {
        $footerTemplates = FooterTemplate::whereIn('profile_id', [Auth::user()->profile_id, 'ZZ00'])->get();
        return view('ex_declaration.footer_templates.index', compact('footerTemplates'));
    }

    public function show($id)
    {
        $footerTemplate = FooterTemplate::findOrFail($id);
        if (view()->exists('ex_declaration.footer_templates.manage')) {
            return view('ex_declaration.footer_templates.manage', compact('footerTemplate'));
        }
        return redirect()->route('footer-template')->with('info', 'ฟังก์ชันการจัดการ Layout ของ Footer Template อยู่ระหว่างการพัฒนา');
    }

    public function store(Request $request)
    {
        $request->validate([
            'footer_template_name' => 'required',
            'status' => 'required'
        ]);

        $data = $request->all();
        if (empty($data['profile_id'])) {
            $data['profile_id'] = Auth::user()->profile_id ?? 'ZZ00';
        }
        $data['created_by'] = Auth::id();

        $footerTemplate = FooterTemplate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $footerTemplate
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'footer_template_name' => 'required',
            'status' => 'required'
        ]);

        $footerTemplate = FooterTemplate::find($id);

        if (!$footerTemplate) {
            return response()->json(['success' => false, 'message' => 'Not found'], 200);
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $footerTemplate->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $footerTemplate
        ]);
    }

    public function destroy($id)
    {
        $footerTemplate = FooterTemplate::find($id);

        if (!$footerTemplate) {
            return response()->json(['success' => false, 'message' => 'Not found'], 200);
        }

        $footerTemplate->deleted_by = Auth::id();
        $footerTemplate->save();
        $footerTemplate->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    public function copy($id)
    {
        $template = FooterTemplate::findOrFail($id);

        DB::beginTransaction();
        try {
            $newTemplate = $template->replicate();
            $newTemplate->footer_template_name = 'Copy of ' . $template->footer_template_name;
            $newTemplate->profile_id = Auth::user()->profile_id ?? 'ZZ00';
            $newTemplate->created_by = Auth::id();
            $newTemplate->created_at = now();
            $newTemplate->updated_at = now();
            $newTemplate->updated_by = null;
            $newTemplate->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Copied successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Failed to copy footer template: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to copy template: ' . $e->getMessage()], 500);
        }
    }
}
