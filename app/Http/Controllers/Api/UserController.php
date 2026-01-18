<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Enums\Status;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $user = User::all();
        return response()->json([
            'status' => Status::Active->value,
            'message' => 'get success',
            'user' => $user,
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'comp_tax' => 'required',
            'profile_id' => 'required',
        ]);

        $user = User::where('username', $request->username)
            ->Where('profile_id', $request->profile_id)
            ->first();

        if ($user) {
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'User already exists',
            ], 200);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'firstname' => $request->firstname ?? "",
                'lastname' => $request->lastname ?? "",
                'email' => $request->email ?? "",
                'comp_tax' => $request->comp_tax,
                'profile_id' => $request->profile_id,
                'status' => $request->status ?? Status::Active->value,
                'remark' => $request->remark ?? "",
            ]);

            $user = User::find($user->id);
            $user->syncPermissions([]);
            $user->syncPermissions($request->service);

            DB::commit();

            return response()->json([
                'status' => Status::Active->value,
                'message' => 'create success',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Error creating user',
                'description' => $e->getMessage()
            ], 200);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'profile_id' => 'required',
        ]);

        $user = User::where('username', $request->username)
            ->Where('profile_id', $request->profile_id)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Cannot update user. Please check user username or profile id',
            ], 200);
        }

        DB::beginTransaction();
        try {

            $user->update([
                'password' => isset($request->password) ? Hash::make($request->password) : $user->password,
                'firstname' => isset($request->firstname) ? $request->firstname : $user->firstname,
                'lastname' => isset($request->lastname) ? $request->lastname : $user->lastname,
                'comp_tax' => isset($request->comp_tax) ? $request->comp_tax : $user->comp_tax,
                'email' => isset($request->email) ? $request->email : $user->email,
                'status' => isset($request->status) ? $request->status : $user->status,
                'remark' => isset($request->remark) ? $request->remark : $user->remark,
            ]);

            $user = User::find($user->id);
            $user->syncPermissions([]);
            $user->syncPermissions($request->service);

            DB::commit();

            return response()->json([
                'status' => Status::Active->value,
                'message' => 'update success',
            ]);

        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Error updating user',
                'description' => $e->getMessage()
            ], 200);
        }
    }

    public function destroy(Request $request)
    {
        $user = User::where('username', $request->username)
            ->Where('profile_id', $request->profile_id)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Cannot delete user. Please check user username or profile id',
            ], 200);
        }

        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            return response()->json([
                'status' => Status::Active->value,
                'message' => 'delete success'
            ]);
        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => Status::InActive->value,
                'message' => 'Error deleting user',
                'description' => $e->getMessage()
            ], 200);
        }
    }
}
