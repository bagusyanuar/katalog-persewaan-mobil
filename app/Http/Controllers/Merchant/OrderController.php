<?php


namespace App\Http\Controllers\Merchant;


use App\Helper\CustomController;
use App\Models\Rent;

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
            $data = Rent::with(['rent_driver.driver', 'user_merchant.merchant', 'user.customer'])
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
            return $this->jsonSuccessResponse('success', $data);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
