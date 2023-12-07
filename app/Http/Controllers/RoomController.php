<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Step;
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
    public function index(Request $request, Room $room)
    {
        #if ($room->status === Room::STATUS_WAITING) {
          #  return $room;
       # }
        #return Room::latest()->when($request->has('type'), function ($q) use($request){
            #$q->where('type', $request->get('type'));
        #})->paginate(20);
        $waitingRooms = Room::where('status', Room::STATUS_WAITING)->paginate(20);

        return $waitingRooms;
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

    public function enter(Room $room)
    {
        $user = auth()->user();
        if ($room->status !== Room::STATUS_WAITING) {
            return response()->json(['message' => "Room started or closed"], 400);
        }
        if (!blank($user->rooms)) {
            return response()->json(['message' => "You already in room"], 400);
        }
        $roomUsersCount = $room->users->count();
        if ($room->capacity === $roomUsersCount) {
            return response()->json(['message' => "fail, room is full"], 400);
        }
        $user->rooms()->attach($room->id);
        if ($room->capacity === $roomUsersCount + 1) {
            $room->refresh();
            $room->status = Room::STATUS_PLAYING;
            $room->user_order = $room->users->pluck('id')->shuffle()->toArray();
            $room->save();
        }
        return response()->json(['message' => "success"]);
    }

    public function createStep(Room $room, Request $request)
    {
        $capacity = count($room->user_order);
        $currentUserIndex = $room->steps->count() % $capacity;

        if ($room->user_order[$currentUserIndex] === auth()->user()->id) {
            return $room->steps()
                        ->create(['data' => $request->get('data'), 'user_id' => auth()->user()->id]);
        }
        return \response()->json(['message' => 'Not your queue']);
    }

    public function getUpdates(Room $room, Request $request)
    {
        return [
            'count' => $room->steps->count(),
            'steps' => $room->steps()
                ->with('user:id')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];
    }
}
