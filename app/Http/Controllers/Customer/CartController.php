<?php


namespace App\Http\Controllers\Customer;


use App\Helper\CustomController;
use App\Models\Cart;
use App\Models\Product;

class CartController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->request->method() === 'POST') {
            return $this->addToCart();
        }
        try {
            $data = Cart::with([])
                ->whereNull('rent_id')
                ->where('user_id', '=', auth()->id())
                ->orderBy('created_at', 'DESC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function addToCart()
    {
        try {
            $productID = $this->postField('product_id');
            $product = Product::with([])
                ->where('id', '=', $productID)
                ->first();

            if (!$product) {
                return $this->jsonNotFoundResponse('product not found');
            }

            $price = $product->price;
            $data_request = [
                'user_id' => auth()->id(),
                'rent_id' => null,
                'product_id' => $productID,
                'price' => $price,
                'driver_price' => 0,
                'total' => 0
            ];
            Cart::create($data_request);
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
