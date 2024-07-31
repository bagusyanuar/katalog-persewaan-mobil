<?php


namespace App\Helper;


use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class CustomController extends Controller
{

    protected $model;

    protected $validationRules = [];

    protected $validationMessage = [];

    /** @var Request $request */
    protected $request;

    /**
     * CustomController constructor.
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }


    public function insert($class = null, $data = [])
    {
        $model = new $class();
        foreach ($data as $key => $d) {
            $model[$key] = $data[$key];
        }
        return $model->save() ? $model : false;
    }

    public function update($class = null, $data = [])
    {
        $id = $this->request->request->get('id');
        $model = $class::find($id);
        foreach ($data as $key => $d) {
            $model[$key] = $data[$key];
        }
        return $model->save() ? $model : false;
    }

    public function directInsert($class = null, $data = [])
    {
        $model = new $class();
        foreach ($data as $key => $d) {
            $model[$key] = $data[$key];
        }
        $model->save();
        return $model;
    }

    public function directUpdate($class = null, $id = 1, $data = [])
    {
        $model = $class::find($id);
        foreach ($data as $key => $d) {
            $model[$key] = $data[$key];
        }
        $model->save();
        return $model;
    }

    public function generateImageName($field = '')
    {
        $value = '';
        if ($this->request->hasFile($field)) {
            $files = $this->request->file($field);
            $extension = $files->getClientOriginalExtension();
            $name = $this->uuidGenerator();
            $value = $name . '.' . $extension;
        }
        return $value;
    }

    //disk setting on app/config/filesystem
    public function uploadImage($field, $targetName = '', $disk = 'upload')
    {
        $file = $this->request->file($field);
        return Storage::disk($disk)->put($targetName, File::get($file));
    }

    public function uuidGenerator()
    {
        return Uuid::uuid1()->toString();
    }

    public function checkValidation(Request $request)
    {
        $data = $request->all();
        return Validator::make($data, $this->getValidationRules(), $this->getValidationMessage());
    }

    public function isAuth($credentials = [])
    {
        if (count($credentials) > 0 && Auth::attempt($credentials)) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getValidationRules()
    {
        return $this->validationRules;
    }

    /**
     * @param mixed $validationRules
     * @return CustomController
     */
    public function setValidationRules($validationRules)
    {
        $this->validationRules = $validationRules;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidationMessage()
    {
        return $this->validationMessage;
    }

    /**
     * @param mixed $validationMessage
     * @return CustomController
     */
    public function setValidationMessage($validationMessage)
    {
        $this->validationMessage = $validationMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     * @return CustomController
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function postField($key)
    {
        return $this->request->request->get($key);
    }

    public function field($key)
    {
        return $this->request->query->get($key);
    }

    public function jsonResponse($msg = '', $status = 200, $data = null)
    {
        return response()->json([
            'status' => $status,
            'message' => $msg,
            'payload' => $data
        ], $status);
    }

    public function getCity()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/city",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 2ffbe3b7365f2ccb2c3929a3a4de486c"
            ),
        ));

        $response = curl_exec($curl);
        return json_decode($response, true);
    }

    public function getOnkgir($tujuan)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=445&destination=" . $tujuan . "&weight=1700&courier=jne",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: 2ffbe3b7365f2ccb2c3929a3a4de486c"
            ),
        ));

        $response = curl_exec($curl);
        return json_decode($response, true);
    }

    public function convertToPdf($viewRender, $data = [])
    {
        $html = view($viewRender)->with($data);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->stream();
    }

    public function basicDataTables($object)
    {
        return DataTables::of($object)->addIndexColumn()->make(true);
    }

    public function send_notification(Messaging $messaging, $to, $title = 'Title', $body = 'Body')
    {
        $data['title'] = $title;
        $data['body'] = $body;
        $message = CloudMessage::fromArray([
            'token' => $to,
            'data' => $data,
            'webpush' => [
                'headers' => [
                    'Urgency' => 'normal',
                ],
            ],

        ]);
        $messaging->send($message);
    }

    public function jsonSuccessResponse($msg = '', $data = null)
    {
        return response()->json([
            'status' => 200,
            'message' => $msg,
            'data' => $data
        ], 200);
    }

    public function jsonCreatedResponse($msg = '', $data = null)
    {
        return response()->json([
            'status' => 201,
            'message' => $msg,
            'data' => $data
        ], 201);
    }

    public function jsonAcceptedResponse($msg = '', $data = null)
    {
        return response()->json([
            'status' => 202,
            'message' => $msg,
            'data' => $data
        ], 202);
    }

    public function jsonNotFoundResponse($msg = 'item not found!', $data = null)
    {
        return response()->json([
            'status' => 404,
            'message' => $msg,
            'data' => $data
        ], 404);
    }

    public function jsonUnauthorizedResponse($msg = 'unauthorized', $data = null)
    {
        return response()->json([
            'status' => 401,
            'message' => $msg,
            'data' => $data
        ], 401);
    }

    public function jsonBadRequestResponse($msg = '', $data = null, $errors = null)
    {
        return response()->json([
            'status' => 400,
            'message' => $msg,
            'data' => $data,
            'errors' => $errors
        ], 400);
    }

    public function jsonForbiddenResponse($msg = '', $data = null, $errors = null)
    {
        return response()->json([
            'status' => 403,
            'message' => $msg,
            'data' => $data
        ], 403);
    }


    public function jsonErrorResponse($msg = '', $data = null)
    {
        return response()->json([
            'status' => 500,
            'message' => 'terjadi kesalahan server ' . $msg,
            'data' => $data
        ], 500);
    }
}
