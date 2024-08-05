<?php


namespace App\Http\Controllers\Customer;


use App\Helper\CustomController;
use App\Models\Rent;

class RentController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $data = Rent::with([])
                ->get();
            return $this->jsonSuccessResponse('success', $data);

        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
