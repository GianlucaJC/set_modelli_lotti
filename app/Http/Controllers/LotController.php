<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LotController extends Controller
{
    private const CIFF_MODELS = [
        'internorid.ciff',
        'esterno rid.ciff',
        'enteropluri.ciff',
    ];

    public function edit(): View
    {
        return view('lots.edit', ['ciffModels' => self::CIFF_MODELS]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lot_number' => ['required', 'string', 'max:255'],
            'ciff_model' => ['required', 'in:'.implode(',', self::CIFF_MODELS)],
        ]);

        $updatedLots = DB::table('impegnolotti')
            ->where('DBlotto', $data['lot_number'])
            ->update(['DBmodelli' => $data['ciff_model']]);

        if ($updatedLots === 0) {
            return back()
                ->withInput()
                ->withErrors(['lot_number' => 'Lotto non trovato.']);
        }

        return redirect()
            ->route('lots.edit')
            ->with('status', 'Modello CIFF impostato per il lotto '.$data['lot_number'].'.');
    }
}