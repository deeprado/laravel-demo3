<?php

namespace App\Manager\Controllers;

use App\Http\Facades\Arr;
use App\Models\Department;
use App\Models\Evection;
use App\Models\Leave;
use App\Models\Organization;
use App\Models\Out;
use App\Models\Post;
use App\Models\Visit;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 获取部门无限极分类
     *
     * @param Department $departments 部门集合
     * @param integer    $pid         父级ID
     * @param bool       $returnIds   返回类型
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function GetDepartment($departments, $pid, $returnIds = true)
    {
        $format = [];
        foreach ($departments as $k => $department) {
            if ($returnIds) {
                if ($department->pid == $pid) {
                    $format[] = $department->id;
                    $format = array_merge($format, $this->GetDepartment($departments, $department->id, $returnIds));
                }
            } else {
                if ($department->pid == $pid) {
                    $department['son'] = $this->GetDepartment($departments, $department->id, $returnIds);
                    $format[] = $department;
                }
            }
        }
        return $format;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 员工列表
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $name = $request->get('name');
        $max = Organization::combo($user->organization_id, true);

        $combo = [
            'max'  => $max,
            'user' => User::where(['organization_id' => $user->organization_id, 'is_boss' => 0])->count()
        ];

        # 获取部门
        $departments = Department::where([
            'organization_id' => $user->organization_id,
            'status'          => Department::DEPARTMENT_STATUS_OPEN
        ])->select(['id', 'pid', 'name'])->orderBy('id', 'asc')->get();

        $departmentIDs = [];
        $departmentOptions = Arr::tree($departments->toArray(), 'name', 'id', 'pid');
        if ($user->is_boss === 0) {
            $departmentIDs = array_merge($this->GetDepartment($departments, $user->department_id), [$user->department_id]);
            $departmentIDs = array_merge(array_sort($departmentIDs), []);
            $departmentOptions = Arr::tree($departments->keyBy('id')->only($departmentIDs)->toArray(), 'name', 'id', 'pid');
        }

        $datum = User::where([
            'organization_id' => $user->organization_id,
        ])
            ->where('is_boss', 0)
            ->where(function ($query) use ($departmentIDs) {
                if (!empty($departmentIDs)) {
                    $query->whereIn('department_id', $departmentIDs);
                }
            })
            ->where(function ($query) use ($name) {
                /** OR 条件筛选 姓名、手机号码、邮箱 */
                if (!empty($name)) {
                    $query->where('name', 'like', "%{$name}%")
                        ->orWhere('phone', 'like', "%{$name}%")
                        ->orWhere('email', 'like', "%{$name}%");
                }
            })
            ->whereIn('status', [1, 4])
            ->orderBy('is_boss', 'desc')
            ->orderBy('department_id', 'asc')
            ->paginate(User::PAGINATION_NUMBER);

        $datum->appends([
            'page' => $request->get('page', 1),
            'name' => $request->get('name'),
        ]);

        $posts = Post::where([
            'organization_id' => $user->organization_id,
        ])->orderBy('created_at', 'asc')->get();
        return view('manager.user.index', [
            'datum'             => $datum,
            'departments'       => $departments,
            'departmentOptions' => $departmentOptions,
            'combo'             => $combo,
            'posts'             => $posts
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 添加员工
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name'           => 'required',
            'telephone'      => 'required|regex:' . User::TELEPHONE_REGEX,
            'department'     => 'required|integer|min:0',
            'post'           => 'required|integer|min:0',
            'email'          => 'required|email|unique:users',
            'is_allow_login' => 'required|integer|in:0,1'
        ], [
            'name.required'       => '姓名必须填写',
            'telephone.required'  => '手机号码必须填写',
            'telephone.regex'     => '手机号码格式有误',
            'department.required' => '部门必须选择',
            'post.required'       => '职位必须选择',
            'email.required'      => '邮箱必须填写',
            'email.email'         => '邮箱的格式有误',
            'email.unique'        => '邮箱已有人注册过了'
        ]);

        $auth = Auth::user();

        # 管理层都有添加员工的权限

//        if ($auth->is_boss === 0) {
//            session()->flash('error', '无权限');
//            return back();
//        }

        $result = User::where(['phone' => $validatedData['telephone']])->first();

        if ($result) {
            return back()->with('error', '当前手机号已注册过了');
        }

        $max = Organization::combo($auth->organization_id, true);
        if ($max === false) {
            return back()->with('error', '未找到企业的套餐关系');
        }

        $users = User::where(['organization_id' => $auth->organization_id, 'is_boss' => 0])->get();
        if ($users->count() >= $max) {
            return back()->with('error', '当前企业套餐允许创建的员工数量已到上限，不能创建了');
        }

        $user = User::GenerateInitData([
            'name'            => $validatedData['name'],
            'organization_id' => $auth->organization_id,
            'phone'           => $validatedData['telephone'],
            'department_id'   => $validatedData['department'],
            'post_id'         => $validatedData['post'],
            'email'           => $validatedData['email'],
            'is_allow_login'  => $validatedData['is_allow_login']
        ]);
        if (!$user) {
            return back()->with('error', '添加失败');
        }
        return redirect()->route('userIndex', [
            'name' => $request->get('name'),
            'page' => $request->get('page', 1)
        ])->with('success', '添加成功');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 修改员工
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'update_id'      => 'required|integer|min:0',
            'name'           => 'required|string|min:2|max:10',
            'department'     => 'required|integer|min:0',
            'post'           => 'required|integer|min:0',
            'email'          => 'required|email',
            'is_allow_login' => 'required|integer|in:0,1'
        ], [
            'update_id.required'      => '索引必须填写',
            'update_id.integer'       => '索引必须一个整数',
            'update_id.min'           => '索引最小为0',
            'name.required'           => '姓名必须填写',
            'name.string'             => '姓名必须是一串字符',
            'name.min'                => '姓名至少2个字',
            'name.max'                => '姓名至多10个字',
            'department.required'     => '部门必须选择',
            'post.required'           => '职位必须选择',
            'email.required'          => '邮箱必须填写',
            'email.email'             => '邮箱的格式有误',
            'is_allow_login.required' => '是否允许登录必须选择',
            'is_allow_login.integer'  => '是否登录的值必须是整数',
            'is_allow_login.in'       => '是否登录的值不在有效范围内'
        ]);

        $auth = Auth::user();

