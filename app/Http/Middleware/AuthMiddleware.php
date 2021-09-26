<?php

namespace App\Http\Middleware;

use Closure;
use App\SSO\AuthRequest;
use App\User;
use Auth;
use App\DatabaseConnection;
use App\Role;
use DB;

class AuthMiddleware extends AuthRequest
{

    public function handle($request, Closure $next)
    {
        $this->authenticate();

        if (!isset($_SESSION['isUserLogin']) || !Auth::check()) {
            Auth::guard()->logout();
            session()->invalidate();

            $user = (array) $_SESSION['authUser'];

            
            
            $auth_user = User::where('flag', 1)
                ->where('sso_id', $user['ssoId'])
                ->first();

            // print_r(json_encode($user));exit;
            if (empty($auth_user)) {
                return response()->view('errors.noaccess');
            }

            #get role information
            if (empty($user['role'])) {
                return response()->view('errors.noaccess');
            }

            

            $role = DB::table('rbac_role')
                ->whereIn('role_id', $user['role'])
                ->select(DB::raw('role_id as brokerrole_id, role_name as nama_role, program_id, prodi_id, fakultas_id, jenisuser_sso'))
                ->get();
            // print_r(json_encode($roles));exit;

            session()->put('role', $role);
            session()->put('currentRole', $role->first());
            // print_r(json_encode(session()->get('currentRole')));exit;
            #

            Auth::loginUsingId($auth_user->user_id);
            $_SESSION['isUserLogin'] = 1;

        }
        return $next($request);
    }
}