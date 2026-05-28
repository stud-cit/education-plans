<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CabinetController extends Controller
{
    private const INFO = 'Інформаційний сервіс «Каталог навчальних планів» дозволить Вам зручно створювати нові навчальні плани.';
    private const ICON = 'service.png';
    private const MASK = 13;

    public function index(Request $request)
    {
        return match ((int) $request->query('mode', 0)) {
            0 => $this->redirect($request),
            2 => $this->icon(),
            3 => $this->info(),
            100 => $this->supportHeader(),
            default => response()->noContent(),
        };
    }

    private function redirect(Request $request): RedirectResponse
    {
        $key = $request->query('key');

        if ($key) {
            return redirect(config('app.url') . '/?key=' . $key);
        }

        return redirect(config('app.url') . '/');
    }

    private function icon(): BinaryFileResponse
    {
        return response()->file(public_path(self::ICON));
    }

    private function info(): Response
    {
        return response(self::INFO)->header('Content-Type', 'text/plain');
    }

    private function supportHeader(): Response
    {
        return response()->noContent()->header('X-Cabinet-Support', self::MASK);
    }
}