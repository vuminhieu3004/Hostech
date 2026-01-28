<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms in a property.
     */
    public function index(Request $request, Property $property)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        // Check access to property
        if (!in_array($property->id, $scopedPropertyIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhà/khu hoặc không có quyền truy cập'
            ], 404);
        }

        if ($property->org_id !== $request->user()->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $query = Room::where('property_id', $property->id);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $rooms = $query->orderBy('code')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $rooms->items(),
            'meta' => [
                'current_page' => $rooms->currentPage(),
                'last_page' => $rooms->lastPage(),
                'per_page' => $rooms->perPage(),
                'total' => $rooms->total(),
            ]
        ]);
    }

    /**
     * Store a newly created room.
     */
    public function store(Request $request, Property $property)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        if (!in_array($property->id, $scopedPropertyIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhà/khu hoặc không có quyền truy cập'
            ], 404);
        }

        if ($property->org_id !== $request->user()->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms')->where(function ($query) use ($property) {
                    return $query->where('property_id', $property->id);
                })
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|in:studio,apartment,house,dormitory,other',
            'area' => 'nullable|numeric|min:0',
            'floor' => 'nullable|integer',
            'capacity' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'status' => 'nullable|in:available,occupied,maintenance,reserved',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'utilities' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $room = Room::create([
            'org_id' => $request->user()->org_id,
            'property_id' => $property->id,
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'area' => $request->input('area'),
            'floor' => $request->input('floor'),
            'capacity' => $request->input('capacity'),
            'base_price' => $request->input('base_price'),
            'status' => $request->input('status', 'available'),
            'description' => $request->input('description'),
            'amenities' => $request->input('amenities'),
            'utilities' => $request->input('utilities'),
        ]);

        AuditLogger::log(
            $request->user()->org_id,
            $request->user()->id,
            'rooms.created',
            Room::class,
            $room->id,
            ['room' => $room->toArray(), 'property_id' => $property->id],
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $room,
            'message' => 'Tạo phòng thành công'
        ], 201);
    }

    /**
     * Display the specified room.
     */
    public function show(Request $request, Room $room)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        // Check property access
        if (!in_array($room->property_id, $scopedPropertyIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phòng hoặc không có quyền truy cập'
            ], 404);
        }

        if ($room->org_id !== $request->user()->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $room->load('property')
        ]);
    }

    /**
     * Update the specified room.
     */
    public function update(Request $request, Room $room)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        if (!in_array($room->property_id, $scopedPropertyIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phòng hoặc không có quyền truy cập'
            ], 404);
        }

        if ($room->org_id !== $request->user()->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('rooms')->where(function ($query) use ($room) {
                    return $query->where('property_id', $room->property_id);
                })->ignore($room->id)
            ],
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:studio,apartment,house,dormitory,other',
            'area' => 'nullable|numeric|min:0',
            'floor' => 'nullable|integer',
            'capacity' => 'sometimes|required|integer|min:1',
            'base_price' => 'sometimes|required|numeric|min:0',
            'status' => 'nullable|in:available,occupied,maintenance,reserved',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'utilities' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $oldData = $room->toArray();
        $room->update($request->only([
            'code',
            'name',
            'type',
            'area',
            'floor',
            'capacity',
            'base_price',
            'status',
            'description',
            'amenities',
            'utilities'
        ]));

        AuditLogger::log(
            $request->user()->org_id,
            $request->user()->id,
            'rooms.updated',
            Room::class,
            $room->id,
            ['old' => $oldData, 'new' => $room->fresh()->toArray()],
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $room->fresh(),
            'message' => 'Cập nhật phòng thành công'
        ]);
    }

    /**
     * Remove the specified room.
     */
    public function destroy(Request $request, Room $room)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        if (!in_array($room->property_id, $scopedPropertyIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phòng hoặc không có quyền truy cập'
            ], 404);
        }

        if ($room->org_id !== $request->user()->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $roomData = $room->toArray();
        $room->delete();

        AuditLogger::log(
            $request->user()->org_id,
            $request->user()->id,
            'rooms.deleted',
            Room::class,
            $room->id,
            ['room' => $roomData, 'property_id' => $room->property_id],
            $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Xóa phòng thành công'
        ]);
    }
}