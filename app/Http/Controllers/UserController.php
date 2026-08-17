<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users (Supports DataTables Server-Side AJAX).
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $draw = $request->input('draw', 1);
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $orderColumnIndex = $request->input('order.0.column', 5);
            $orderDir = $request->input('order.0.dir', 'desc');

            $query = User::with('roles');

            $recordsTotal = User::count();

            // Search across ALL fields
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('username', 'like', "%{$searchValue}%")
                      ->orWhere('email', 'like', "%{$searchValue}%")
                      ->orWhere('comp_tax', 'like', "%{$searchValue}%")
                      ->orWhere('remark', 'like', "%{$searchValue}%")
                      ->orWhereHas('roles', function ($rq) use ($searchValue) {
                          $rq->where('name', 'like', "%{$searchValue}%");
                      });

                    $lowerSearch = mb_strtolower(trim($searchValue));
                    if ($lowerSearch === 'active' || $lowerSearch === 'เปิดใช้งาน' || $searchValue === '1') {
                        $q->orWhere('status', 1);
                    } elseif ($lowerSearch === 'inactive' || $lowerSearch === 'ปิดใช้งาน' || $searchValue === '0') {
                        $q->orWhere('status', 0);
                    }
                });
            }

            $recordsFiltered = $query->count();

            // Column ordering map
            $columnsMap = [
                0 => 'id',
                1 => 'name',
                2 => 'email',
                3 => 'id',
                4 => 'status',
                5 => 'created_at',
                6 => 'id',
            ];

            $orderColumn = $columnsMap[$orderColumnIndex] ?? 'created_at';
            $query->orderBy($orderColumn, $orderDir);

            $users = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($users as $index => $user) {
                // Name column HTML
                $initial = strtoupper(substr($user->name, 0, 1));
                $usernameHtml = !empty($user->username) ? '<small class="text-muted"><i class="bi bi-at"></i>' . e($user->username) . '</small>' : '';
                $nameHtml = '
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2 fw-bold" style="width: 32px; height: 32px; font-size: 13px;">
                            ' . e($initial) . '
                        </div>
                        <div>
                            <div class="fw-bold text-dark">' . e($user->name) . '</div>
                            ' . $usernameHtml . '
                        </div>
                    </div>';

                // Email column HTML
                $emailHtml = '<span class="text-dark"><i class="bi bi-envelope me-1 text-muted"></i>' . e($user->email) . '</span>';

                // Role column HTML
                $roleHtml = '';
                if ($user->roles->isNotEmpty()) {
                    foreach ($user->roles as $role) {
                        $roleHtml .= '<span class="badge bg-info text-dark px-2 py-1 me-1"><i class="bi bi-shield-check me-1"></i>' . e($role->name) . '</span>';
                    }
                } else {
                    $roleHtml = '<span class="badge bg-secondary px-2 py-1">No Role</span>';
                }

                // Status column HTML
                if (($user->status ?? 1) == 1) {
                    $statusHtml = '<span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Active</span>';
                } else {
                    $statusHtml = '<span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Inactive</span>';
                }

                // Date column HTML
                $createdAtHtml = $user->created_at ? $user->created_at->format('Y-m-d H:i') : '-';

                // Action column HTML (Edit & Delete buttons)
                $editUrl = route('users.edit', $user->id);
                $deleteUrl = route('users.destroy', $user->id);
                $csrfToken = csrf_token();

                $actionHtml = '
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="' . $editUrl . '" class="btn btn-sm btn-outline-warning" title="Edit User">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                        <form action="' . $deleteUrl . '" method="POST" class="d-inline" onsubmit="return confirm(\'คุณต้องการลบผู้ใช้งาน \\\'' . e($user->name) . '\\\' ใช่หรือไม่?\');">
                            <input type="hidden" name="_token" value="' . $csrfToken . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </form>
                    </div>';

                $data[] = [
                    'no' => $start + $index + 1,
                    'name' => $nameHtml,
                    'email' => $emailHtml,
                    'role' => $roleHtml,
                    'status' => $statusHtml,
                    'created_at' => $createdAtHtml,
                    'action' => $actionHtml,
                ];
            }

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['nullable', 'string'],
            'status' => ['nullable', 'integer'],
            'comp_tax' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $request->has('status') ? (int)$request->status : 1,
            'comp_tax' => $validated['comp_tax'] ?? null,
            'remark' => $validated['remark'] ?? null,
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('users.index')->with('success', 'สร้างผู้ใช้งานเรียบร้อยแล้ว (User created successfully)');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->first();

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['nullable', 'string'],
            'status' => ['nullable', 'integer'],
            'comp_tax' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'status' => $request->has('status') ? (int)$request->status : $user->status,
            'comp_tax' => $validated['comp_tax'] ?? null,
            'remark' => $validated['remark'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if (isset($validated['role'])) {
            $user->syncRoles($validated['role'] ? [$validated['role']] : []);
        }

        return redirect()->route('users.index')->with('success', 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว (User updated successfully)');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting current user
        if (auth()->check() && auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'ไม่สามารถลบบัญชีผู้ใช้งานที่กำลังใช้งานอยู่ได้');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว (User deleted successfully)');
    }
}