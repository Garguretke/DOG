<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function index()
    {
        return view('generator-qr.index');
    }

public function generate(Request $request)
    {
        // Definicja niestandardowego komunikatu walidacji
        $customMessages = [
            'content.required' => 'Zawartość nie może być pusta.',
        ];

        // Walidacja danych wejściowych z niestandardowymi komunikatami
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ], $customMessages);

        // Jeśli walidacja nie powiodła się, zwracamy komunikat błędu
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Jeśli dane zostały poprawnie zwalidowane, generujemy kod QR
        $content = $request->input('content');
        $qrCode = QrCode::format('png')->size(300)->generate($content);

        return view('generator-qr.index', compact('qrCode'));
    }
}
?>