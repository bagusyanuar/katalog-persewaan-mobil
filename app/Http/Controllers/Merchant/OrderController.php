<?php


namespace App\Http\Controllers\Merchant;


use App\Helper\CustomController;
use App\Models\Rent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $status = $this->field('status');
            $data = Rent::with(['rent_driver.driver', 'user_merchant.merchant', 'user.customer', 'payment'])
                ->where('merchant_id', '=', auth()->id())
                ->where('status', '=', $status)
                ->orderBy('created_at', 'ASC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function findByID($id)
    {
        try {
            $data = Rent::with(['rent_driver.driver', 'user_merchant.merchant', 'user.customer', 'carts.product'])
                ->where('merchant_id', '=', auth()->id())
                ->where('id', '=', $id)
                ->first();
            if (!$data) {
                return $this->jsonNotFoundResponse('data not found');
            }

            if ($this->request->method() === 'POST') {
                return $this->confirm_status($data);
            }
            return $this->jsonSuccessResponse('success', $data);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }


    /**
     * @param Model $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function confirm_status($data)
    {
        try {
            DB::beginTransaction();
            $statusConfirm = $this->postField('status');
            $statusRent = $data->status;

            $data_request = [];
            if ($statusRent === 1) {
                /** @var Model $payment */
                $payment = $data->payment;
                $data_request['status']= 5;
                $data_payment = [
                    'status' => 2,
                    'reason' => $this->postField('reason')
                ];
                if ($statusConfirm === 'accept') {
                    $data_request['status'] = 2;
                    $data_payment['status'] = 1;
                    $data_payment['reason'] = '-';
                }

                $payment->update($data_payment);
                $data->update($data_request);
                DB::commit();
            }
            if ($statusRent === 2) {
                $data_request = [
                    'status' => 3
                ];
                $data->update($data_request);
                DB::commit();
            }

            if ($statusRent === 3) {
                $data_request = [
                    'status' => 4
                ];
                $data->update($data_request);
                DB::commit();
            }

            return $this->jsonSuccessResponse('success', [$statusConfirm]);
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
