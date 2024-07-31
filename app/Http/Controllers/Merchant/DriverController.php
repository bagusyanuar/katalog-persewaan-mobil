<?php


namespace App\Http\Controllers\Merchant;


use App\Helper\CustomController;
use App\Models\Driver;

class DriverController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->request->method() === 'POST') {
            return $this->store();
        }
        try {
            $drivers = Driver::with([])
                ->where('merchant_id', '=', auth()->id())
                ->get();
            return $this->jsonSuccessResponse('success', $drivers);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function findByID($id)
    {
        try {
            $driver = Driver::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$driver) {
                return $this->jsonNotFoundResponse('driver not found');
            }
            return $this->jsonSuccessResponse('success', $driver);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function store()
    {
        try {
            $merchantID = auth()->id();
            $name = $this->postField('name');
            $phone = $this->postField('phone');
            $price = $this->postField('price');

            $data_request = [
                'merchant_id' => $merchantID,
                'name' => $name,
                'phone' => $phone,
                'price' => $price
            ];
            Driver::create($data_request);
            return $this->jsonSuccessResponse('success');
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
