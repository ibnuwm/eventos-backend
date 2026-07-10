<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VirtualExpoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DB::table('virtual_expos');
            $status = $request->query('status');
            if ($status === 'upcoming') {
                $query->where('status', 'upcoming');
            } elseif ($status === 'ongoing') {
                $query->where('status', 'ongoing');
            } elseif ($status === 'past') {
                $query->where('status', 'past');
            }
            $expos = $query->orderBy('event_date', 'desc')->get();
            return response()->json(['status' => 'success', 'data' => $expos]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $expo = DB::table('virtual_expos')->where('id', $id)->first();
            if (!$expo) {
                return response()->json(['status' => 'error', 'message' => 'Expo not found'], 404);
            }

            $booths = DB::table('virtual_expo_booths')
                ->join('vendors', 'virtual_expo_booths.vendor_id', '=', 'vendors.id')
                ->where('virtual_expo_booths.expo_id', $id)
                ->select(
                    'virtual_expo_booths.*',
                    'vendors.name as vendor_name',
                    'vendors.category as vendor_category',
                    'vendors.rating as vendor_rating'
                )
                ->get();

            $expo->booths = $booths;
            return response()->json(['status' => 'success', 'data' => $expo]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'required|date',
                'registration_end' => 'nullable|date',
                'status' => 'nullable|string|in:upcoming,ongoing,past',
            ]);

            $id = Str::uuid()->toString();
            DB::table('virtual_expos')->insert([
                'id' => $id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'event_date' => $validated['event_date'],
                'registration_end' => $validated['registration_end'] ?? null,
                'status' => $validated['status'] ?? 'upcoming',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $expo = DB::table('virtual_expos')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $expo], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function registerBooth(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'expo_id' => 'required|string',
                'vendor_id' => 'required|string',
                'booth_title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'video_url' => 'nullable|string|max:500',
                'gallery' => 'nullable|array',
            ]);

            $existing = DB::table('virtual_expo_booths')
                ->where('expo_id', $validated['expo_id'])
                ->where('vendor_id', $validated['vendor_id'])
                ->first();
            if ($existing) {
                return response()->json(['status' => 'error', 'message' => 'Vendor already registered in this expo'], 409);
            }

            $id = Str::uuid()->toString();
            DB::table('virtual_expo_booths')->insert([
                'id' => $id,
                'expo_id' => $validated['expo_id'],
                'vendor_id' => $validated['vendor_id'],
                'booth_title' => $validated['booth_title'] ?? null,
                'description' => $validated['description'] ?? null,
                'video_url' => $validated['video_url'] ?? null,
                'gallery' => isset($validated['gallery']) ? json_encode($validated['gallery']) : null,
                'visitor_count' => 0,
                'lead_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $booth = DB::table('virtual_expo_booths')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $booth], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getBooths(string $expoId): JsonResponse
    {
        try {
            $booths = DB::table('virtual_expo_booths')
                ->join('vendors', 'virtual_expo_booths.vendor_id', '=', 'vendors.id')
                ->where('virtual_expo_booths.expo_id', $expoId)
                ->select(
                    'virtual_expo_booths.*',
                    'vendors.name as vendor_name',
                    'vendors.category as vendor_category',
                    'vendors.rating as vendor_rating',
                    'vendors.starting_price',
                    'vendors.area'
                )
                ->get();
            return response()->json(['status' => 'success', 'data' => $booths]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function trackVisitor(string $boothId): JsonResponse
    {
        try {
            $booth = DB::table('virtual_expo_booths')->where('id', $boothId)->first();
            if (!$booth) {
                return response()->json(['status' => 'error', 'message' => 'Booth not found'], 404);
            }

            DB::table('virtual_expo_booths')->where('id', $boothId)->increment('visitor_count');
            return response()->json([
                'status' => 'success',
                'data' => ['visitor_count' => $booth->visitor_count + 1],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}