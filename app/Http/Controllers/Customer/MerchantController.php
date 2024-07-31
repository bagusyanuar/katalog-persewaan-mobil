<?php


namespace App\Http\Controllers\Customer;


use App\Helper\CustomController;
use App\Models\Driver;
use App\Models\Merchant;
use App\Models\Product;

class MerchantController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $merchants = Merchant::with([])
                ->get();
            return $this->jsonSuccessResponse('success', $merchants);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function findByID($id)
    {
        try {
            $merchant = Merchant::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$merchant) {
                return $this->jsonNotFoundResponse('merchant not found');
            }
            return $this->jsonSuccessResponse('success', $merchant);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function productByMerchant($id)
    {
        try {
            $products = Product::with([])
                ->where('merchant_id', '=', $id)
                ->get();
            return $this->jsonSuccessResponse('success', $products);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function driverByMerchant($id)
    {
        try {
            $drivers = Driver::with([])
                ->where('merchant_id', '=', $id)
                ->get();
            return $this->jsonSuccessResponse('success', $drivers);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
