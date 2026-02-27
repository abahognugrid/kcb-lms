<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Partner;
use Illuminate\Http\Request;
use App\Models\PartnerApiSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearer_token = $request->bearerToken();

        if (!$bearer_token) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $partnerCode = $request->header('X-PARTNER-CODE');
        if (!$partnerCode) {
            return response()->json([
                'message' => 'Unauthorized. Your secure partner code is required.'
            ], 401);
        }

        $partner = Partner::where('Identification_Code', $partnerCode)->first();

        if (!$partner) {
            $message = 'Unauthorized. Invalid partner code.';
            Log::info($message);
            return response()->json([
                'message' => $message
            ], 401);
        }

        $partner_token = PartnerApiSetting::where('partner_id', $partner->id)->first();

        if (!$partner_token) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        if ($partner_token->isExpired()) {
            return response()->json([
                'message' => 'Unauthorized. API key has expired.'
            ], 401);
        }

        // No need to decrypt manually, as it happens in the accessor
        // Decryption is happening when we get the attribute in the model. See getApiKeyAttribute.
        $decrypted_token = $partner_token->api_key;
        if ($decrypted_token != $bearer_token) {
            return response()->json([
                'message' => 'Invalid API key.',
                'status' => false,
                'partner_code' => $partnerCode,
                'bearer_token' => $bearer_token,
            ], 401);
        }

        $partner_token->last_used_at = now();
        $partner_token->has_been_used = true;
        $partner_token->save();

        $request->merge(['partner_id' => $partner->id]);

        return $next($request);
    }
}
