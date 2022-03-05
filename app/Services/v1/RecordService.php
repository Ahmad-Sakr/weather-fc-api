<?php


namespace App\Services\v1;


use App\Events\PullData;
use App\Http\Requests\v1\RecordRequest;
use App\Http\Resources\v1\RecordResource;
use App\Interfaces\v1\RecordInterface;
use App\Models\City;
use App\Models\Record;
use App\Traits\ApiResponder;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Event;

class RecordService implements RecordInterface
{
    use ApiResponder;

    public function forecast($date, $city = null)
    {
        $builder = Record::query()
                           ->orderBy('rc_date')
                           ->whereDate('rc_date', $date);
        if($city) {
            $builder->where('city_id', $city->id);
        }
        $records = $builder->with('city')
                           ->get();

        if($records->count() === 0) {
            return $this->error('No available data in the input date.', Response::HTTP_NOT_FOUND);
        }

        Event::dispatch(new PullData($date, $city));

        return $this->success(RecordResource::collection($records),"List of Records", Response::HTTP_OK);
    }

    public function storeRecord(RecordRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $city = City::query()->where('name', $data['city'])->first();

            $this->storeRecordInDatabase($data, $city);

            DB::commit();
            return $this->success([], '', Response::HTTP_OK);
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeRecordInDatabase($data, $city) {

        //Create Or Update Record
        $record = Record::query()
            ->where('rc_date', $data['rc_date'])
            ->where('city_id', $city->id)
            ->first();
        if(!$record) {
            $record = Record::query()->create([
                'rc_date' => $data['rc_date'],
                'city_id' => $city->id,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
        else {
            $record->touch();

            //Remove Old Details
            $record->details()->delete();
        }

        //Save New Details
        $record->details()->createMany($data['details']);

        return $record;

    }
}
