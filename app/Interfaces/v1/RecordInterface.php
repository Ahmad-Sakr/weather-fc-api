<?php


namespace App\Interfaces\v1;


use App\Http\Requests\v1\RecordRequest;

interface RecordInterface
{
    public function storeRecord(RecordRequest $request);
}
