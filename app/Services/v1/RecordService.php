<?php


namespace App\Services\v1;


use App\Events\PullData;
use App\Http\Requests\v1\RecordRequest;
use App\Http\Resources\v1\RecordResource;
use App\Interfaces\v1\RecordInterface;
use App\Jobs\PullDataFromVendorAPIJob;
use App\Models\City;
use App\Models\Record;
use App\Traits\ApiResponder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $records = $this->getRecords($date, $city);

        if($records->count() === 0) {
            //Try To Get Data From Vendor API
            $cities = ($city) ? [$city] : City::all();
            foreach ($cities as $city1) {
                PullDataFromVendorAPIJob::dispatch($city1);
            }

            $records = $this->getRecords($date, $city);
            if($records->count() === 0) {
                throw new ModelNotFoundException('No available data in the input date.');
            }
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

    /**
     * @param $date
     * @param $city
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getRecords($date, $city)
    {
        $builder = Record::query()
            ->orderBy('rc_date')
            ->whereDate('rc_date', $date);
        if ($city) {
            $builder->where('city_id', $city->id);
        }
        $records = $builder->with('city')
            ->get();
        return $records;
    }
}
