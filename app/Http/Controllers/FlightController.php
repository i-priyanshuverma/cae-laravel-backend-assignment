<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Roster;
use Carbon\Carbon;
use Exception;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;

class FlightController extends Controller
{

    /**
     * can uploads the file on Web/UI as well on route '/'
     */
    public function uploadFile(Request $request)

    {

        $this->validate($request, ['file' => 'required|mimes:html,htm|max:1024']);

        if ($this->storeFile($request)) {

            return redirect()->back()->with('flash_success', 'Your file has been uploaded.');
        } else {

            return redirect()->back()->with('flash_error', 'Something went wrong! Please try again.');
        }
    }

    /**
     * as per current scenario the same file will be over-written with name roster.html||roster.htm
     * though the data will be update in the table as per the content in file
     */

    public function storeFile(Request $request)
    {
        if ($request->hasFile('file')) {

            $saveFile = $request->file->storeAs('file', 'roster.html', 'local');
            if (!$saveFile) return false;

            return true;
        }

        return false;
    }

    /**
     * @var from & @var to are used simultaneously to get records in between those dates    
     * @var keyword with value weekly is used for to get records on weekly basis 
     * @var keyword with value weekly_standby is used for to get records on weekly basis where activity is SBY/StandBy 
     * @var start_location is used to get records basis on from where the flight will depart
     */
    public function getFlightsData(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), [
                'from' => 'required_with:to|date',
                'to' => 'required_with:from|date|after:from',
                'keyword' => 'nullable|string|in:weekly,weekly_standby',
                'start_location' => 'nullable|string|exists:flights,from',
            ]);

            if ($validation->fails()) {
                $errors = $validation->messages()->first();
                return response()->json($errors, 400);
            }

            $from = $request->from ?? null;
            $to = $request->to ?? null;
            $keyword = $request->keyword ?? null;
            $start_location = $request->start_location ?? null;

            $getFlightDataQuery = Flight::select('*');

            if (isset($start_location)) {

                $getFlightDataQuery->where('from', $start_location);
            }

            if (isset($from) && isset($to)) {

                $startDate = $from;
                $endDate = $to;
            }

            if (isset($keyword)) {

                $startDate = Carbon::parse('14 Jan 2022')->format('Y-m-d');
                $endDate = Carbon::parse('14 Jan 2022')->addWeek()->format('Y-m-d');

                if (isset($keyword) && ($keyword == 'weekly_standby')) {

                    $getFlightDataQuery->where('activity', 'SBY')->orWhere('remark', 'SBY');
                }
            }

            if (isset($startDate) && isset($endDate)) {

                $getFlightDataQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $getFlightData = $getFlightDataQuery->orderBy('date')->orderBy('std(z)')->get();

            if (empty($getFlightData)) {

                return response()->json('No data found!', 200);
            }
            return response()->json($getFlightData, 200);
        } catch (Exception $exception) {

            return $exception;
            return response()->json('Something went wrong!', 500);
        }
    }

    /**
     * a new @var file can uploaded over api if required
     * new/updated data will be stored in the DB
     */
    public function parseHtmlRoster(Request $request)
    {
        // try {

            $validation = Validator::make($request->all(), [
                'file' => 'nullable|file|mimes:html,htm|size:1024',  // file size is restricted at 1MB and of type .html/.htm only
            ]);

            if ($validation->fails()) {
                $errors = $validation->messages()->first();
                return response()->json($errors, 400);
            }

            if (isset($request->file)) {
                if (!$this->storeFile($request)) {

                    return response()->json('Something went wrong! file not uploaded!.', 500);
                }
            }

            $inserData = [];
            $html = Storage::get('/file/roster.html');
            $crawler = new Crawler($html);

            $nodeValues = $crawler->filter('b')->each(function (Crawler $node, $i) {

                if (str_contains($node->text(), 'Period')) {
                    return $node->text();
                }
            });

            $period =  array_filter($nodeValues);
            $period = substr(ltrim(str_replace(' ', '', $period[0]), 'Period:'), 0, 16);

            if (str_contains($period, 'to')) {

                $date['fromDate'] =  Carbon::parse(stristr($period, "to", true))->format('d-m-Y');
                $date['toDate'] = Carbon::parse(ltrim(stristr($period, "to"), 'to'))->format('d-m-Y');
            }

            $tableData = $crawler->filter('table')/*->first()*/->each(function ($table) {
                return $table->filter('tr')->each(function ($tr) {
                    return $tr->filter('td')->each(function ($td) {
                        return $td->text();
                    });
                });
            });

            $returData['columns'] = $tableData[0][0];
            $returData['rows'] =  array_slice($tableData[0], 1);

            foreach ($returData['columns'] as $cKey => $column) {

                $returData['columns'][$cKey] = trim((str_replace(' ', '_', strtolower($column))), '.');
            }

            $i = 0;
            foreach ($returData['rows'] as $k => $row) {

                foreach ($row as $key => $item) {

                    if (empty($item)) {

                        $insertItem = null;
                    } else {

                        $insertItem = $item;
                    }

                    $inserData[$k][$returData['columns'][$key]] = $insertItem;
                }
            }

            foreach ($inserData as $newRow) {
                if (isset($newRow['date']) && (!empty($newRow['date']))) {

                    $flyingDate = Carbon::parse($newRow['date'] . (substr($date['fromDate'], 2)))->format('Y-m-d');
                    $flyingDay  = substr($newRow['date'], 0, -3);
                }

                $newRow['date'] = $flyingDate;
                $newRow['day'] = $flyingDay;

                $flightData = Flight::where([
                    'date' => $flyingDate,
                    'day' => $flyingDay,
                    'activity' => $newRow['activity'],
                    'remark' => $newRow['remark'],
                    'std(z)' => $newRow['std(z)'],
                    'std(l)' => $newRow['std(l)'],
                    'sta(z)' => $newRow['sta(z)'],
                    'sta(l)' => $newRow['sta(l)'],
                    'c/i(z)' => $newRow['c/i(z)'],
                    'c/i(l)' => $newRow['c/i(l)'],
                    'c/o(z)' => $newRow['c/o(z)'],
                    'c/o(l)' => $newRow['c/o(l)'],
                    'to' => $newRow['to'],
                    'from' => $newRow['from'],
                ])->first();

                if ($flightData) {

                    $log = $flightData->update(array_filter($newRow));
                } else {

                    $log = Flight::create(array_filter($newRow));
                }
                Log::info($log);
            }

            return response()->json('Success', 200);
        // } catch (Exception $exception) {

        //     dd($exception);
        //     return response()->json('Something went wrong!', 500);
        // }
    }
}
