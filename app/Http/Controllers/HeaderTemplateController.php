<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HeaderTemplate;
use Illuminate\Support\Facades\DB;

class HeaderTemplateController extends Controller
{
    public function index()
    {
        $headerTemplates = HeaderTemplate::whereIn('profile_id', [Auth::user()->profile_id, 'ZZ00'])->get();
        return view('ex_declaration.header_templates.index', compact('headerTemplates'));
    }

    public function show($id)
    {
        $headerTemplate = HeaderTemplate::with('items')->findOrFail($id);
        $fields = DB::connection('print')->table('sysfield_ex')
            ->whereIn('print1', ['HL', 'HR'])
            ->where('app_show', 'Y')
            ->orderBy('app_showt', 'asc')
            ->get();

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

        DB::beginTransaction();
        try {
            $layoutJson = null;
            if ($request->has('description')) {
                $descriptionValue = $request->input('description');
                // Check if description is a valid layout JSON array
                $decoded = json_decode($descriptionValue, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $layoutJson = $decoded;
                    unset($data['description']); // Prevent saving JSON to VARCHAR(255) column
                }
            }

            $headerTemplate->update($data);

            if ($layoutJson !== null) {
                // Delete old items
                $headerTemplate->items()->delete();

                // Insert new items
                $itemsToInsert = [];
                foreach ($layoutJson as $cellId => $fieldsList) {
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
                                    'updated_at' => now()
                                ];
                            }
                        }
                    }
                }
                if (!empty($itemsToInsert)) {
                    \App\Models\HeaderTemplateItem::insert($itemsToInsert);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Failed to save header layout: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to save layout: ' . $e->getMessage()], 500);
        }

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

    public function copy($id)
    {
        $template = HeaderTemplate::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Replicate the template model
            $newTemplate = $template->replicate();
            $newTemplate->header_template_name = 'Copy of ' . $template->header_template_name;
            $newTemplate->profile_id = Auth::user()->profile_id ?? 'ZZ00';
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
            return response()->json(['success' => true, 'message' => 'Copied successfully']);
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
        $headerTemplate = HeaderTemplate::findOrFail($id);

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

