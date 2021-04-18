<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\User;
use App\Models\Availability;

class AvailabilityController extends Controller
{
    // show all availability based on role
    // for user only show availability that user sent before
    // for owner only show availability that owner receiver based on 'ownerId'
    public function index(Request $request) {
        if ($request->user()['role'] != 0) {
            return response()->json(
                Availability::where('userId', $request->user()['id'])
                ->orderBy('kostId', 'asc')
                ->get(),
                200
            );
        } else {
            return response()->json(
                Availability::where('ownerId', $request->user()['id'])
                ->orderBy('kostId', 'asc')
                ->get(),
                200
            );
        }
    }
    
    // show availability based on role
    // for user only show availability that user sent before
    // for owner only show availability his kost based on 'kostId'
    public function show(Request $request, $kostId) {
        if ($request->user()['role'] != 0) {
            return response()->json(
                Availability::where('userId', $request->user()['id'])
                ->where('kostId', $kostId)
                ->orderBy('kostId', 'asc')
                ->get(),
                200
            );
        } else {
            return response()->json(Availability::find($kostId), 200);
        }
    }

    // Method for user to ask availability of owner's kost
    // this action will deducted user's credit by 5 point
    // this method only for user
    public function askRoomAvailability(Request $request, $kostId) {
        $userId = $request->user()['id'];
        $user = User::find($userId);
        $role = $user->role;
        if ($role == 0) {
            return response()->json([
                'status' => 'Access forbidden',
                'message' => 'This feature is restricted for current role'
            ], 403);
        } else {
            $kost = Kost::find($kostId);
            if ($kost) {
                $credit = $user->credit;
                if ($credit < 5){
                    return response()->json([
                        'status' => 'Insufficient Credit',
                        'message' => 'Your credit is not enough'
                    ], 200);
                } else {
                    $credit = $credit - 5;
                    $user->credit = $credit;
                    $user->save();

                    $availability = Availability::create([
                        'userId' => $userId,
                        'kostId' => $kostId,
                        'ownerId' => $kost->userId,
                        'status' => 0
                    ]);

                    if ($user && $availability) {
                        return response()->json([
                            'status' => 'OK',
                            'message' => 'Your request has been sent'
                        ], 202);
                    } else {
                        return response()->json([
                            'status' => 'Error',
                            'message' => 'Failed to send your request'
                        ], 500);
                    }
                }
            } else {
                return response()->json([
                    'status' => 'Not Found',
                    'message' => 'Data not found'
                ], 404);
            }
        }
    }

    // Method for owner to give availability to user's request
    // this method only for owner
    public function giveRoomAvailability(Request $request){
        $userId = $request->user()['id'];
        $user = User::find($userId);
        $role = $user->role;
        if ($role != 0) {
            return response()->json([
                'status' => 'Access forbidden',
                'message' => 'This feature is restricted for current role'
            ], 403);
        } else {
            $availability = Availability::find($request->id);
            if ($availability) {
                $availability->status = 1;
                $availability->is_available = $request->is_available;
                $availability->save();
    
                return response()->json([
                    'status' => 'OK',
                    'message' => 'Your request has been sent'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'Not Found',
                    'message' => 'Data not found'
                ], 404);
            }
        }
    }
}
