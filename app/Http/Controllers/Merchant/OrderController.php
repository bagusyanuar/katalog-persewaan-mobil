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
            $data = Rent::with(['product', 'rent_driver.driver', 'user_merchant.merchant'])
                ->where('merchant_id', '=', auth()->id())
                ->orderBy('created_at', 'ASC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
