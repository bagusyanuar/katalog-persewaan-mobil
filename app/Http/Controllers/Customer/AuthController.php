<?php


namespace App\Http\Controllers\Customer;


use App\Helper\CustomController;
use App\Models\Customer;
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
            $role = 'customer';
            $name = $this->postField('name');
            $phone = $this->postField('phone');
            $address = $this->postField('address');

            $data_user = [
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => $role
            ];

            $user = User::create($data_user);
            $data_customer = [
                'user_id' => $user->id,
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
            ];

            Customer::create($data_customer);
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
            $email = $this->postField('email');
            $password = $this->postField('password');

            $user = User::with([])
                ->where('email', '=', $email)
                ->where('role', '=', 'customer')
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
                'access_token' => $token,
                'email' => $email,
                'username' => $user->username
            ]);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