//        if ($auth->is_boss === 0) {
//            session()->flash('error', '无权限');
//            return back();
//        }
        $user = User::find($validatedData['update_id']);

        # 管理层都有添加员工的权限
//        if (!$user) {
//            session()->flash('error', '用户不存在');
//            return back();
//        }

        if ($user->organization_id != $auth->organization_id) {
            session()->flash('error', '无权限');
            return back();
        }

        $page = $request->get('page', 1);

        /**
         *  --- 检查邮箱
         */
        $check = User::where(['email' => $validatedData['email']])->first();
        if ($check && $check->id != $validatedData['update_id']) {
            session()->flash('error', $validatedData['email'] . '已有人注册');
            return back();
        }

        $user->name = $validatedData['name'];
        $user->department_id = $validatedData['department'];
        $user->post_id = $validatedData['post'];
        $user->email = $validatedData['email'];
        $user->is_allow_login = $user->is_boss == 1 ? 1 : $validatedData['is_allow_login'];

        if (!$user->save()) {
            session()->flash('error', '保存失败');
            return back();
        }
        session()->flash('success', '保存成功');
        return redirect()->route('userIndex', ['page' => $page]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - Ajax检查手机号码
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function checkTelephone(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'telephone' => 'required|regex:' . User::TELEPHONE_REGEX,
            ], [
                'telephone.required' => '手机号码必须填写',
                'telephone.regex'    => '手机号码格式有误',
            ]);

            $user = User::where(['phone' => $validatedData['telephone']])->first();
            if ($user) {
                return ['code' => 400, 'message' => $validatedData['telephone'] . '手机号已注册过了'];
            }
            return ['code' => 200, 'message' => $validatedData['telephone'] . '手机号可以用'];
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return ['code' => 200, 'message' => $e->validator->errors()->first()];
            }

