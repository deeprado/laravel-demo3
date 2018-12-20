<?php

namespace App\Manager\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 权限列表
     *
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function index()
    {
        $users = User::all(['id', 'name'])->toArray();

        $permissions = Permission::orderBy('created_at', 'desc')->paginate(10);
        return view('manager.permission.permission', ['permissions' => $permissions, 'users'=> $users]);
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
            'name'         => 'required|unique:permissions|min:2|max:20',
            'guard_name' => 'nullable|max:20',
            //'description'  => 'required|max:100',
            //'pid'          => 'required|integer|min:0'
        ], [
            'name.required'         => '权限名称必须填写',
            'name.unique'           => '权限名称已存在',
            'name.min'              => '权限名称至少2个字',
            'name.max'              => '权限名称至多20个字',
            'guard_name.required' => '守护名称必须填写',
            'guard_name.max'      => '守护名称至多20个字',
            'description.required'  => '权限描述必须填写',
            'description.max'       => '权限描述至多100个字',
            'pid.required'          => '权限层级必须选择',
            'pid.integer'           => '权限层级必须是一个整数',
            'pid.min'               => '权限层级至少为0'
        ]);

        $data = [
            'name' => $validatedData['name'],
            'guard_name' => $validatedData['guard_name'],
        ];
        $permission = Permission::create($data);

        if (empty($permission)) {
            session()->flash('error', '添加失败');
            return back();
        }

        session()->flash('success', '添加成功');
        return redirect()->route('permission');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 更新权限
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
            'description'  => 'required|max:100',
            'id'           => 'required|integer|min:0',
            'pid'          => 'required|integer|min:0'
        ], [
            'name.required'         => '权限名称必须填写',
            'name.min'              => '权限名称至少2个字',
            'name.max'              => '权限名称至多20个字',
            'guard_name.required' => '守护名称必须填写',
            'guard_name.max'      => '守护名称至多20个字',
            'description.required'  => '权限描述必须填写',
            'description.max'       => '权限描述至多100个字',
            'id.required'           => '索引不能为空',
            'id.integer'            => '索引必须为整数',
            'id.min'                => '索引至少为0',
            'pid.required'          => '权限层级必须选择',
            'pid.integer'           => '权限层级必须是一个整数',
            'pid.min'               => '权限层级至少为0'
        ]);

        $user = Auth::user();
        if ($user->is_boss === 0) {
            session()->flash('error', '无权限');
            return back();
        }
        $find = Permission::where(['name' => $validatedData['name']])->first();
        if ($find && $find->id != $validatedData['id']) {
            session()->flash('error', "权限名称{$validatedData['name']}已存在");
            return back();
        }

        $permission = Permission::find($validatedData['id']);
        if (!$permission) {
            session()->flash('error', '记录不存在');
            return back();
        }

        if($find->id == $permission->id){
            session()->flash('error', '不能选择自己');
            return back();
        }

        $permission->name = $validatedData['name'];
        $permission->pid = $validatedData['pid'];
        $permission->guard_name = $validatedData['guard_name'];
        $permission->description = $validatedData['description'];
        if (!$permission->save()) {
            session()->flash('error', '修改失败');
            return back();
        }
        session()->flash('success', '修改成功');
        return redirect()->route('permission');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 权限管理 - 删除权限
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
            if ($user->is_boss === 0) {
                return ['code' => 400, 'message' => '无权限'];
            }
            $permission = Permission::find($validatedData['id']);
            if (!$permission) {
                return ['code' => 400, 'message' => '记录不存在'];
            }

            if($permission->pid == 0){
                $second = Permission::where(['pid'=>$permission->id])->get();
                if($second->count() > 0) return ['code' => 400, 'message' => '当前权限组下还有子权限，不能删除'];
            }

            if (!$permission->delete()) {
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
     * 用户授权
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function user() {
        $user_roles = User::with('roles')->with('permissions')->paginate(10);
        $users = User::all();
        $roles = Role::all();
        $permissions = Permission::all();
        return view('manager.permission.user', ['roles' => $roles, 'users'=> $users, 'user_roles' => $user_roles, 'permissions' => $permissions]);
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
                'permission' => 'required|integer|min:0',
            ], [
                'permission.required' => '索引不能为空',
                'permission.integer'  => '索引必须为整数',
                'permission.min'      => '索引至少为0',
            ]);

            $permission = Permission::find($validatedData['permission']);
            if (!$permission) {
                return ['code' => 400, 'message' => '权限不存在'];
            }

            $users = $permission->users()->pluck('id');

            return ['code' => 200, 'message' => 'ok', 'data' => $users];
        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
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


        $permission = Permission::find($validatedData['id']);
        if (!$permission) {
            session()->flash('error', '权限不存在');
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
            $user->givePermissionTo($permission['name']);
        }

        session()->flash('success', '配置成功');
        return redirect()->route('permission');
    }


}