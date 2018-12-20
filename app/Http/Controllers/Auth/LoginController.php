<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Gee;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     * @var string
     */
    protected $redirectTo = '/manager';

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'phone';
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => ['required','regex:/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/'],
            'password' => 'required|string',
        ],[
            $this->username().'.required' =>'登录的手机号码不能为空',
            $this->username().'.regex' =>'无效号码',
        ]);
    }

    protected $gee;

    # 重写login  追加用户允许登陆字段、用户状态字段
    protected function attemptLogin(Request $request)
    {
        $request->offsetSet('is_allow_login', 1);
        $request->offsetSet('status', 1);

        if (!$this->gee instanceof Gee) {
            $this->gee = new Gee;
        }

        $data = [
            #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "client_type" => "web",
            # 请在此处传输用户请求验证时所携带的IP
            "ip_address" => $request->getClientIp()
        ];

        if (session()->has('gtserver') && session()->get('gtserver') == 1) {   //服务器正常
            $result = $this->gee->success_validate(
                $request->get('geetest_challenge'),
                $request->get('geetest_validate'),
                $request->get('geetest_seccode'),
                $data
            );
            if ($result === false) {
                $this->sendFailedLoginResponse($request,'图形验证失败');
                die;
            }
        }else{
            if ($this->gee->fail_validate(
                $request->get('geetest_challenge'),
                $request->get('geetest_validate'),
                $request->get('geetest_seccode')
            ) === false) {
                $this->sendFailedLoginResponse($request,'图形验证失败');
                die;
            }
        }

        return $this->guard()->attempt(
            array_merge($this->credentials($request), [
                    'is_allow_login' => $request->get('is_allow_login'),
                    'status'         => $request->get('status')
                ]
            ), $request->filled('remember')
        );
    }

    protected function sendFailedLoginResponse(Request $request,$error = '手机号码或登录密码错误')
    {
        throw ValidationException::withMessages([
            $this->username() => $error,
        ]);
    }

}
