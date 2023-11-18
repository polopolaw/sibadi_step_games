<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return Room::where('type', $request->get('type'))->latest()->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $capacity = $request->get('capacity');
        $type = $request->get('type');
        $user = auth()->user();

        $existingRoom = Room::where('user_id', $user->id)->first();
        if ($existingRoom) {
            return response()->json(['message' => 'У вас уже есть комната', 'room' => $existingRoom->id], 400);
        }

        $newRoom = Room::create([
                                    'name' => $name,
                                    'capacity' => $capacity,
                                    'type' => $type,
                                    'user_id' => $user->id
                                ]);
        $user->rooms()->attach($newRoom->id);

        return response()->json(['message' => 'Комната создана успешно', 'room' => $newRoom], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(Room $room): Response|Room
    {
        return $room;
    }

    public function leave(Room $room): JsonResponse
    {
        auth()->user()->rooms()->detach($room->id);
        if (blank($room->users)) {
            $room->delete();
        }
        return response()->json(["message" => "Success"]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        return Room::where('id', $id)->delete();
    }

    public function enter(Room $room) {
        $user = auth()->user();
        if ($room->status !== Room::STATUS_WAITING) {
            return response()->json(['message' => "Room started or closed"], 400);
        }
        if (!blank($user->rooms)) {
            return response()->json(['message' => "You already in room"], 400);
        }
        if ($room->capacity === $room->users->count()) {
            return response()->json(['message' => "fail, room is full"], 400);
        }
        $user->rooms()->attach($room->id);
        return response()->json(['message' => "success"]);
    }
}
