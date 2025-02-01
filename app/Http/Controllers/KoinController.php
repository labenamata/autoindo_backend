<?php
namespace App\Http\Controllers;

use App\Models\Koin;
use Illuminate\Support\Facades\Http;

class KoinController extends Controller
{
    public function fetchAndSavePairs()
    {
        // Fetch data from Indodax API
        $response = Http::get('https://indodax.com/api/pairs');

        if ($response->successful()) {
            $pairs = $response->json();

            foreach ($pairs as $pairData) {
                Koin::updateOrCreate(
                    ['koin_id' => $pairData['id']], // Unique identifier
                    [
                        'koin_id'  => $pairData['id'],
                        'name'     => $pairData['traded_currency_unit'],
                        'currency' => $pairData['base_currency'],
                        'image'    => $pairData['url_logo_png'],
                        'fee'      => $pairData['trade_fee_percent_maker'],
                        'ticker'   => $pairData['ticker_id'],
                    ]
                );
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Trading pairs saved successfully!',
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to fetch trading pairs',
        ]);
    }

    public function getAllKoins()
    {
        try {
            // Fetch all records from the Koin model
            $koins = Koin::select('koin_id', 'name', 'currency', 'image', 'fee', 'ticker')->get();

            return response()->json([
                'success' => true,
                'data'    => $koins,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getKoin($currency)
    {

        $data = Koin::where('currency', $currency)->get();

        return response()->json($data);
    }

    public function filterKoin($currency, $name)
    {
        $data = Koin::where('currency', $currency)
            ->Where('name', 'like', '%' . $name . '%')->get();

        return response()->json($data);
    }
}
