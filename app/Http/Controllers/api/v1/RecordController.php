<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\RecordRequest;
use App\Interfaces\v1\RecordInterface;

class RecordController extends Controller
{
    protected $recordInterface;

    public function __construct(RecordInterface $recordInterface)
    {
        $this->recordInterface = $recordInterface;
    }

    public function store(RecordRequest $request)
    {
        return $this->recordInterface->storeRecord($request);
    }
}
