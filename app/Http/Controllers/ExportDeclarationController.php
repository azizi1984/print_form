<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\FormTemplate;
use App\Models\ProfileTemplate;
use App\Models\HeaderTemplate;
use App\Models\DetailTemplate;
use App\Models\FooterTemplate;

class ExportDeclarationController extends Controller
{
    public function index()
    {
        $userProfileId = Auth::user()->profile_id ?? 'ZZ00';

        $profileTemplates = ProfileTemplate::with(['headerTemplate', 'detailTemplate', 'footerTemplate'])
            ->whereIn('profile_id', array_unique(array_filter([$userProfileId, 'ZZ00'])))
            ->orderByDesc('created_at')
            ->get();

        // ดึงเฉพาะ Header, Detail, Footer Template ที่อยู่ใน profile_id เดียวกัน และมีสถานะ Active (status = 1)
        $headerTemplates = HeaderTemplate::where('profile_id', $userProfileId)
            ->where('status', 1)
            ->orderBy('header_template_name')
            ->get();

        $detailTemplates = DetailTemplate::where('profile_id', $userProfileId)
            ->where('status', 1)
            ->orderBy('detail_template_name')
            ->get();

        $footerTemplates = FooterTemplate::where('profile_id', $userProfileId)
            ->where('status', 1)
            ->orderBy('footer_template_name')
            ->get();

        return view('ex_declaration.profile_templates.index', compact(
            'profileTemplates',
            'headerTemplates',
            'detailTemplates',
            'footerTemplates'
        ));
    }

    public function store(Request $request)
    {
        $userProfileId = Auth::user()->profile_id ?? 'ZZ00';

        $request->validate([
            'profile_template_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('profile_templates', 'profile_template_name')
                    ->where(fn ($query) => $query->where('profile_id', $userProfileId)->whereNull('deleted_at'))
            ],
            'status' => 'required|in:0,1',
            'header_template_id' => 'nullable|integer',
            'detail_template_id' => 'nullable|integer',
            'footer_template_id' => 'nullable|integer',
            'description' => 'nullable|string|max:255',
        ], [
            'profile_template_name.required' => 'กรุณาระบุชื่อ Profile Template',
            'profile_template_name.unique' => 'ชื่อ Profile Template นี้มีอยู่แล้วใน Profile ของคุณ กรุณาตั้งชื่ออื่น',
            'status.required' => 'กรุณาเลือกสถานะการใช้งาน',
        ]);

        $data = $request->only([
            'profile_template_name',
            'description',
            'header_template_id',
            'detail_template_id',
            'footer_template_id',
            'status'
        ]);
        $data['profile_id'] = $userProfileId;
        $data['created_by'] = Auth::id();

        $profileTemplate = ProfileTemplate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'สร้าง Profile Template สำเร็จ',
            'data' => $profileTemplate
        ]);
    }

    public function update(Request $request, $id)
    {
        $profileTemplate = ProfileTemplate::find($id);

        if (!$profileTemplate) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล Profile Template'], 404);
        }

        $userProfileId = $profileTemplate->profile_id ?? (Auth::user()->profile_id ?? 'ZZ00');

        $request->validate([
            'profile_template_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('profile_templates', 'profile_template_name')
                    ->where(fn ($query) => $query->where('profile_id', $userProfileId)->whereNull('deleted_at'))
                    ->ignore($id, 'profile_template_id')
            ],
            'status' => 'required|in:0,1',
            'header_template_id' => 'nullable|integer',
            'detail_template_id' => 'nullable|integer',
            'footer_template_id' => 'nullable|integer',
            'description' => 'nullable|string|max:255',
        ], [
            'profile_template_name.required' => 'กรุณาระบุชื่อ Profile Template',
            'profile_template_name.unique' => 'ชื่อ Profile Template นี้มีอยู่แล้วใน Profile ของคุณ กรุณาตั้งชื่ออื่น',
            'status.required' => 'กรุณาเลือกสถานะการใช้งาน',
        ]);

        $data = $request->only([
            'profile_template_name',
            'description',
            'header_template_id',
            'detail_template_id',
            'footer_template_id',
            'status'
        ]);
        $data['updated_by'] = Auth::id();

        $profileTemplate->update($data);

        return response()->json([
            'success' => true,
            'message' => 'อัปเดต Profile Template สำเร็จ',
            'data' => $profileTemplate
        ]);
    }

    public function destroy($id)
    {
        $profileTemplate = ProfileTemplate::find($id);

        if (!$profileTemplate) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล Profile Template'], 404);
        }

        $profileTemplate->deleted_by = Auth::id();
        $profileTemplate->save();
        $profileTemplate->delete();

        return response()->json(['success' => true, 'message' => 'ลบ Profile Template สำเร็จ']);
    }
}
