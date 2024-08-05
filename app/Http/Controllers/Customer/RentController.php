<?php


namespace App\Http\Controllers\Customer;


use App\Helper\CustomController;
use App\Models\Payment;
use App\Models\Rent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

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
                ->where('user_id', '=', auth()->id())
                ->orderBy('created_at', 'DESC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);

        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function getDataByID($id)
    {
        if ($this->request->method() === 'POST') {
            return $this->payment($id);
        }
        try {
            $data = Rent::with(['carts.product', 'rent_driver.driver'])
                ->where('id', '=', $id)
                ->first();

            if (!$data) {
                return $this->jsonNotFoundResponse('transaction not found');
            }
            return $this->jsonSuccessResponse('success', $data);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }


    private function payment($id)
    {
        try {
            DB::beginTransaction();
            $data = Rent::with([])
                ->where('id', '=', $id)
                ->first();

            if (!$data) {
                return $this->jsonNotFoundResponse('transaction not found');
            }

            $data->update([
                'status' => 1
            ]);

            $payment_request = [
                'rent_id' => $data->id,
                'account_name' => $this->postField('account_name'),
                'account_bank' => $this->postField('account_bank'),
                'status' => 0,
                'description' => '-'
            ];


            if ($this->request->hasFile('attachment')) {
                $file = $this->request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $document = Uuid::uuid4()->toString() . '.' . $extension;
                $storage_path = public_path('assets/transfer');
                $documentName = $storage_path . '/' . $document;
                $payment_request['attachment'] = '/assets/transfer/' . $document;
                $file->move($storage_path, $documentName);
            }
            Payment::create($payment_request);
            DB::commit();
            return $this->jsonSuccessResponse('success');
        }catch (\Exception $e) {
            DB::rollBack();
            return  $this->jsonErrorResponse($e->getMessage());
        }
    }
}
