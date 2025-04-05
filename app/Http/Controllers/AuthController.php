<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
use App\Models\BrowserLog;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|string|email',
            "password" => "required|string"
        ]);
        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), "Validation Error", 400);
        }

        $user = User::where("email", $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponseClass::sendResponse(null, "Email or Password is not correct.", 404);
        }

        if ($user->is_disable == 1) {
            return ApiResponseClass::sendResponse(null, "Your account has been blocked. Please contact admin for more detail.", 300);
        }
        $user->last_login = now();
        $user->save();

        $this->logUserBrowser($request);

        $token = $user->createToken("authToken")->plainTextToken;

        // return ApiResponseClass::sendResponse($user, "Login is successful.", 200);
        return ApiResponseClass::sendResponse([
            "user" => $this->formatCamelCase($user),
            "token" => $token
        ], "login successful", 200);
    }

    //Get Last Logged In User
    public function getLastLoggedInUser() {
        $user = User::whereNotNull('last_login')
            ->orderBy('last_login', 'desc')
            ->first();

        \Log::info('Last logged-in user', ['user' => $user]);

        if (!$user) {
            return ApiResponseClass::sendResponse(null, 'No login data found', 404);
        }

        return ApiResponseClass::sendResponse([
            'id' => $user->id,
            'userName' => $user->user_name,
            'email' => $user->email,
            'lastLogin' => $user->last_login
        ], 'Last logged-in user fetched successfully', 200);
    }

    public function logUserBrowser(Request $request){
        $userAgent = $request->header('User-Agent') ?? 'Unknown';
        \Log::info('User-Agent:', ['userAgent' => $userAgent]);

        $browser = $this->detectBrowser($userAgent);

        if ($browser === 'Unknown') {
            return response()->json([
                'message' => 'Non-browser request ignored.',
                'browser' => $browser,
            ]);
        }

        BrowserLog::create([
            'browser' => $browser,
        ]);

        return response()->json([
            'message' => 'Browser logged successfully!',
            'browser' => $browser,
        ]);
    }
    public function browserList(){

        $logs = DB::table('browser_logs')
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->where('browser', '!=', 'Unknown') // Exclude Unknown
            ->groupBy('browser')
            ->orderByDesc('count')
            ->get();

        return response()->json([
            'message' => 'Unique logged browsers fetched successfully.',
            'logs' => $logs,
        ]);
    }
    private function detectBrowser($userAgent){
        $agent = new Agent();
        $agent->setUserAgent($userAgent); 

        if ($agent->isChrome()) {
            return 'Chrome';
        } elseif ($agent->isFirefox()) {
            return 'Firefox';
        } elseif ($agent->isEdge()) {
            return 'Edge';
        } elseif ($agent->isSafari()) {
            return 'Safari';
        } elseif ($agent->isOpera()) {
            return 'Opera';
        } else {
            return 'Unknown';
        }
     }
    public function logout(Request $request){
        $user = $request->user();
        if(!$user){
            return ApiResponseClass::sendResponse(null,"Access Denied! There is no token or invalid token");
        }
        // revoke exact, single user's token
        // $request->user()->currentAccessToken()->delete();

        // revoke all related user's token
        $user->tokens()->delete();

        return ApiResponseClass::sendResponse(null,"Log out successful");

    }

    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'userName' => $obj->user_name,
            'email' => $obj->email,
            'phoneNo' => $obj->phone_no,
            'roleId' => $obj->role_id,
            'departmentId' => $obj->department_id,
            'remark' => $obj->remark,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
