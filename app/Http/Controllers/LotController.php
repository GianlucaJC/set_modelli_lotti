<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LotController extends Controller
{
    private const CIFF_MODELS = [
        'PiastraRID.ciff' => 'EasyRID Piastra',
        'BustaRID.cif' => 'EasyRID Busta',
    ];

    public function edit(): View
    {
        return view('lots.edit', ['ciffModels' => self::CIFF_MODELS]);
    }

    public function currentRecipe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lot_number' => ['required', 'string', 'max:255'],
        ]);

        $recipe = DB::table('impegnolotti')
            ->where('DBlotto', $data['lot_number'])
            ->whereNotNull('DBmodelli')
            ->whereRaw('TRIM(DBmodelli) <> ?', [''])
            ->value('DBmodelli');

        return response()->json(['recipe' => $recipe]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lot_number' => ['required', 'string', 'max:255'],
            'ciff_model' => ['required', 'in:'.implode(',', array_keys(self::CIFF_MODELS))],
        ]);

        $username = (string) $request->session()->get('set_modelli_user.username');
        $setBy = substr($username, 0, 14).' '.now()->format('H:i');

        $updatedLots = DB::table('impegnolotti')
            ->where('DBlotto', $data['lot_number'])
            ->update([
                'DBmodelli' => $data['ciff_model'],
                'set_db_modelli' => $setBy,
            ]);

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