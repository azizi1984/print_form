<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailTemplate;
use Illuminate\Support\Facades\DB;

class DetailTemplateController extends Controller
{
    public function index()
    {
        $detailTemplates = DetailTemplate::whereIn('profile_id', [Auth::user()->profile_id, 'ZZ00'])->get();
        return view('ex_declaration.detail_templates.index', compact('detailTemplates'));
    }

    public function show($id)
    {
        $detailTemplate = DetailTemplate::with('items')->findOrFail($id);
        $fields = DB::table('sysfield_ex')
            ->whereIn('print1', ['D1', 'D2'])
            ->where('app_show', 'Y')
            ->get();

        $layoutConfig = [];
        foreach ($detailTemplate->items as $item) {
            $layoutConfig[$item->cell_id][] = [
                'fieldId' => $item->field_name,
                'fieldName' => $item->field_name,
                'customText' => $item->custom_text
            ];
        }

        return view('ex_declaration.detail_templates.manage', compact('detailTemplate', 'fields', 'layoutConfig'));
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

            $detailTemplate->update($data);

            if ($layoutJson !== null) {
                // Delete old items
                $detailTemplate->items()->delete();

                // Insert new items
                $itemsToInsert = [];
                foreach ($layoutJson as $cellId => $fieldsList) {
                    if (is_array($fieldsList)) {
                        foreach ($fieldsList as $index => $fieldData) {
                            if (isset($fieldData['fieldId'])) {
                                $itemsToInsert[] = [
                                    'detail_template_id' => $detailTemplate->detail_template_id,
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
                    \App\Models\DetailTemplateItem::insert($itemsToInsert);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Failed to save detail layout: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to save layout: ' . $e->getMessage()], 500);
        }

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

    public function copy($id)
    {
        $template = DetailTemplate::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Replicate the template model
            $newTemplate = $template->replicate();
            $newTemplate->detail_template_name = 'Copy of ' . $template->detail_template_name;
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
                    'detail_template_id' => $newTemplate->detail_template_id,
                    'cell_id' => $item->cell_id,
                    'field_name' => $item->field_name,
                    'custom_text' => $item->custom_text,
                    'seq' => $item->seq,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            if (!empty($newItems)) {
                \App\Models\DetailTemplateItem::insert($newItems);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Copied successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Failed to copy detail template: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to copy template: ' . $e->getMessage()], 500);
        }
    }
}
