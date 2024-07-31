<?php


namespace App\Helper;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LazyController extends Controller
{

    /** @var Request $request */
    protected $request;

    /**
     * LazyController constructor.
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    public function lazyInsert(){

    }
}
