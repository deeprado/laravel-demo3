<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/28 0028
 * Time: 上午 9:10
 */
namespace App\Http\Controllers\Auth;

use App\Helpers\Gee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeeController extends Controller
{
    protected $gee;

    public function init(Request $request)
    {
        if (!$this->gee instanceof Gee) {
            $this->gee = new Gee;
        }
        $data = [
            #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "client_type" => "web",
            # 请在此处传输用户请求验证时所携带的IP
            "ip_address" => $request->getClientIp()
        ];
        $status = $this->gee->pre_process($data, 1);
        session(['gtserver'=>$status]);
        return $this->gee->get_response();
    }
}