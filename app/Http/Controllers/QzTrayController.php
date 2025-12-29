<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QzTrayController extends Controller
{
    public function certificate()
    {
        $certificate = $this->loadCertificate();

        return response()->make($certificate, 200, ['Content-Type' => 'text/plain']);
    }

    public function sign(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|string',
        ]);

        $keyContents = $this->loadPrivateKey();
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

    private function loadCertificate(): string
    {
        $base64 = config('qz.certificate_base64');
        if ($base64) {
            $decoded = base64_decode($base64, true);
            if ($decoded === false) {
                abort(500, 'QZ certificate base64 is invalid.');
            }
            return $decoded;
        }

        $raw = config('qz.certificate_raw');
        if ($raw) {
            return str_replace("\\n", "\n", $raw);
        }

        $path = config('qz.certificate_path');
        if ($path && is_file($path)) {
            return file_get_contents($path);
        }

        abort(404, 'QZ certificate not found.');
    }

    private function loadPrivateKey(): string
    {
        $base64 = config('qz.private_key_base64');
        if ($base64) {
            $decoded = base64_decode($base64, true);
            if ($decoded === false) {
                abort(500, 'QZ private key base64 is invalid.');
            }
            return $decoded;
        }

        $raw = config('qz.private_key_raw');
        if ($raw) {
            return str_replace("\\n", "\n", $raw);
        }

        $path = config('qz.private_key_path');
        if ($path && is_file($path)) {
            return file_get_contents($path);
        }

        abort(500, 'QZ private key not configured.');
    }
}
