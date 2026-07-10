<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantRegistrationController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:tenant_registrations,email',
                'whatsapp' => 'required|string|max:20',
                'password' => 'required|string|min:8',
            ]);

            $tenantId = Str::uuid()->toString();
            $userId = Str::uuid()->toString();
            $registrationId = Str::uuid()->toString();
            $hashedPassword = Hash::make($validated['password']);
            $domainSlug = Str::slug($validated['company_name']) . '-' . Str::random(4);

            DB::beginTransaction();

            DB::table('tenant_registrations')->insert([
                'id' => $registrationId,
                'company_name' => $validated['company_name'],
                'email' => $validated['email'],
                'whatsapp' => $validated['whatsapp'],
                'password' => $hashedPassword,
                'status' => 'pending',
                'subscription_tier' => 'basic',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tenants')->insert([
                'id' => $tenantId,
                'company_name' => $validated['company_name'],
                'domain_slug' => $domainSlug,
                'subscription_tier' => 'basic',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->insert([
                'id' => $userId,
                'tenant_id' => $tenantId,
                'name' => $validated['company_name'],
                'email' => $validated['email'],
                'password' => $hashedPassword,
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'registration_id' => $registrationId,
                    'tenant_id' => $tenantId,
                    'company_name' => $validated['company_name'],
                    'domain_slug' => $domainSlug,
                    'status' => 'pending',
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function verify(string $id): JsonResponse
    {
        try {
            $registration = DB::table('tenant_registrations')->where('id', $id)->first();
            if (!$registration) {
                return response()->json(['status' => 'error', 'message' => 'Registration not found'], 404);
            }
            if ($registration->status !== 'pending') {
                return response()->json(['status' => 'error', 'message' => 'Registration already processed'], 409);
            }

            $tenantId = Str::uuid()->toString();
            $userId = Str::uuid()->toString();
            $domainSlug = Str::slug($registration->company_name) . '-' . Str::random(4);

            DB::beginTransaction();

            DB::table('tenant_registrations')->where('id', $id)->update([
                'status' => 'approved',
                'updated_at' => now(),
            ]);

            DB::table('tenants')->insert([
                'id' => $tenantId,
                'company_name' => $registration->company_name,
                'domain_slug' => $domainSlug,
                'subscription_tier' => 'basic',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->insert([
                'id' => $userId,
                'tenant_id' => $tenantId,
                'name' => $registration->company_name,
                'email' => $registration->email,
                'password' => $registration->password,
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'tenant_id' => $tenantId,
                    'company_name' => $registration->company_name,
                    'domain_slug' => $domainSlug,
                    'status' => 'approved',
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}