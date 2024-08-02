<?php


namespace App\Http\Controllers\Customer;


use App\Helper\CustomController;
use App\Models\Product;

class ProductController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findByID($id)
    {
        try {
            $product = Product::with(['merchant.merchant'])
                ->where('id', '=', $id)
                ->first();
            if (!$product) {
                return $this->jsonNotFoundResponse('merchant not found');
            }
            return $this->jsonSuccessResponse('success', $product);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
