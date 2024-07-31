<?php


namespace App\Http\Controllers\Merchant;


use App\Helper\CustomController;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ProductController extends CustomController
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
            $products = Product::with([])
                ->where('merchant_id', '=', auth()->id())
                ->get();
            return $this->jsonSuccessResponse('success', $products);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function findByID($id)
    {
        try {
            $product = Product::with([])
                ->where('id','=', $id)
                ->first();
            if (!$product) {
                return $this->jsonNotFoundResponse('product not found');
            }

            if ($this->request->method() === 'POST') {
                return $this->patch($product);
            }
            return $this->jsonSuccessResponse('success', $product);
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function store()
    {
        try {
            $merchantID = auth()->id();
            $vehicleNumber = $this->postField('vehicle_number');
            $name = $this->postField('name');
            $price = $this->postField('price');
            $description = $this->postField('description');

            $data_request = [
                'merchant_id' => $merchantID,
                'vehicle_number' => $vehicleNumber,
                'name' => $name,
                'price' => $price,
                'description' => $description
            ];

            if ($this->request->hasFile('image')) {
                $file = $this->request->file('image');
                $extension = $file->getClientOriginalExtension();
                $document = Uuid::uuid4()->toString() . '.' . $extension;
                $storage_path = public_path('assets/products');
                $documentName = $storage_path . '/' . $document;
                $data_request['image'] = '/assets/products/' . $document;
                $file->move($storage_path, $documentName);
            }

            Product::create($data_request);
            return $this->jsonSuccessResponse('success');
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    /**
     * @param Model $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function patch($data)
    {
        try {
            $merchantID = auth()->id();
            $vehicleNumber = $this->postField('vehicle_number');
            $name = $this->postField('name');
            $price = $this->postField('price');
            $description = $this->postField('description');

            $data_request = [
                'merchant_id' => $merchantID,
                'vehicle_number' => $vehicleNumber,
                'name' => $name,
                'price' => $price,
                'description' => $description
            ];


            if ($this->request->hasFile('image')) {
                $file = $this->request->file('image');
                $extension = $file->getClientOriginalExtension();
                $document = Uuid::uuid4()->toString() . '.' . $extension;
                $storage_path = public_path('assets/products');
                $documentName = $storage_path . '/' . $document;
                $data_request['image'] = '/assets/products/' . $document;
                $file->move($storage_path, $documentName);
            }

            $data->update($data_request);
            return $this->jsonSuccessResponse('success');
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
