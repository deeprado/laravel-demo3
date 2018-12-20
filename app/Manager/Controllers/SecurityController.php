<?php

namespace App\Manager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SecurityController extends Controller
{

    public function password()
    {
        return view('manager.security.password');
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'password'                 => 'required',
            'newPassword'              => 'required|confirmed|regex:/^[a-zA-Z]\w{5,11}$/',
            'newPassword_confirmation' => 'required|same:newPassword'
        ], [
            'password.required'                 => '原密码不能为空',
            'newPassword.required'              => '新密码不能为空',
            'newPassword.confirmed'             => '两次密码不一致',
            'newPassword.regex'                 => '新密码须以字母开头，长度在6~12之间，只能包含字母、数字和下划线',
            'newPassword_confirmation.required' => '确认密码不能为空',
            'newPassword_confirmation.same'     => '确认密码与新密码不一致'
        ]);

        $user = Auth::user();

        # 检查是否是体验用户
        if($user->organization->is_test == 1){
            session()->flash('error','非常抱歉，体验用户不能修改密码');
            return back();
        }

        if(!Hash::check($validatedData['password'],$user->password)){
            session()->flash('error','原密码错误');
            return back();
        }

        $user ->password = Hash::make($validatedData['newPassword']);

        if(!$user->save()){
            session()->flash('error','密码保存失败');
            return back();
        }
        session()->flash('success','密码修改完成');
        return redirect()->route('security.password');
    }
}