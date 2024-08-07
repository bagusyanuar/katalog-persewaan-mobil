<?php


namespace App\Http\Controllers\Customer;


use App\Helper\CustomController;
use App\Models\Cart;
use App\Models\Driver;
use App\Models\Product;
use App\Models\Rent;
use App\Models\RentDriver;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            $data = Cart::with(['product.merchant'])
                ->whereNull('rent_id')
                ->where('user_id', '=', auth()->id())
                ->orderBy('created_at', 'DESC')
                ->get();

            $drivers = [];
            if (count($data) > 0) {
                $cart = $data->first();
                $merchant = $cart->product->merchant;
                $merchantID = $merchant->id;
                $drivers = Driver::with([])
                    ->where('merchant_id', '=', $merchantID)
                    ->get();
            }
            return $this->jsonSuccessResponse('success', [
                'carts' => $data,
                'drivers' => $drivers
            ]);
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

    public function checkout()
    {
        try {
            DB::beginTransaction();
            $userID = auth()->id();
            $body = json_decode(request()->get('data'), true);
            $carts = Cart::with(['product'])
                ->where('user_id', '=', $userID)
                ->whereNull('rent_id')
                ->get();

            if (count($carts) <= 0) {
                return $this->jsonBadRequestResponse('no cart found');
            }

            $merchantID = $carts->first()->product->merchant_id;
            $returnDate = $body['date_return'];
            $rentDay = $body['rent_day'];
            $driversRequest = $body['driver'];

            $drivers = Driver::with([])
                ->whereIn('id', $driversRequest)
                ->get();
            $totalDriver = 0;
            foreach ($drivers as $driver) {
                $totalDriver += $driver->price;
            }

            $subTotal = 0;
            foreach ($carts as $cart) {
                $subTotal += $cart->price;
            }

            $total = ($subTotal + $totalDriver) * $rentDay;

            $rent_request = [
                'user_id' => $userID,
                'merchant_id' => $merchantID,
                'reference_number' => 'rent-'.date('YmdHis'),
                'total' => $total,
                'date_rent' => Carbon::now(),
                'date_return' => $returnDate,
                'status' => 0
            ];

            $rent = Rent::create($rent_request);

            foreach ($drivers as $driver) {
                $rent_driver_request = [
                    'rent_id' => $rent->id,
                    'driver_id' => $driver->id,
                    'price' => $driver->price
                ];

                RentDriver::create($rent_driver_request);
            }

            foreach ($carts as $cart) {
                $cart->update([
                    'rent_id' => $rent->id
                ]);
            }
            DB::commit();

            return $this->jsonSuccessResponse('success', [
                'id' => $rent->id
            ]);
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
