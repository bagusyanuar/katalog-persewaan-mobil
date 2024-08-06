<?php


namespace App\Http\Controllers\Customer;


use App\Helper\CustomController;
use App\Models\User;
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
            $user = User::with(['customer'])
                ->where('id', '=', auth()->id())
                ->first();
            if (!$user) {
                return $this->jsonNotFoundResponse('user not found');
            }
            if ($this->request->method() === 'POST') {
                return $this->store($user);
            }

            return $this->jsonSuccessResponse('success', $user);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    /**
     * @param Model $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function store($data)
    {
        try {
            DB::beginTransaction();
            /** @var Model $profile */
            $profile = $data->customer;
            $user_request = [
                'username' => $this->postField('username'),
                'email' => $this->postField('email')
            ];

            if ($this->postField('password') !== '') {
                $user_request['password'] = Hash::make($this->postField('password'));
            }
            $data->update($user_request);

            $profile_request = [
                'name' => $this->postField('name'),
                'phone' => $this->postField('phone'),
                'address' => $this->postField('address'),
            ];

            $profile->update($profile_request);
            DB::commit();
            return  $this->jsonSuccessResponse('success');
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
