<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties (auto-filtered by middleware).
     */
    public function index(Request $request)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        // If no scoped properties, return empty
        if (empty($scopedPropertyIds)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'message' => 'No properties accessible'
                ]
            ]);
        }

        $query = Property::query()
            ->whereIn('id', $scopedPropertyIds)
            ->where('org_id', $request->user()->org_id);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = $request->input('per_page', 15);
        $properties = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $properties->items(),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ]
        ]);
    }

    /**
     * Store a newly created property.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('properties')->where(function ($query) use ($request) {
                    return $query->where('org_id', $request->user()->org_id);
                })
            ],
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,maintenance',
            'billing_cycle_day' => 'nullable|integer|min:1|max:31',
            'bank_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $property = Property::create([
            'org_id' => $request->user()->org_id,
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'address' => $request->input('address'),
            'description' => $request->input('description'),
            'status' => $request->input('status', 'active'),
            'billing_cycle_day' => $request->input('billing_cycle_day', 1),
            'bank_info' => $request->input('bank_info'),
        ]);

        AuditLogger::log(
            $request->user()->org_id,
            $request->user()->id,
            'properties.created',
            Property::class,
            $property->id,
            ['property' => $property->toArray()],
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $property,
            'message' => 'Property created successfully'
        ], 201);
    }

    /**
     * Display the specified property.
     */
    public function show(Request $request, Property $property)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        // Check if user has access to this property
        if (!in_array($property->id, $scopedPropertyIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found or access denied'
            ], 404);
        }

        // Double-check org ownership
        if ($property->org_id !== $request->user()->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $property
        ]);
    }

    /**
     * Update the specified property.
     */
    public function update(Request $request, Property $property)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        // Check if user has access to this property
        if (!in_array($property->id, $scopedPropertyIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found or access denied'
            ], 404);
        }

        // Double-check org ownership
        if ($property->org_id !== $request->user()->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('properties')->where(function ($query) use ($request) {
                    return $query->where('org_id', $request->user()->org_id);
                })->ignore($property->id)
            ],
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,maintenance',
            'billing_cycle_day' => 'nullable|integer|min:1|max:31',
            'bank_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $oldData = $property->toArray();
        $property->update($request->only([
            'name',
            'code',
            'address',
            'description',
            'status',
            'billing_cycle_day',
            'bank_info'
        ]));

        AuditLogger::log(
            $request->user()->org_id,
            $request->user()->id,
            'properties.updated',
            Property::class,
            $property->id,
            ['old' => $oldData, 'new' => $property->fresh()->toArray()],
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $property->fresh(),
            'message' => 'Property updated successfully'
        ]);
    }

    /**
     * Remove the specified property.
     */
    public function destroy(Request $request, Property $property)
    {
        $scopedPropertyIds = $request->input('_scoped_property_ids', []);

        // Check if user has access to this property
        if (!in_array($property->id, $scopedPropertyIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found or access denied'
            ], 404);
        }

        // Double-check org ownership
        if ($property->org_id !== $request->user()->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $propertyData = $property->toArray();
        $property->delete();

        AuditLogger::log(
            $request->user()->org_id,
            $request->user()->id,
            'properties.deleted',
            Property::class,
            $property->id,
            ['property' => $propertyData],
            $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Property deleted successfully'
        ]);
    }
}
