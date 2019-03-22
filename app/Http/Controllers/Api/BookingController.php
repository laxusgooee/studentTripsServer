<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\User;
use App\Booking;
use App\Terminal;
use App\Destination;
use App\BusDestination;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only([]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $user = auth('api')->user();
            return response()->json($user);

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }


    public function terminals(Request $request)
    {
        try{

            $search = [];

            $page = $request->input('page', 1);
            $limit = $request->input('limit', 15);
            $offset = $request->input('offset', 0);

            if($page > 1)
                $offset = ($page - 1) * $limit;

            $query = Terminal::where('is_available', 1);

            //search by name or destination
            if(!empty($request->q)){
                $search['q'] = $request->q;

                $query = $query->where('name', 'like', '%'.$search['q'].'%');
                $des_query = Destination::where('is_available', 1)->where(function($q) use ($search){
                    $q->where('city', 'like', '%'.$search['q'].'%')->orWhere('state', 'like', '%'.$search['q'].'%')->orWhere('country', 'like', '%'.$search['q'].'%');
                })->get();

                $query = $query->orWhereIn('destination_id', $des_query->pluck('id'));     
            }

            //one terminal is already picked, get the other one
            if(!empty($request->terminal)){
                $search['terminal'] = $request->terminal;

                //todo: select by bus
                $query = $query->where('id', '<>', $search['terminal']);

            }

            $total_cnt = $query->count();

            $terminals =  $query->with(['destination' => function ($q){
                $q->select('id', 'city', 'state', 'country');
            }])->take($limit)->skip($offset)->get();

            return response()->json([
                'data' => $terminals,
                'page_count' => $terminals->count(),
                'per_page' => $limit,
                'page' => $page,
                'total_count' => $total_cnt,
                'search' => $search
            ]);

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }

    public function buses(Request $request)
    {
        try{

            $search = [];

            $validator = Validator::make($request->all(), [
                'terminal_from' => 'required|integer',
                'terminal_to' => 'required|integer',
                'departing_date' => 'required|date',
                'travelers' => 'required|integer',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $page = $request->input('page', 1);
            $limit = $request->input('limit', 15);
            $offset = $request->input('offset', 0);

            if($page > 1)
                $offset = ($page - 1) * $limit;

            $query = new BusDestination();

            $query = $query->where('terminal_from', $request->terminal_from)->where('terminal_to', $request->terminal_to);
            $query = $query->whereDate('departing_date', '>=', new Carbon($request->departing_date));

            $total_cnt = $query->count();

            $terminals =  $query->with('bus')->take($limit)->skip($offset)->get();
            //get with available seats
            $terminals = $terminals->map(function($q){
                $booking = Booking::where('bus_destination_id', $q->id);
                $passengers = $booking->sum('traveler_no');
                $seats = $booking->get()->pluck('seats');

                $x = [];

                foreach ($seats as $seat) {
                   $x[] = json_decode($seat);
                }

                $q->reserved_seats = collect($x)->flatten();
                $q->available_seats = $q->bus->seat_number - $passengers;

                return $q;
            })->filter(function($q){
                return $q->available_seats > 1;
            });
           
            return response()->json([
                'data' => $terminals,
                'page_count' => $terminals->count(),
                'per_page' => $limit,
                'page' => $page,
                'total_count' => $total_cnt,
                'search' => $search
            ]);

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }

}
