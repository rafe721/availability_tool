<?php

namespace App\Http\Controllers;

use Validator;
use App\Property;
use App\Room;
use App\Rate;
use App\Availability;
use Illuminate\Http\Request;
use App\Services\PropertySearchService;
use App\Services\CalendarService;

class AvailabilityController extends Controller
{

    protected $propertySearch;
    protected $calendarService;

    public function __construct(PropertySearchService $propertySearch, CalendarService $calendarService)
    {
        $this->propertySearch = $propertySearch;
        $this->calendarService = $calendarService;
    }

    public function index()
    {
        return view("check_availability");
    }

    public function search(Request $request)
    {
        $no_of_guests = $arrival_date = "";
        $this->validate($request, [
            'arrival_date' => 'required|date', //|after:yesterday',
            'no_of_guests' => 'required|integer|max:255',
        ]);
        if (isset($request["arrival_date"]))  {
            $arrival_date = date_format(date_create($request["arrival_date"]), 'Y-m-d');
        }
        if (isset($request["no_of_guests"]))  {
            $no_of_guests = $request["no_of_guests"];
            if ($no_of_guests == 0) {
                $no_of_guests = 99999;
            }
        }

        $response_data = array(
            "arrival_date" => $arrival_date,
            "no_of_guests" => $no_of_guests,
            "stuff" => $this->calendarService->printStuff()
        );

        $data = $this->propertySearch->searchProperty(123, $arrival_date, $no_of_guests);

        return response()->json(array_merge($response_data, $data));
    }

    public function enquire(Request $request)
    {
//        $this->validate($request, [
//            'first_name' => 'required|string|max:255',
//            'last_name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255',
//        ]);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ], [
            'required' => 'The :attribute field is required.'
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors());
        }
        $first_name = $last_name = $email = $arrival_date = "";
        $room_id = $nights = $total_rate = 0;
        if (isset($request["first_name"]))  {
            $first_name = $request["first_name"];
        }
        if (isset($request["last_name"]))  {
            $last_name = $request["last_name"];
        }
        if (isset($request["email"]))  {
            $email = $request["email"];
        }
        if (isset($request["email"]))  {
            $room_id = $request["room_id"];
        }
        if (isset($request["email"]))  {
            $arrival_date = $request["arrival_date"];
        }
        if (isset($request["email"]))  {
            $nights = $request["nights"];
        }
        if (isset($request["email"]))  {
            $total_rate = $request["total_rate"];
        }

        $data = [
            "room_id" => $room_id,
            "arrival_date" => $arrival_date,
	        "nights" => $nights,
	        "total-rate" => $total_rate,
            "guest_firstname" => $first_name,
            "guest_lastname" => $last_name,
            "guest_email" => $email,
        ];
        return response()->json($data);
    }

    public function test (Request $request)
    {
        header('Content-Type: application/json');
        $data = $this->propertySearch->searchProperty(123, "2018-12-13", 2);
        $end_dates = array_column($data["availability"], "end_date");
        $calendar_availability = array();
        $rooms = array();
        foreach ($data['rooms'] as $room)
        {
            $rooms[$room['id']] = $room;
        }
        foreach ($data["availability"] as $room_availability) {
            $period = $this->getPeriod($room_availability["start_date"], $room_availability["end_date"]);
            foreach ($period as $value) {
                $date = $value->format('Y-m-d');
                if (!array_key_exists($date, $calendar_availability)) {
                    $calendar_availability[$date] = array();
                }
                $calendar_availability[$date][$room_availability['room_id']] = [
                    "available" => $room_availability['available']
                ];
            }
        }
        foreach ($data["rates"] as $room_rates) {
            $period = $this->getPeriod($room_rates["start_date"], $room_rates["end_date"]);
            foreach ($period as $value) {
                $date = $value->format('Y-m-d');
                $room_id = $room_rates['room_id'];
                if (!array_key_exists($date, $calendar_availability)) {
                    $calendar_availability[$date] = array();
                }
                if (!array_key_exists($room_id, $calendar_availability[$date])) {
                    $calendar_availability[$date][$room_id] = [ "available" => false ];
                }
                $calendar_availability[$date][$room_id]["rate"] = $room_rates['rate'];
            }
        }

        echo json_encode($rooms, JSON_PRETTY_PRINT);
        echo "\n";
        echo json_encode($calendar_availability, JSON_PRETTY_PRINT);
//        echo json_encode($rates_calendar, JSON_PRETTY_PRINT);
        echo "\n";
        echo min($end_dates);
        echo "\n";
        echo max($end_dates);
        echo "\n";
    }

    private function getPeriod($start_date, $end_date, $interval = 'P1D') {
        return new \DatePeriod(
            new \DateTime($start_date),
            new \DateInterval($interval),
            new \DateTime($end_date)
        );
    }

}
