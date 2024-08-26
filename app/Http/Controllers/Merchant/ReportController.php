<?php


namespace App\Http\Controllers\Merchant;


use App\Helper\CustomController;
use App\Models\Rent;

class ReportController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $start = $this->field('start');
            $end = $this->field('end');
            $data = Rent::with(['rent_driver.driver', 'user_merchant.merchant', 'user.customer', 'payment'])
                ->where('merchant_id', '=', auth()->id())
                ->whereBetween('date_rent',[$start, $end])
                ->orderBy('created_at', 'ASC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
