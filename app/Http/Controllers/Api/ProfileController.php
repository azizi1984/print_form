<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Enums\Status;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function getAllProfiles()
    {
        $profile = Profile::all();
        return response()->json([
            'status' => Status::Active->value,
            'message' => 'get success',
            'profile' => $profile,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'profile_id' => 'required',
            'profile_tax' => 'required',
        ]);

        $profile = Profile::where('profile_id', $request->profile_id)
            ->orWhere('profile_tax', $request->profile_tax)
            ->first();

        if ($profile) {
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Profile or Tax already exists',
            ], 200);
        }

        DB::beginTransaction();

        try {
            $profile = Profile::create([
                'profile_id' => $request->profile_id,
                'profile_tax' => $request->profile_tax,
                'status' => $request->status ?? Status::Active->value,
                'remark' => $request->profile_remark ?? "",
            ]);

            DB::commit();

            return response()->json([
                'status' => Status::Active->value,
                'message' => 'create success',
                'profile' => $profile,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Error creating profile',
                'description' => $e->getMessage()
            ], 200);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'profile_id' => 'required',
            'profile_tax' => 'required',
        ]);

        $profile = Profile::where('profile_id', $request->profile_id)
            ->Where('profile_tax', $request->profile_tax)
            ->first();

        if (!$profile) {
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Cannot update profile. Please check profile id or profile tax',
            ], 200);
        }

        DB::beginTransaction();
        try {

            $profile->update([
                'profile_tax' => isset($request->profile_tax) ? $request->profile_tax : $profile->profile_tax,
                'remark' => isset($request->profile_remark) ? $request->profile_remark : $profile->remark,
                'status' => isset($request->status) ? $request->status : $profile->status,
            ]);
            
            DB::commit();
            
            return response()->json([
                'status' => Status::Active->value,
                'message' => 'update success',
            ]);

        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Error updating profile',
                'description' => $e->getMessage()
            ], 200);
        }
    }

    public function destroy(Request $request)
    {
        $profile = Profile::where('profile_id', $request->profile_id)
            ->first();

        if (!$profile) {
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Cannot delete profile. Please check profile id or profile tax',
            ], 200);
        }

        DB::beginTransaction();
        try {
            $profile->delete();
            DB::commit();
            return response()->json([
                'status' => Status::Active->value,
                'message' => 'delete success'
            ]);
        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Error deleting profile',
                'description' => $e->getMessage()
            ], 200);
        }
    }
}
