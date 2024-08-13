<?php


namespace App\Http\Controllers\Admin;


use App\Helper\CustomController;
use App\Models\Merchant;

class MerchantController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        try {
            $data = Merchant::with(['user'])
                ->orderBy('created_at', 'DESC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
