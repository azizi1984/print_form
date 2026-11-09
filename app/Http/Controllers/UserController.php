<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    /**
     * Display a listing of the users (Supports DataTables Server-Side AJAX).
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = trim((string) $request->input('search.value', ''));
            $orderColumnIndex = (int) $request->input('order.0.column', 6);
            $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            $hasNameColumn = Schema::hasColumn('users', 'name');
            $query = User::with('roles');

            $recordsTotal = User::count();

            // Search across all relevant user fields
            if ($searchValue !== '') {
                $query->where(function ($q) use ($searchValue, $hasNameColumn) {
                    $q->where('firstname', 'like', "%{$searchValue}%")
                        ->orWhere('lastname', 'like', "%{$searchValue}%")
                        ->orWhereRaw("CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')) LIKE ?", ["%{$searchValue}%"])
                        ->orWhere('username', 'like', "%{$searchValue}%")
                        ->orWhere('profile_id', 'like', "%{$searchValue}%")
                        ->orWhere('email', 'like', "%{$searchValue}%")
                        ->orWhere('comp_tax', 'like', "%{$searchValue}%")
                        ->orWhere('lsp_tax_no', 'like', "%{$searchValue}%")
                        ->orWhere('lsp_comp_nmt', 'like', "%{$searchValue}%")
                        ->orWhere('remark', 'like', "%{$searchValue}%")
                        ->orWhereHas('roles', function ($rq) use ($searchValue) {
                            $rq->where('name', 'like', "%{$searchValue}%");
                        });

                    if ($hasNameColumn) {
                        $q->orWhere('name', 'like', "%{$searchValue}%");
                    }

                    $lowerSearch = mb_strtolower($searchValue);
                    if ($lowerSearch === 'active' || $lowerSearch === 'เปิดใช้งาน') {
                        $q->orWhere('status', Status::Active->value);
                    } elseif ($lowerSearch === 'inactive' || $lowerSearch === 'ปิดใช้งาน') {
                        $q->orWhere('status', Status::InActive->value);
                    }
                });
            }

            $recordsFiltered = $query->count();

            // Column ordering map matching DataTables columns in index.blade.php
            $columnsMap = [
                0 => 'id',
                1 => 'firstname',
                2 => 'profile_id',
                3 => 'email',
                4 => 'id',
                5 => 'status',
                6 => 'created_at',
                7 => 'id',
            ];

            $orderColumn = $columnsMap[$orderColumnIndex] ?? 'created_at';
            $query->orderBy($orderColumn, $orderDir);

            $users = $query->skip($start)->take($length)->get();
            $currentUserId = auth()->id();

            $data = [];
            foreach ($users as $index => $user) {
                $displayName = $user->name;
                $initial = mb_strtoupper(mb_substr($displayName !== '' ? $displayName : 'U', 0, 1));

                $metaParts = [];
                if (!empty($user->username)) {
                    $metaParts[] = '<span class="text-muted"><i class="bi bi-at"></i>' . e($user->username) . '</span>';
                }
                if (!empty($user->comp_tax)) {
                    $metaParts[] = '<span class="text-muted"><i class="bi bi-building me-1"></i>' . e($user->comp_tax) . '</span>';
                }
                $metaHtml = !empty($metaParts) ? '<div class="small d-flex flex-wrap gap-2 mt-0.5">' . implode('<span class="text-muted">•</span>', $metaParts) . '</div>' : '';

                $nameHtml = '
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex justify-content-center align-items-center me-2 fw-bold text-white shadow-sm flex-shrink-0" style="width: 36px; height: 36px; font-size: 13px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                            ' . e($initial) . '
                        </div>
                        <div>
                            <div class="fw-semibold text-dark" style="font-size: 13.5px;">' . e($displayName) . '</div>
                            ' . $metaHtml . '
                        </div>
                    </div>';

                // Profile ID column HTML
                if (!empty($user->profile_id)) {
                    $centralBadge = $user->profile_id === 'ZZ00'
                        ? '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle ms-1" style="font-size: 10.5px;"><i class="bi bi-shield-lock me-1"></i>ส่วนกลาง</span>'
                        : '';
                    $profileHtml = '<span class="badge bg-light text-dark border px-2 py-1 fw-semibold" style="font-size: 12px;">' . e($user->profile_id) . '</span>' . $centralBadge;
                } else {
                    $profileHtml = '<span class="text-muted small">-</span>';
                }

                // Email column HTML
                $emailHtml = !empty($user->email)
                    ? '<span class="text-secondary"><i class="bi bi-envelope me-1.5 text-muted"></i>' . e($user->email) . '</span>'
                    : '<span class="text-muted small">-</span>';

                // Role column HTML
                $roleHtml = '';
                if ($user->roles->isNotEmpty()) {
                    foreach ($user->roles as $role) {
                        $roleHtml .= '<span class="badge badge-modern badge-primary-modern me-1"><i class="bi bi-shield-check me-1"></i>' . e($role->name) . '</span>';
                    }
                } else {
                    $roleHtml = '<span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="border-radius: 6px; font-size: 11.5px;">No Role</span>';
                }

                // Status column HTML
                if (($user->status ?? Status::Active->value) === Status::Active->value) {
                    $statusHtml = '<span class="badge badge-modern badge-success-modern"><i class="bi bi-check-circle-fill me-1"></i>Active</span>';
                } else {
                    $statusHtml = '<span class="badge badge-modern badge-danger-modern"><i class="bi bi-dash-circle-fill me-1"></i>Inactive</span>';
                }

                // Date column HTML
                $createdAtHtml = $user->created_at
                    ? '<span class="text-muted" style="font-size: 13px;">' . $user->created_at->format('d/m/Y H:i') . '</span>'
                    : '<span class="text-muted">-</span>';

                // Action column HTML (Edit & Delete buttons)
                $editUrl = route('users.edit', $user->id);
                $deleteUrl = route('users.destroy', $user->id);
                $csrfToken = csrf_token();

                if ($currentUserId !== null && (int) $currentUserId === (int) $user->id) {
                    $deleteBtnHtml = '
                        <span class="d-inline-block" title="ไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้">
                            <button type="button" class="btn-action btn-action-delete disabled opacity-50" disabled style="cursor: not-allowed;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </span>';
                } else {
                    $deleteBtnHtml = '
                        <form action="' . $deleteUrl . '" method="POST" class="d-inline form-delete-user" data-user-name="' . e($displayName) . '">
                            <input type="hidden" name="_token" value="' . $csrfToken . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn-action btn-action-delete" title="ลบผู้ใช้">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>';
                }

                $actionHtml = '
                    <div class="d-flex justify-content-center align-items-center gap-1.5">
                        <a href="' . $editUrl . '" class="btn-action btn-action-edit" title="แก้ไขข้อมูลผู้ใช้">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        ' . $deleteBtnHtml . '
                    </div>';

                $data[] = [
                    'no' => $start + $index + 1,
                    'name' => $nameHtml,
                    'profile_id' => $profileHtml,
                    'email' => $emailHtml,
                    'role' => $roleHtml,
                    'status' => $statusHtml,
                    'created_at' => $createdAtHtml,
                    'action' => $actionHtml,
                ];
            }

            return response()->json([
                'draw' => $draw,
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
    public function create(): View
    {
        $roles = Role::orderBy('name')->pluck('name', 'name')->all();
        $profiles = Profile::where('status', Status::Active->value)->orderBy('profile_id')->get();

        return view('admin.users.create', compact('roles', 'profiles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Backward compatibility if 'name' is submitted without 'firstname'
        if (!$request->filled('firstname') && $request->filled('name')) {
            $parts = preg_split('/\s+/', trim((string) $request->input('name')), 2);
            $request->merge([
                'firstname' => $parts[0] ?? '',
                'lastname' => $parts[1] ?? ($request->input('lastname') ?? ''),
            ]);
        }

        $profileId = strtoupper(trim((string) $request->input('profile_id', '')));
        $request->merge(['profile_id' => $profileId]);

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['nullable', 'string', 'max:255'],
            'profile_id' => ['required', 'string', 'max:5'],
            'username' => [
                'required',
                'string',
                'max:25',
                Rule::unique('users', 'username')->where(function ($query) use ($profileId) {
                    return $query->where('profile_id', $profileId)->whereNull('deleted_at');
                }),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'status' => ['required', 'integer', 'in:0,1'],
            'comp_tax' => ['nullable', 'string', 'max:15'],
            'lsp' => ['nullable', 'string', 'max:2'],
            'lsp_tax_no' => ['nullable', 'string', 'max:13'],
            'lsp_comp_nmt' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:255'],
        ], [
            'firstname.required' => 'กรุณากรอกชื่อจริง (First Name)',
            'profile_id.required' => 'กรุณาระบุรหัส Profile ID สำหรับเข้าสู่ระบบ',
            'profile_id.max' => 'รหัส Profile ID ต้องมีความยาวไม่เกิน 5 ตัวอักษร',
            'username.required' => 'กรุณากรอก Username สำหรับเข้าสู่ระบบ',
            'username.max' => 'Username ต้องมีความยาวไม่เกิน 25 ตัวอักษร',
            'username.unique' => 'Username นี้มีอยู่แล้วภายใต้ Profile ID ที่เลือก',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานในระบบแล้ว',
            'password.required' => 'กรุณากำหนดรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'firstname' => trim($validated['firstname']),
                'lastname' => isset($validated['lastname']) ? trim($validated['lastname']) : '',
                'username' => trim($validated['username']),
                'profile_id' => $validated['profile_id'],
                'email' => !empty($validated['email']) ? trim($validated['email']) : null,
                'password' => Hash::make($validated['password']),
                'status' => (int) $validated['status'],
                'comp_tax' => $validated['comp_tax'] ?? null,
                'lsp' => $validated['lsp'] ?? null,
                'lsp_tax_no' => $validated['lsp_tax_no'] ?? null,
                'lsp_comp_nmt' => $validated['lsp_comp_nmt'] ?? null,
                'remark' => $validated['remark'] ?? null,
            ]);

            if (!empty($validated['role'])) {
                $user->assignRole($validated['role']);
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            }

            DB::commit();

            return redirect()
                ->route('users.index')
                ->with('success', 'สร้างผู้ใช้งานเรียบร้อยแล้ว (User created successfully)');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการสร้างผู้ใช้งาน: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(int|string $id): View
    {
        $user = User::findOrFail($id);
        $roles = Role::orderBy('name')->pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->first();
        $profiles = Profile::where('status', Status::Active->value)->orderBy('profile_id')->get();

        return view('admin.users.edit', compact('user', 'roles', 'userRole', 'profiles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Backward compatibility if 'name' is submitted without 'firstname'
        if (!$request->filled('firstname') && $request->filled('name')) {
            $parts = preg_split('/\s+/', trim((string) $request->input('name')), 2);
            $request->merge([
                'firstname' => $parts[0] ?? '',
                'lastname' => $parts[1] ?? ($request->input('lastname') ?? ''),
            ]);
        }

        $profileId = strtoupper(trim((string) $request->input('profile_id', $user->profile_id ?? '')));
        $request->merge(['profile_id' => $profileId]);

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['nullable', 'string', 'max:255'],
            'profile_id' => ['required', 'string', 'max:5'],
            'username' => [
                'required',
                'string',
                'max:25',
                Rule::unique('users', 'username')
                    ->where(function ($query) use ($profileId) {
                        return $query->where('profile_id', $profileId)->whereNull('deleted_at');
                    })
                    ->ignore($user->id),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'status' => ['required', 'integer', 'in:0,1'],
            'comp_tax' => ['nullable', 'string', 'max:15'],
            'lsp' => ['nullable', 'string', 'max:2'],
            'lsp_tax_no' => ['nullable', 'string', 'max:13'],
            'lsp_comp_nmt' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:255'],
        ], [
            'firstname.required' => 'กรุณากรอกชื่อจริง (First Name)',
            'profile_id.required' => 'กรุณาระบุรหัส Profile ID สำหรับเข้าสู่ระบบ',
            'profile_id.max' => 'รหัส Profile ID ต้องมีความยาวไม่เกิน 5 ตัวอักษร',
            'username.required' => 'กรุณากรอก Username สำหรับเข้าสู่ระบบ',
            'username.max' => 'Username ต้องมีความยาวไม่เกิน 25 ตัวอักษร',
            'username.unique' => 'Username นี้มีอยู่แล้วภายใต้ Profile ID ที่เลือก',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานในระบบแล้ว',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ]);

        // Prevent currently logged-in user from deactivating their own account
        if (auth()->check() && (int) auth()->id() === (int) $user->id && (int) $validated['status'] === Status::InActive->value) {
            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถเปลี่ยนสถานะบัญชีของตนเองเป็น Inactive (ปิดใช้งาน) ได้');
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'firstname' => trim($validated['firstname']),
                'lastname' => isset($validated['lastname']) ? trim($validated['lastname']) : '',
                'username' => trim($validated['username']),
                'profile_id' => $validated['profile_id'],
                'email' => !empty($validated['email']) ? trim($validated['email']) : null,
                'status' => (int) $validated['status'],
                'comp_tax' => $validated['comp_tax'] ?? null,
                'lsp' => $validated['lsp'] ?? null,
                'lsp_tax_no' => $validated['lsp_tax_no'] ?? null,
                'lsp_comp_nmt' => $validated['lsp_comp_nmt'] ?? null,
                'remark' => $validated['remark'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            if ($request->exists('role')) {
                $user->syncRoles(!empty($validated['role']) ? [$validated['role']] : []);
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            }

            DB::commit();

            return redirect()
                ->route('users.index')
                ->with('success', 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว (User updated successfully)');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูลผู้ใช้งาน: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage (Soft Delete).
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Prevent deleting current user
        if (auth()->check() && (int) auth()->id() === (int) $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ไม่สามารถลบบัญชีผู้ใช้งานที่กำลังใช้งานอยู่ได้');
        }

        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();

            return redirect()
                ->route('users.index')
                ->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว (User deleted successfully)');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('users.index')
                ->with('error', 'เกิดข้อผิดพลาดในการลบผู้ใช้งาน: ' . $e->getMessage());
        }
    }
}
