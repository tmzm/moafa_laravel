<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passport\Token;

class UserController extends Controller
{
    /**
     * @param CreateUserRequest $request
     */
    public function create(CreateUserRequest $request)
    {
        self::register_user($request);
    }

    /**
     * @param StoreUserRequest $request
     */
    public function store(StoreUserRequest $request)
    {
        self::login_user($request);
    }

    /**
     * @param UpdateUserRequest $request
     */
    public function update(UpdateUserRequest $request)
    {
        self::update_user($request);
    }

    /**
     * @param Request $request
     */
    public function edit(Request $request)
    {
        self::edit_fcm_token($request);
    }


    /**
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        self::logout_user($request);
    }

    /**
     * @param Request $request
     */
    public function show(Request $request)
    {
        self::show_user_details($request);
    }

    public function show_by_id($user_id)
    {
        $user = User::find($user_id);

        if($user)
            self::ok($user);

        self::notFound();
    }

    /**
     * @param Request $request
     */
    public function index()
    {
        self::ok([
            'users' => User::filter(request(['search','take','skip']))->get(),
            'count' => User::filter(request(['search']))->count()
        ]);
    }

    public function refresh_token(Request $request) {
        if($request->user()->tokenCan('user-refresh-token')){
            $tokens = Token::where('user_id', $request->user()->id)->get();

            foreach ($tokens as $token) {
                if (in_array('user-access-token', $token->scopes)) {
                    $token->revoke();
                }
            }

            $token = $request->user()->createToken('user_access_token',['user-access-token'])->accessToken;

            self::ok(null,['accessToken' => $token]);
        }

        self::unAuth();
    }

    public function delete(Request $request)
    {
        if($request->user()->delete())
            self::ok();

        self::unAuth();
    }

    public function delete_user($user_id)
    {
        $user = User::find($user_id);

        if($user){
            $user->delete();
            self::ok();
        }

        self::notFound();
    }

    public function unique($phone_number)
    {
        $user = User::firstWhere('phone_number',$phone_number);

        if($user){
            self::ok($user);
        }

        self::notFound();
    }
}
