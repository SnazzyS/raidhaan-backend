<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QzTrayController extends Controller
{
    public function certificate()
    {
        $path = config('qz.certificate_path');

        if (!$path || !is_file($path)) {
            abort(404, 'QZ certificate not found.');
        }

        return response()->make(
            file_get_contents($path),
            200,
            ['Content-Type' => 'text/plain']
        );
    }

    public function sign(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|string',
        ]);

        $keyPath = config('qz.private_key_path');

        if (!$keyPath || !is_file($keyPath)) {
            abort(500, 'QZ private key not configured.');
        }

        $keyContents = file_get_contents($keyPath);
        $passphrase = config('qz.private_key_passphrase');

        if ($passphrase !== null && $passphrase !== '') {
            $privateKey = openssl_pkey_get_private($keyContents, $passphrase);
        } else {
            $privateKey = openssl_pkey_get_private($keyContents);
        }

        if ($privateKey === false) {
            abort(500, 'Unable to load QZ private key.');
        }

        $signature = '';
        $success = openssl_sign($validated['data'], $signature, $privateKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKey);

        if (!$success) {
            abort(500, 'Unable to sign QZ request.');
        }

        return response()->json([
            'signature' => base64_encode($signature),
        ]);
    }
}
