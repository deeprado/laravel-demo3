<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Email
{
    public static $error;

    public static function send($to = '', $code = 0)
    {
        try {
            Mail::send('email.send', ['code' => $code], function ($message) use ($to) {
                $message->to($to)->subject('修改邮箱验证码');
            });
            # 添加缓存
            cache()->put($to, $code, 15);
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public static function check($to = '', $inputCode = 0)
    {
        try {
            if (!cache()->has($to)) {
                self::$error = '验证码已过期';
                return false;
            }

            if (cache()->get($to) != $inputCode) {
                self::$error = '验证码错误';
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public static function destroy($to){
        try{
            if(cache()->has($to)){
                cache()->forget($to);
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }
}