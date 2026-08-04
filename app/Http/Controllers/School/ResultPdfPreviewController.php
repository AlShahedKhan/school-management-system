<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ResultPdfPreviewController extends Controller
{
    public function __invoke(string $token): View
    {
        $payload = Cache::pull('transcript-pdf:'.$token);

        abort_unless(
            is_array($payload)
                && isset($payload['user_id'])
                && is_array($payload['result'] ?? null),
            404
        );

        abort_unless(Auth::onceUsingId((int) $payload['user_id']), 404);

        return view('school.exam.result_find', [
            'pdfResultData' => $payload['result'],
        ]);
    }
}
