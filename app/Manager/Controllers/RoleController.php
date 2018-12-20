<?php

namespace App\Manager\Controllers;

use Spatie\Permission\Models\Role;
use App\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 角色列表
     *
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function index()
    {
        $user = Auth::user();
        $roles = Role::orderBy('created_at', 'desc')->paginate(10);
        $role_users = [];
        foreach ($roles as $role) {
            $users = $role->users()->get();
            $role_users[] = [
                'role_name' => $role['name'],
                'users' => $users
            ];
        }
        $users = User::all(['id', 'name'])->toArray();
        $permissions = Permission::orderBy('created_at', 'desc')->paginate(10);
        return view('manager.permission.role', ['roles' => $roles, 'users'=> $users, 'role_users' => $role_users, 'permissions' => $permissions]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 创建角色
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|unique:roles|min:2|max:20',
            'guard_name' => 'required|max:20',
            //'description'  => 'required|max:100'
        ], [
            'name.required'         => '角色名称必须填写',
            'name.unique'           => '角色名称已存在',
            'name.min'              => '角色名称至少2个字',
            'name.max'              => '角色名称至多20个字',
            'guard_name.required' => '角色别名必须填写',
            'guard_name.max'      => '角色别名至多20个字',
            'description.required'  => '角色描述必须填写',
            'description.max'       => '角色描述至多100个字'
        ]);

        $role = Role::create([
            'name' => $validatedData['name'],
            'guard_name' => $validatedData['guard_name'],
        ]);

        if (empty($role)) {
            session()->flash('error', '添加失败');
            return back();
        }

        session()->flash('success', '添加成功');
        return redirect()->route('role');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 修改角色
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|min:2|max:20',
            'guard_name' => 'required|max:20',
            //'description'  => 'required|max:100',
            'id'           => 'required|integer|min:0'
        ], [
            'name.required'         => '角色名称必须填写',
            'name.min'              => '角色名称至少2个字',
            'name.max'              => '角色名称至多20个字',
            'guard_name.required' => '角色别名必须填写',
            'guard_name.max'      => '角色别名至多20个字',
            //'description.required'  => '角色描述必须填写',
            //'description.max'       => '角色描述至多100个字',
            'id.required'           => '索引不能为空',
            'id.integer'            => '索引必须为整数',
            'id.min'                => '索引至少为0'
        ]);

        $role = Role::find($validatedData['id']);
        if (!$role) {
            session()->flash('error', '记录不存在');
            return back();
        }
        $arole = Role::where([
            'name' => $validatedData['name'],
            'guard_name' => $validatedData['guard_name']
        ])->first();
        if ($arole && $arole->id != $validatedData['id']) {
            session()->flash('error', "角色名称{$validatedData['name']}已存在");
            return back();
        }

        $role->name = $validatedData['name'];
        $role->guard_name = $validatedData['guard_name'];
        if (!$role->save()) {
            session()->flash('error', '修改失败');
            return back();
        }
        session()->flash('success', '修改成功');
        return redirect()->route('role');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 删除角色
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function delete(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer|min:0'
            ], [
                'id.required' => '索引不能为空',
                'id.integer'  => '索引必须为整数',
                'id.min'      => '索引至少为0'
            ]);

            $user = Auth::user();

            $role = Role::find($validatedData['id']);
            if (!$role) {
                return ['code' => 400, 'message' => '记录不存在'];
            }

            $role_user = DB::select('select * from role_user where role_id = ?', [$role->id]);

            if (!empty($role_user)) {
                return ['code' => 400, 'message' => '当前角色下还有管理员不能删除 ' . $role->guard_name . ' 角色'];
            }

            if (!$role->delete()) {
                return ['code' => 400, 'message' => '删除失败'];
            }
            return ['code' => 200, 'message' => '删除成功'];
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return ['code' => 400, 'message' => $e->validator->errors()->first()];
            }
            return ['code' => 400, 'message' => '发生错误'];
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 分配管理员
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function allot_user(Request $request)
    {
        $validatedData = $request->validate([
            'id'   => 'required|integer|min:0',
            'user' => 'required|array',
        ], [
            'id.required'   => '索引不能为空',
            'id.integer'    => '索引必须为整数',
            'id.min'        => '索引至少为0',
            'user.required' => '索引不能为空',
            'user.array'  => '索引必须为数组',
        ]);


        $role = Role::find($validatedData['id']);
        if (!$role) {
            session()->flash('error', '角色不存在');
            return back();
        }
        $users = [];
        foreach ($validatedData['user'] as $user_id) {
            $user = User::find($user_id);
            if (!$user) {
                session()->flash('error', '用户不存在');
                return back();
            }
            $users[] = $user;
        }

        foreach ($users as $user) {
            $user->assignRole($role);
        }

        session()->flash('success', '配置成功');
        return redirect()->route('role');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 分配权限
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function allot_permission(Request $request)
    {
        $validatedData = $request->validate([
            'id'         => 'required|integer|min:0',
            'permission' => 'required',
        ], [
            'id.required'         => '索引不能为空',
            'id.integer'          => '索引必须为整数',
            'id.min'              => '索引至少为0',
            'permission.required' => '权限必选选择',
        ]);

        $auth = Auth::user();

        $role = Role::find($validatedData['id']);
        if (!$role) {
            session()->flash('error', '角色不存在');
            return back();
        }

        $permissions = [];
        foreach ($validatedData['permission'] as $permission_id) {
            $permission = Permission::find($permission_id);
            $permissions[] = $permission;
            //$permission->assignRole($role);
            $role->givePermissionTo($permission);

        }
        $role->syncPermissions($permissions);


        session()->flash('success', '添加成功');
        return redirect()->route('role');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - AJAX获取权限
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function AjaxGetPermission(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'role' => 'required|integer|min:0',
            ], [
                'role.required' => '索引不能为空',
                'role.integer'  => '索引必须为整数',
                'role.min'      => '索引至少为0',
            ]);

            $role = Role::find($validatedData['role']);
            if (!$role) {
                return ['code' => 400, 'message' => '角色不存在'];
            }

            $role_permissions = $role->permissions()->pluck('id');

            return ['code' => 200, 'message' => 'ok', 'data' => $role_permissions];
        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - AJAX获取权限
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function AjaxGetUser(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'role' => 'required|integer|min:0',
            ], [
                'role.required' => '索引不能为空',
                'role.integer'  => '索引必须为整数',
                'role.min'      => '索引至少为0',
            ]);

            $role = Role::find($validatedData['role']);
            if (!$role) {
                return ['code' => 400, 'message' => '角色不存在'];
            }

            $users = $role->users()->pluck('id');

            return ['code' => 200, 'message' => 'ok', 'data' => $users];
        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }
    }

}