//            return ['code' => 400, 'message' => '发生错误'];
            return ['code' => 400, 'message' => $e->getMessage()];
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - Ajax检查邮箱
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function checkEmail(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'id'    => 'present|integer|min:0'
            ], [
                'email.required' => '邮箱必须传入',
                'email.email'    => '邮箱格式有误',
                'id.integer'     => '索引必须是一个整数',
                'id.min'         => '索引最小为0'
            ]);
            $email = User::where(['email' => $validatedData['email']])->first();
            if (!$email || (!empty($validatedData['id']) && $email->id == $validatedData['id'])) {
                return ['code' => 200, 'message' => $validatedData['email'] . '邮箱可用'];
            }
            return ['code' => 400, 'message' => $validatedData['email'] . '邮箱已有人注册'];
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return ['code' => 200, 'message' => $e->validator->errors()->first()];
            }
            return ['code' => 400, 'message' => '发生错误'];
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 待审核列表
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function unchecked(Request $request)
    {
        $user = Auth::user();
        $organization_id = $user->organization_id;

        $where = [
            ['status', '=', 2],
            ['is_boss', '=', 0],
        ];

        $name = $request->get('name');
        if (!empty($name)) {
            array_push($where, ['name', 'like', "%{$name}%"]);
        }

        $datum = User::where([
            'organization_id' => $organization_id,
        ])
            ->where($where)
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(User::PAGINATION_NUMBER);

        $datum->appends([
            'page' => $request->get('page', 1),
            'name' => $request->get('name'),
        ]);

        $departments = Department::where([
            'organization_id' => $organization_id,
            'status'          => Department::DEPARTMENT_STATUS_OPEN
        ])->orderBy('created_at', 'asc')->get();


        # 数据格式化
        $new_departments = [];
        foreach ($departments as $department) {
            if ($department->pid === 0) {
                $new_departments[$department->id] = [
                    'id'       => $department->id,
                    'name'     => $department->name,
                    'children' => []
                ];
                continue;
            }
            if (array_key_exists($department->pid, $new_departments)) {
                array_push($new_departments[$department->pid]['children'], [
                    'id'   => $department->id,
                    'name' => $department->name,
                ]);
            }
        }

        $posts = Post::where([
            'organization_id' => $organization_id,
        ])->orderBy('created_at', 'asc')->get();

        return view('manager.user.unchecked', ['datum' => $datum, 'departments' => $new_departments, 'posts' => $posts]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 用户申请审核动作
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function checked(Request $request)
    {
        $validatedData = $request->validate([
            'user_id'    => 'required|integer|min:0',
            'allow'      => 'required',
            'department' => 'required|integer|min:0',
            'post'       => 'required|integer|min:0'
        ]);

        $user = Auth::user();

        $result = User::find($validatedData['user_id']);

        if (!$result) {
            return back()->with('error', '您要操作的记录,不存在');
        }

        if ($result->status !== 2) {
            return back()->with('error', '记录已审核,请勿重复提交审核');
        }

        # 权限检查
        if ($user->organization_id != $result->organization_id) {
            return back()->with('error', '检查有恶意攻击,如频繁提交,该公司将列入黑名单中');
        }

        # Department检查
        $departments = Department::where([
            'organization_id' => $user->organization_id,
            'status'          => Department::DEPARTMENT_STATUS_OPEN
        ])->orderBy('created_at', 'asc')->get();

        if (!in_array($validatedData['department'], array_column($departments->toArray(), 'id'))) {
            return back()->with('error', '检查有恶意攻击,如频繁提交,该公司将列入黑名单中');
        }

        # POST检查
        $posts = Post::where([
            'organization_id' => $user->organization_id,
        ])->orderBy('created_at', 'asc')->get();

        if (!in_array($validatedData['post'], array_column($posts->toArray(), 'id'))) {
            return back()->with('error', '检查有恶意攻击,如频繁提交,该公司将列入黑名单中');
        }

        $result->status = $validatedData['allow'] == 'true' ? 1 : 3;
        $result->post_id = $validatedData['post'];
        $result->department_id = $validatedData['department'];
        if (!$result->save()) {
            return back()->with('error', '操作失败');
        }
        return response()->redirectToRoute('userUnChecked')->with('success', '操作成功');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 禁用动作Ajax
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function forbidden(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|integer|min:0',
            ]);
            $auth = Auth::user();
//            if ($auth->is_boss === 0) {
//                session()->flash('error', '无权限');
//                return back();
//            }
            $user = User::find($validatedData['user_id']);
            if (!$user) {
                return ['code' => 400, 'message' => '用户不存在'];
            }
            if (!in_array($user->status, [1, 4])) {
                return ['code' => 400, 'message' => '状态异常'];
            }
            # 权限检查
            if ($auth->organization_id != $auth->organization_id) {
                return ['code' => 400, 'message' => '无权限'];
            }
            $user->status = $user->status == 1 ? 4 : 1;
            if (!$user->save()) {
                return ['code' => 400, 'message' => '操作失败'];
            }
            return ['code' => 200, 'message' => '操作成功'];
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return ['code' => 200, 'message' => $e->validator->errors()->first()];
            }
            return ['code' => 400, 'message' => '发生错误'];
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 设置管理员动作Ajax
     *
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function setManage(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|integer|min:0',
            ], [
                'user_id.required' => '索引不能为空',
                'user_id.integer'  => '索引必须是一个整数',
                'user_id.min'      => '索引最小为0'
            ]);
            $auth = Auth::user();
