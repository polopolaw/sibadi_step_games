<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $capacity = $request->get('capacity');
        $type = $request->get('type');
        $user_id = auth()->user()->id;

        $existingRoom = Room::where('user_id', $user_id)->exists();
        if ($existingRoom) {
            return response()->json(['message' => 'У вас уже есть комната'], 400);
        }

        $newRoom = Room::create([
                                    'name' => $name,
                                    'capacity' => $capacity,
                                    'type' => $type,
                                    'user_id' => $user_id
                                ]);

        return response()->json(['message' => 'Комната создана успешно', 'room' => $newRoom], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room): \Illuminate\Http\Response|Room
    {
        return $room;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        $room = Room::find($id);
//        $room->delete();
        return Room::where('id', $id)->delete();
    }

    public function enter(Room $room, Request $request) {
        $user = auth()->user();

        if ($room->capacity === $user->rooms->count()) {
            return response()->json(['message' => "fail, room is full"]);
        }
        $user->rooms()->attach($room->id);
        return response()->json(['message' => "success"]);
    }
}
