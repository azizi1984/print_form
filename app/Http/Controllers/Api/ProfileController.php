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

    public function store(Request $request)
    {
        $request->validate([
            'profile_id' => 'required',
            'profile_tax' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $profile = Profile::create([
                'profile_id' => $request->profile_id,
                'profile_tax' => $request->profile_tax,
                'status' => Status::Active->value,
                'profile_remark' => $request->profile_remark,
            ]);

            // $user = User::find(1);
            // $user->syncPermissions($request->service);
            // $user->syncPermissions([]);

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

        try {

            $profile = Profile::find($request->profile_id);
            $profile->update([
                'profile_tax' => $request->profile_tax,
                'profile_remark' => $request->profile_remark,
                'status' => $request->status,
            ]);

            return response()->json([
                'status' => Status::Active->value,
                'message' => 'update success',
            ]);

        } catch(\Exception $e){
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Error updating profile',
                'description' => $e->getMessage()
            ], 200);
        }
    }

    public function destroy(Request $request)
    {
        $profile = Profile::find($request->profile_id);
        $profile->delete();
        return response()->json([
            'status' => Status::Active->value,
            'message' => 'delete success'
        ]);
    }
}
