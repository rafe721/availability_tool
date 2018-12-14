<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Services\PropertyService;

class AvailabilityController extends Controller
{
    // Property service acts as a downstream API...
    protected $propertyService;

    // Constructor with Dependency injection for PropertySearchService...
    public function __construct(PropertyService $propertySearch)
    {
        $this->propertyService = $propertySearch;
    }

    // Web route that redirects to the check_availability view
    public function index()
    {
        return view("check_availability");
    }

    /* API Endpoint - to handle Room search requests..
     */
    public function search(Request $request)
    {
        $no_of_guests = $arrival_date = "";
        $this->validate($request, [
            'arrival_date' => 'required|date', //|after:yesterday',
            'no_of_guests' => 'required|integer|max:255',
        ]);
        if (isset($request["arrival_date"]))  { // stardard PHP param check... not necessary here as validator will take care of it..
            // convert it to desired format...
            $arrival_date = date_format(date_create($request["arrival_date"]), 'Y-m-d');
        }
        if (isset($request["no_of_guests"]))  {
            $no_of_guests = $request["no_of_guests"];
            if ($no_of_guests == 0) { // if 0 (Zero) then show no rooms; Zero is the default...
                $no_of_guests = 99999;
            }
        }
        // prepare response to contain an echo of input information (format of date has been changed)....
        $response_data = array(
            "arrival_date" => $arrival_date,
            "no_of_guests" => $no_of_guests,
        );

        $data = $this->propertyService->searchProperty(123, $arrival_date, $no_of_guests);

        return response()->json(array_merge($response_data, $data));
    }
    /* API Endpoint - to handle Room enquiry requests.
     */
    public function enquire(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ], [ // these are list of format for corresponding error messages
            'required' => 'The :attribute field is required.'
        ]);

        if ($validator->fails()) {
            // for now... just return with Errors. This will be printed as JSON in the front end..
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

}