//            if ($auth->is_boss === 0) {
//                return ['code' => 400, 'message' => '无权限'];
//            }

            $user = User::find($validatedData['user_id']);
            if (!$user) {
                return ['code' => 400, 'message' => '用户不存在'];
            }

            $users = User::findUserByOrganization($auth->organization_id);
            if (!in_array($validatedData['user_id'], array_column($users->toArray(), 'id'))) {
                return ['code' => 400, 'message' => '无权限'];
            }

            # 设置管理员
            $setUser = User::find($validatedData['user_id']);
            $setUser->is_allow_login = $user->is_allow_login == 1 ? 0 : 1;
            if (!$setUser->save()) {
                return ['code' => 400, 'message' => '设置失败'];
            }
            return ['code' => 200, 'message' => '设置成功'];
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return ['code' => 400, 'message' => $e->validator->errors()->first()];
            }
            return ['code' => 400, 'message' => '发生错误'];
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 离职处理动作Ajax
     *
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function separation(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|min:0',
        ]);
        $user = Auth::user();
        if ($user->is_boss === 0) {
            return ['code' => 400, 'message' => '无权限'];
        }
        # 权限验证 2
        # 获取当前公司下的所有员工
        $cache_users_key = "users_organization:" . $user->organization_id;
        $organization = $user->organization;
        if (!cache()->has($cache_users_key)) {
            $users = $organization->users;
            cache()->put($cache_users_key, $users, 5);
        }
        $users = cache()->get($cache_users_key);
        $user_ids = array_column($users->toArray(), 'id');
        if (!in_array($validatedData['user_id'], $user_ids)) {
            return ['code' => 400, 'message' => '要操作的员工不是您公司下的'];
        }

        # 离职处理
        $separationUser = User::find($validatedData['user_id']);
        $separationUser->name = $separationUser->name . '[已离职][' . $organization->name . ']';
        $separationUser->phone = '0-' . $separationUser->phone;
        $separationUser->status = -1;
        $separationUser->organization_id = 0;
        $separationUser->department_id = 0;
        $separationUser->post_id = 0;
        if (!$separationUser->save()) {
            return ['code' => 400, 'message' => '处理失败'];
        }
        return ['code' => 200, 'message' => '处理成功'];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 删除用户申请记录
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|min:0',
        ]);
        $user = Auth::user();
        $result = User::find($validatedData['user_id']);
        if (!$result) {
            return ['code' => 400, 'message' => '您要操作的资源不存在'];
        }

        if (!in_array($result->status, [4])) {
            return ['code' => 400, 'message' => '状态异常'];
        }

        # 权限检查
        if ($user->organization_id != $result->organization_id) {
            return ['code' => 400, 'message' => '检查有恶意攻击,如频繁提交,该公司将列入黑名单中'];
        }

        if (!$result->delete()) {
            return ['code' => 400, 'message' => '删除失败'];
        }
        session()->flash('success', '删除成功');
        return ['code' => 200, 'message' => '删除成功'];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 解除禁用动作Ajax
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function relieve(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|min:0',
        ]);

        $user = Auth::user();

        $result = User::find($validatedData['user_id']);

        if (!$result) {
            return ['code' => 400, 'message' => '您要操作的资源不存在'];
        }

        if (!in_array($result->status, [4])) {
            return ['code' => 400, 'message' => '状态异常'];
        }

        # 权限检查
        if ($user->organization_id != $result->organization_id) {
            return ['code' => 400, 'message' => '检查有恶意攻击,如频繁提交,该公司将列入黑名单中'];
        }

        $result->status = 1;
        if (!$result->save()) {
            return ['code' => 400, 'message' => '解除失败'];
        }
        session()->flash('success', '解除成功');
        return ['code' => 200, 'message' => '解除成功'];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 批量导入
     *
     *
     * @param Request $request
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function import(Request $request)
    {
        # 直接导入到数组里面
        $collection = Excel::toArray(null, $request->file('excel'));
        # 读取sheet1表格里面的数据 [0]：文件头 [1]：标题
        unset($collection[0][0], $collection[0][1], $collection[0][2]);
        # 重置索引
        sort($collection[0]);
        $datum = $collection[0];

        # 过滤无效数据
        foreach ($datum as $k => $item) {
            $item[0] = trim($item[0]);
            if (empty($item[0])) {
                unset($datum[$k]);
            }
        }

        $auth = Auth::user();
        $organization = $auth->organization;

        $max = Organization::combo($organization->id, true);
        if ($max === false) {
            return back()->with('error', '未找到企业的套餐关系');
        }

        # 查询当前公司有多少员工，不包括老板
        # 为防止缓存产生创建员工超出套餐限制数量 这里查询员工不加缓存
        $users = User::where(['organization_id' => $organization->id, 'is_boss' => 0])->get();
        if ($users->count() >= $max) {
            return back()->with('error', '当前企业套餐允许创建的员工数量已到上限，不能进行导入了');
        }

        if (($users->count() + count($datum)) > $max) {
            return back()->with('error', '当前要导入的员工数已经超过：' . (($users->count() + count($datum)) - $max) . '人');
        }
        $regex = User::TELEPHONE_REGEX;
        DB::beginTransaction();
        try {
            foreach ($datum as $k => $data) {

                # 去除空格处理
                $data[0] = trim($data[0]);
                $data[1] = trim($data[1]);
                $data[2] = trim($data[2]);
                $data[3] = trim($data[3]);

                /**
                 * 这里判断当前用户姓名如果为空 则跳出当前循环
                 */
                if (empty($data[0])) {
                    continue;
                }

                /**
                 * 用户检查
                 */
                if (!preg_match($regex, $data[1])) {
                    throw new \Exception($data[0] . '的手机号码格式有误，请重新导入');
                }
                /**
                 * 这里应该使用redis set数据类型的缓存存储所有用户的手机号码 减少数据库压力
                 */
                $user = User::where(['phone' => $data[1]])->first();
                if ($user) {
                    throw new \Exception($data[0] . '的手机号码' . $data[1] . '已有人注册，请重新导入');
                }
                $data[3] = (int)$data[3];
                if (!in_array($data[3], [0, 1])) {
                    throw new \Exception($data[0] . '的是否设置为管理员格式有误，请重新导入');
                }

                /**
                 * ---- 检查邮箱是否已注册过
                 */

                if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $data[2])) {
                    throw new \Exception($data[0] . '的邮箱' . $data[2] . '格式有误');
                }

                $email = User::where(['email' => $data[2]])->first();
                if ($email) {
                    throw new \Exception($data[0] . '的邮箱' . $data[2] . '已有人注册，请重新导入');
                }
                $user = User::GenerateInitData([
                    'name'            => $data[0],
                    'phone'           => $data[1],
                    'email'           => $data[2],
                    'is_allow_login'  => $data[3],
                    'organization_id' => $organization->id,
                ]);
                if (!$user) {
                    throw new \Exception($data[0] . '的数据保存失败，请重新导入');
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('userIndex')->with('success', '导入成功');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 下载批量导入模板
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function download()
    {
        return response()->download(storage_path('app/excel/批量导入模板2018-10-27.xlsx'));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 鹰眼销售轨迹
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function eagle(Request $request)
    {
        $auth = Auth::user();
        $date = $request->get('date', date('Y-m-d'));
        $entity_name = $request->get('entity_name');
        $employees = User::findUserByOrganization($auth->organization_id);
        if (strtotime($date) === false) {
            return back()->with('error', '无效日期');
        }

        $parse = [];
        $user = [];
        $data = [
            'out'      => [],
            'leave'    => [],
            'evection' => [],
            'visit'    => [],
        ];

        if ($date && $entity_name != null) {
            $parse = $this->GetEagle($entity_name, $date);
            $user = User::where(['api_token' => $entity_name])->first();
            if (is_string($parse)) {
                session()->flash('error', $parse);
            } else {
                session()->forget('error');
            }

//            $date = "2018-11-28";

            $begin = $date . " 00:00:00";
            $end = $date . " 23:59:59";

            # 查询外出数据
            $data_key_track_out = 'track_out_date:' . $date;
            if (!cache()->has($data_key_track_out)) {
                $data = Out::where([
                    'user_id'         => $user->id,
                    'organization_id' => $user->organization_id,
                    'status'          => Out::OUT_STATUS_FINISH
                ])
                    ->select(['begin_time', 'end_time', 'total as day', 'remark'])
                    ->whereBetween('created_at', [$begin, $end])->get();
                $data->each(function ($item) {
                    $item->begin_time = date('m-d H:i', $item->begin_time);
                    $item->end_time = date('m-d H:i', $item->end_time);
                    return $item;
                });
                cache()->put($data_key_track_out, $data->toArray(), 15);
            }

            # 查询请假数据
            $data_key_track_leave = 'track_leave_date:' . $date;
            if (!cache()->has($data_key_track_leave)) {
                $data = Leave::where([
                    'user_id'         => $user->id,
                    'organization_id' => $user->organization_id,
                    'status'          => Leave::LEAVE_STATUS_FINISH
                ])
                    ->select('begin_time', 'end_time', 'leave_status', 'leave_day as day', 'cause')
                    ->whereBetween('created_at', [$begin, $end])->get();
                $data->each(function ($item) {
                    $item->begin_time = date('Y-m-d H:i', $item->begin_time);
                    $item->end_time = date('Y-m-d H:i', $item->end_time);
                    $item->leave_status = $item->leaveStatus->title;
                    unset($item->leaveStatus);
                    return $item;
                });
                cache()->put($data_key_track_leave, $data->toArray(), 15);
            }

            # 查询出差数据
            $data_key_track_evection = 'track_evection_date:' . $date;
            if (!cache()->has($data_key_track_evection)) {
                $data = Evection::where([
                    'user_id'         => $user->id,
                    'organization_id' => $user->organization_id,
                    'status'          => Evection::EVENCTION_STATUS_FINISH
                ])
                    ->select('begin_time', 'end_time', 'evection_day as day', 'address', 'cause')
                    ->whereBetween('created_at', [$begin, $end])->get();

                $data->each(function ($item) {
                    $item->begin_time = date('Y-m-d H:i', $item->begin_time);
                    $item->end_time = date('Y-m-d H:i', $item->end_time);
                    return $item;
                });
                cache()->put($data_key_track_evection, $data->toArray(), 15);
            }

            # 查询拜访数据
            $data_key_track_visit = 'track_visit_date:' . $date;
            if (!cache()->has($data_key_track_visit)) {
                $data = Visit::where([
                    'visits.user_id'         => $user->id,
                    'visits.organization_id' => $user->organization_id,
                    'visits.status'          => 1
                ])
                    ->join('visit_types', 'visit_types.id', '=', 'visits.type')
                    ->select('visits.customer_id', 'visits.address', 'visit_types.name as type')
                    ->whereBetween('visits.access_at', [$begin, $end])
                    ->get();
                $data->each(function ($item) {
                    $customer = $item->customer_has_one;
                    $item['customer_name'] = $customer->customer_name;
                    unset($item->customer_has_one);
                    return $item;
                });
                cache()->put($data_key_track_visit, $data->toArray(), 15);
            }
            # 合并数据
            $data = [
                'out'      => cache()->get($data_key_track_out),
                'leave'    => cache()->get($data_key_track_leave),
                'evection' => cache()->get($data_key_track_evection),
                'visit'    => cache()->get($data_key_track_visit),
            ];
        } else {
            session()->flash('error', '需要选择一个用户');
        }

        return view('manager.user.eagle', [
            'points'    => $parse,
            'employees' => $employees,
            'user'      => $user,
            'data'      => $data
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 员工管理 - 获取鹰眼数据
     *
     * @param null    $entity_name
     * @param null    $date
     *
     * @throws \Exception
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function GetEagle($entity_name = null, $date = null)
    {
        $key = "eagle_entity_name:" . $entity_name . "_date:" . $date;
        $parse = $this->eagleRequest(['entity_name' => $entity_name], $date);
        if ($parse['code'] !== 200) {
            return $parse['message'];
        }
        if (!cache()->has($key)) {
            # 单页数据
            if ($parse['size'] == $parse['total']) {
                cache()->put($key, $parse, 15);
            }
            # 多页数据
            if ($parse['size'] < $parse['total']) {
                $page = ceil($parse['total'] / $parse['size']);
                for ($i = 2; $i <= $page; $i++) {
                    $result = $this->eagleRequest(['entity_name' => $entity_name, 'page_index' => $i], $date);
                    if ($result['code'] == 200 && count($result['data']) > 0) {
                        foreach ($result['data'] as $item) {
                            array_push($parse['data'], $item);
                        }
                    }
                }
                cache()->put($key, $parse, 15);
            }
        }
        return cache()->get($key);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * 私有方法 - 请求WEB接口百度鹰眼数据
     *
     * @param array  $query
     * @param string $date
     *
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     */
    private function eagleRequest($query = [], $date = '')
    {
        $begin_time = strtotime($date . ' 00:00:00');
        $end_time = strtotime($date . ' 23:59:59');
        $query = array_merge([
            "ak"          => "WUjKahY6fNrKYFcs7r92wqLpCIDL1jH5",
            "service_id"  => "206119",
            "entity_name" => null,
            "start_time"  => $begin_time,
            "end_time"    => $end_time,
            "page_size"   => 500,
            "page_index"  => 1
        ], $query);
        $url = "http://yingyan.baidu.com/api/v3/track/gettrack?" . http_build_query($query);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        if ($data === false) {
            return ['code' => 500, 'message' => '请求失败'];
        }
        $parse = json_decode($data, true);
        if ($parse['status'] != 0 && $parse['status'] == 3003) {
            return ['code' => 400, 'message' => "没有轨迹的数据"];
        }
        if (isset($parse['points']) && count($parse['points']) == 0) {
            return ['code' => 400, 'message' => '没有轨迹点', 'data' => [], 'total' => 0, 'size' => $parse['size']];
        }
        return ['code' => 200, 'message' => 'ok', 'total' => $parse['total'], 'size' => $parse['size'], 'data' => $parse['points']];
    }

}
