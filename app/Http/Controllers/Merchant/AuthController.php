<?php


namespace App\Http\Controllers\Merchant;


use App\Helper\CustomController;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function register()
    {
        try {
            DB::beginTransaction();
            $email = $this->postField('email');
            $username = $this->postField('username');
            $password = $this->postField('password');
            $role = 'merchant';
            $name = $this->postField('name');
            $phone = $this->postField('phone');
            $address = $this->postField('address');
            $latitude = $this->postField('latitude');
            $longitude = $this->postField('longitude');

            $data_user = [
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => $role
            ];

            $user = User::create($data_user);
            $data_merchant = [
                'user_id' => $user->id,
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];

            Merchant::create($data_merchant);
            $token = auth('api')->setTTL(null)->tokenById($user->id);
            DB::commit();
            return $this->jsonSuccessResponse('success', [
                'access_token' => $token
            ]);
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function login()
    {
        try {
            $username = $this->postField('username');
            $password = $this->postField('password');

            $user = User::with([])
                ->where('username', '=', $username)
                ->first();
            if (!$user) {
                return $this->jsonNotFoundResponse('user not found!');
            }

            $isPasswordValid = Hash::check($password, $user->password);
            if (!$isPasswordValid) {
                return $this->jsonUnauthorizedResponse('username and password did not match...');
            }

            $token = auth('api')->setTTL(null)->tokenById($user->id);
            return $this->jsonSuccessResponse('success', [
                'access_token' => $token
            ]);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
