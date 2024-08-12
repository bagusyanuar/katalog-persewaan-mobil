<?php


namespace App\Http\Controllers\Merchant;


use App\Helper\CustomController;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $data = Merchant::with(['user'])
                ->where('user_id', '=', auth()->id())
                ->first();
            if (!$data) {
                return $this->jsonNotFoundResponse('profile not found');
            }

            if ($this->request->method() === 'POST') {
                return $this->store($data);
            }
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    /**
     * @param Model $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($profile)
    {
        try {
            DB::beginTransaction();
            $email = $this->postField('email');
            $username = $this->postField('username');
            $password = $this->postField('password');
            $name = $this->postField('name');
            $phone = $this->postField('phone');
            $address = $this->postField('address');
            $latitude = $this->postField('latitude');
            $longitude = $this->postField('longitude');

            /** @var Model $user */
            $user = $profile->user;

            $profile_request = [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];

            $profile->update($profile_request);

            $user_request = [
                'email' => $email,
                'username' => $username
            ];
            if ($password !== '') {
                $user_request['password'] = Hash::make($password);
            }
            $user->update($user_request);
            DB::commit();
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
