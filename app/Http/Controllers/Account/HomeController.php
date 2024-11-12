<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminService as AdminAdminService;
use App\Services\Account\Admin\AdminService;
use App\Services\Account\Agency\AgencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\UserService;
use App\Services\Account\AgentService;
use App\Services\Account\DecoderService;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        if(Auth::user()->role=='agent'){

            return AgentService::index();
        }
        elseif(Auth::user()->role=='organization'){
            
            return AgencyService::index();
        }
        elseif(Auth::user()->role=='decoder'){
            return DecoderService::index();
        }

        elseif(Auth::user()->role=='admin'){
            return AdminAdminService::index();
        }
        return UserService::index();
    }
}
