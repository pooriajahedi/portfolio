<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class ContactRequestApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            'company_website' => ['nullable', 'string', 'max:1'],
        ], [
            'required' => ':attribute الزامی است.',
            'email' => 'فرمت :attribute معتبر نیست.',
            'min.string' => ':attribute باید حداقل :min کاراکتر باشد.',
            'max.string' => ':attribute نباید بیشتر از :max کاراکتر باشد.',
        ], [
            'name' => 'نام',
            'email' => 'ایمیل',
            'subject' => 'موضوع',
            'message' => 'پیام',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'اعتبارسنجی ناموفق بود.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (filled((string) $request->input('company_website'))) {
            return response()->json([
                'message' => 'ارسال نامعتبر شناسایی شد.',
            ], 422);
        }

        $rateKey = 'contact-request:' . sha1((string) $request->ip());

        if (RateLimiter::tooManyAttempts($rateKey, 4)) {
            return response()->json([
                'message' => 'درخواست‌های پیاپی زیاد بود. لطفا کمی بعد دوباره تلاش کنید.',
            ], 429);
        }

        RateLimiter::hit($rateKey, 60);

        ContactRequest::query()->create([
            'name' => trim((string) $request->input('name')),
            'email' => trim((string) $request->input('email')),
            'subject' => trim((string) $request->input('subject')),
            'message' => trim((string) $request->input('message')),
            'ip_address' => (string) $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'پیام شما با موفقیت ثبت شد.',
        ]);
    }
}
