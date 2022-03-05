<?php


namespace App\Interfaces\v1;


use App\Http\Requests\v1\RecordRequest;

interface RecordInterface
{
    public function forecast($date, $city = null);

    public function storeRecord(RecordRequest $request);

    public function storeRecordInDatabase($data, $city);
}
