<?php

namespace App\Manager\Controllers;

use App\Models\Order;
use App\Models\UserCustomer;
use App\User;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        return view('manager.home.index');
    }


}
