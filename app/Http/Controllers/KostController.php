<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Kost;
use App\Models\Availability;

class KostController extends Controller
{
    public function index() {
        return response()->json(Kost::all(), 200);
    }

    public function show($id) {
        return response()->json(Kost::find($id), 200);
    }

    public function getKostByOwnerId(Request $request) {
        if ($request->user()['role'] != 0) {
            return response()->json([
                'status' => 'Access forbidden',
                'message' => 'This feature is restricted for current role'
            ], 403);
        } else {
            return response()->json(
                Kost::where('userId', $request->user()['id'])
                ->orderBy('price', 'asc')
                ->get(),
                200
            );
        }
    }

    public function insert(Request $request) {
        if ($request->user()['role'] != 0) {
            return response()->json([
                'status' => 'Access forbidden',
                'message' => 'This feature is restricted for current role'
            ], 403);
        } else {
            $data = $request->all();
       
            $validator = Validator::make($data, [
                'name' => 'required',
                'description' => 'required',
                'location' => 'required',
                'price' => 'required'
            ]);
       
            if($validator->fails()){
                return response()->json([
                    'status' => 'Bad request',
                    'message' => 'Validation Error.'.$validator->errors()
                ], 400);
            }
    
            $data['userId'] = $request->user()['id'];
       
            $kost = Kost::create($data);
    
            return response()->json([
                'status' => 'OK',
                'message' => 'Create success',
                'data' => $data
            ], 201);
        }
    }

    public function update(Request $request, $id) {
        if ($request->user()['role'] != 0) {
            return response()->json([
                'status' => 'Access forbidden',
                'message' => 'This feature is restricted for current role'
            ], 403);
        } else {
            $kost = Kost::find($id);
            if ($kost) {
                if ($request->name != null) {
                    $kost->name = $request->name;
                }
                if ($request->description != null) {
                    $kost->description = $request->description;
                }
                if ($request->location != null) {
                    $kost->location = $request->location;
                }
                if ($request->price != null) {
                    $kost->price = $request->price;
                }
                $kost->save();
    
                return response()->json([
                    'status' => 'OK',
                    'message' => 'Edit success',
                    'data' => $kost
                ], 200);
            } else {
                return response()->json([
                    'status' => 'Not Found',
                    'message' => 'Data not found'
                ], 404);
            }
        }
    }

    public function destroy(Request $request, $id) {
        if ($request->user()['role'] != 0) {
            return response()->json([
                'status' => 'Access forbidden',
                'message' => 'This feature is restricted for current role'
            ], 403);
        } else {
            $kost = Kost::find($id);
            if ($kost) {
                // When delete kost
                // availability with 'kostId' must be deleted if available
                $kost->destroy($id);
                $availability = Availability::where('kostId', $id);
                if ($availability) {
                    $availability->delete();
                }
                return response()->json([
                    'status' => 'OK',
                    'message' => 'Delete success'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'Not Found',
                    'message' => 'Data not found'
                ], 404);
            }
        }
    }

    public function find(Request $request) {
        $name = $request->name;
        $location = $request->location;
        $price = $request->price;

        // search by selecting parameter
        // available parameter is name, location, and price
        // result sorted by lowest price
        $kost = Kost::when($name, function ($query) use ($name) {
                        return $query->where('name', 'like', '%'.$name.'%');
                    })
                    ->when($location, function ($query) use ($location) {
                        return $query->where('location', 'like', '%'.$location.'%');
                    })
                    ->when($price, function ($query) use ($price) {
                        return $query->where('price', $price);
                    })
                    ->orderBy('price', 'asc')
                    ->get();
        if ($kost) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Find success',
                'data' => $kost
            ], 200);
        } else {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Data not found'
            ], 404);
        }
    }
}
