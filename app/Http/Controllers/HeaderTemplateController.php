<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HeaderTemplate;
use App\Models\SysfieldEx;
use Illuminate\Support\Facades\DB;

class HeaderTemplateController extends Controller
{
    /**
     * ดึง profile_id ของผู้ใช้ปัจจุบัน
     */
    private function getUserProfileId(): string
    {
        return Auth::user()->profile_id ?? 'ZZ00';
    }

    /**
     * ค้นหาเทมเพลตที่ผู้ใช้มีสิทธิ์เข้าถึง (เทมเพลตของโปรไฟล์ตนเอง หรือ เทมเพลตกลาง ZZ00)
     */
    private function findAccessibleTemplate($id)
    {
        $userProfileId = $this->getUserProfileId();
        return HeaderTemplate::where('header_template_id', $id)
            ->whereIn('profile_id', array_unique([$userProfileId, 'ZZ00']))
            ->first();
    }

    /**
     * ตรวจสอบสิทธิ์ว่าสามารถแก้ไขหรือลบเทมเพลตนี้ได้หรือไม่
     * (ผู้ใช้ทั่วไปไม่อนุญาตให้แก้ไข/ลบเทมเพลตกลาง ZZ00)
     */
    private function canModifyTemplate(HeaderTemplate $template): bool
    {
        $userProfileId = $this->getUserProfileId();
        if ($userProfileId !== 'ZZ00' && $template->profile_id === 'ZZ00') {
            return false;
        }
        return $template->profile_id === $userProfileId;
    }

    public function index()
    {
        $userProfileId = $this->getUserProfileId();
        $headerTemplates = HeaderTemplate::whereIn('profile_id', array_unique([$userProfileId, 'ZZ00']))
            ->orderByRaw("CASE WHEN profile_id = 'ZZ00' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();
        return view('ex_declaration.header_templates.index', compact('headerTemplates'));
    }

    public function show(Request $request, $id)
    {
        $headerTemplate = HeaderTemplate::with('items')
            ->where('header_template_id', $id)
            ->whereIn('profile_id', array_unique([$this->getUserProfileId(), 'ZZ00']))
            ->firstOrFail();

        // ตรวจสอบ Force Refresh (?refresh=1) เพื่อล้างแคชและดึงข้อมูลสดจากฐานข้อมูล (แนวทางที่ 2)
        $forceRefresh = $request->boolean('refresh') || $request->has('refresh');
        $fields = SysfieldEx::getHeaderFields($forceRefresh);

        $layoutConfig = [];
        foreach ($headerTemplate->items as $item) {
            $layoutConfig[$item->cell_id][] = [
                'fieldId' => $item->field_name,
                'fieldName' => $item->field_name,
                'customText' => $item->custom_text
            ];
        }

        return view('ex_declaration.header_templates.manage', compact('headerTemplate', 'fields', 'layoutConfig'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'header_template_name' => 'required',
            'status' => 'required'
        ]);

        $data = $request->only(['header_template_name', 'description', 'status']);
        // กำหนด profile_id จากผู้ใช้ที่ล็อกอินอยู่เสมอ เพื่อป้องกัน profile spoofing / IDOR
        $data['profile_id'] = $this->getUserProfileId();
        $data['created_by'] = Auth::id();

        $headerTemplate = HeaderTemplate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $headerTemplate
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'header_template_name' => 'required',
            'status' => 'required'
        ]);

        $headerTemplate = $this->findAccessibleTemplate($id);

        if (!$headerTemplate) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลเทมเพลต'], 404);
        }

        if (!$this->canModifyTemplate($headerTemplate)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่อนุญาตให้แก้ไขเทมเพลตมาตรฐานส่วนกลาง (กรุณาคัดลอกเทมเพลตเพื่อสร้างเป็นของท่านเองก่อน)'
            ], 403);
        }

        $data = $request->only(['header_template_name', 'description', 'status']);
        $data['updated_by'] = Auth::id();

        try {
            $headerTemplate->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'data' => $headerTemplate
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update header template: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $headerTemplate = $this->findAccessibleTemplate($id);

        if (!$headerTemplate) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลเทมเพลต'], 404);
        }

        if (!$this->canModifyTemplate($headerTemplate)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่อนุญาตให้ลบเทมเพลตมาตรฐานส่วนกลาง'
            ], 403);
        }

        $headerTemplate->deleted_by = Auth::id();
        $headerTemplate->save();
        $headerTemplate->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    public function copy($id)
    {
        $template = HeaderTemplate::with('items')
            ->where('header_template_id', $id)
            ->whereIn('profile_id', array_unique([$this->getUserProfileId(), 'ZZ00']))
            ->first();

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลเทมเพลต'], 404);
        }

        DB::beginTransaction();
        try {
            // Replicate the template model
            $newTemplate = $template->replicate();
            $newTemplate->header_template_name = 'Copy of ' . $template->header_template_name;
            $newTemplate->profile_id = $this->getUserProfileId();
            $newTemplate->created_by = Auth::id();
            $newTemplate->created_at = now();
            $newTemplate->updated_at = now();
            $newTemplate->updated_by = null;
            $newTemplate->save();

            // Replicate associated layout items
            $newItems = [];
            foreach ($template->items as $item) {
                $newItems[] = [
                    'header_template_id' => $newTemplate->header_template_id,
                    'cell_id' => $item->cell_id,
                    'field_name' => $item->field_name,
                    'custom_text' => $item->custom_text,
                    'seq' => $item->seq,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            if (!empty($newItems)) {
                \App\Models\HeaderTemplateItem::insert($newItems);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Copied successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Failed to copy header template: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to copy template: ' . $e->getMessage()], 500);
        }
    }

    public function saveLayout(Request $request, $id)
    {
        $headerTemplate = $this->findAccessibleTemplate($id);

        if (!$headerTemplate) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลเทมเพลต'], 404);
        }

        if (!$this->canModifyTemplate($headerTemplate)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่อนุญาตให้แก้ไขเลย์เอาต์ของเทมเพลตมาตรฐานส่วนกลาง (กรุณาคัดลอกเทมเพลตเพื่อสร้างเป็นของท่านเองก่อน)'
            ], 403);
        }

        $layoutData = $request->input('layout');
        if (is_string($layoutData)) {
            $layoutData = json_decode($layoutData, true);
        }

        if (!is_array($layoutData)) {
            $layoutData = [];
        }

        DB::beginTransaction();
        try {
            // Delete old items
            $headerTemplate->items()->delete();

            // Insert new items
            $itemsToInsert = [];
            foreach ($layoutData as $cellId => $fieldsList) {
                if (is_array($fieldsList)) {
                    foreach ($fieldsList as $index => $fieldData) {
                        if (isset($fieldData['fieldId'])) {
                            $itemsToInsert[] = [
                                'header_template_id' => $headerTemplate->header_template_id,
                                'cell_id' => $cellId,
                                'field_name' => $fieldData['fieldId'],
                                'custom_text' => $fieldData['customText'] ?? null,
                                'seq' => $index + 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            }

            if (!empty($itemsToInsert)) {
                \App\Models\HeaderTemplateItem::insert($itemsToInsert);
            }

            $headerTemplate->updated_by = Auth::id();
            $headerTemplate->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'บันทึก Layout สำเร็จเรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Failed to save header layout: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึก Layout: ' . $e->getMessage()
            ], 500);
        }
    }
}
