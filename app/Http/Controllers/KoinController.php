<?php
namespace App\Http\Controllers;

use App\Helpers\TradeHelper;
use App\Models\Bstate;
use App\Models\Jaring;
use App\Models\Koin;
use App\Models\notif;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

    public function kodeTrade()
    {

        $record = Bstate::where('state', 'on')->
            with('kredentials.jarings.pairs')->get();
        //return response()->json($record);
        foreach ($record as $users) {
            $key    = $users->kredentials->key;
            $secret = $users->kredentials->secret;
            foreach ($users->kredentials->jarings as $jarings) {
                $buypair  = 'receive_' . Str::lower($jarings->pairs->name);
                $sellpair = 'receive_' . Str::lower($jarings->pairs->currency);
                if ($jarings->pairs->currency == 'idr') {
                    $currency = 'idr';
                } else {
                    $currency = 'usdt';
                }
                if ($jarings->status == 'pending') {
                    $data = [
                        'method'     => 'trade',
                        'pair'       => $jarings->pairs->ticker,
                        'type'       => 'buy',
                        'price'      => $jarings->buy,
                        $currency    => $jarings->modal,
                        'timestamp'  => '1578304294000',
                        'recvWindow' => '1578303937000',
                    ];

                    $response = TradeHelper::sendServer($key, $secret, $data);
                    if ($response['success'] == 1) {
                        jaring::where('id', $jarings->id)->update([
                            'order_id' => $response['return']['order_id'],

                            'status'   => 'buy',
                        ]);
                        notif::create([
                            'email'        => $users->email,
                            'read'         => 'no',
                            'notification' => 'Order buy ' . $jarings->pairs->name . ' ' . $jarings->modal . ' placed',
                        ]);
                    } else {
                        notif::create([
                            'email'        => $users->email,
                            'read'         => 'no',
                            'notification' => $response['error'],
                        ]);
                    }
                } else {
                    $data = [
                        'method'     => 'getOrder',
                        'pair'       => $jarings->pairs->ticker,
                        'order_id'   => $jarings->order_id,
                        'timestamp'  => '1578304294000',
                        'recvWindow' => '1578303937000',
                    ];
                    $response = TradeHelper::sendServer($key, $secret, $data);
                    if ($response['success'] == 1) {
                        if ($response['return']['order']['status'] == 'filled') {

                            if ($jarings->status == 'buy') {
                                $get  = $response['return']['order'][$buypair];
                                $data = [
                                    'method'                          => 'trade',
                                    'pair'                            => $jarings->pairs->ticker,
                                    'type'                            => 'sell',
                                    'price'                           => $jarings->sell,
                                    Str::lower($jarings->pairs->name) => $get,
                                    'timestamp'                       => '1578304294000',
                                    'recvWindow'                      => '1578303937000',
                                ];
                                $response = TradeHelper::sendServer($key, $secret, $data);
                                if ($response['success'] == 1) {
                                    jaring::where('id', $jarings->id)->update([
                                        'order_id' => $response['return']['order_id'],
                                        'status'   => 'sell',
                                    ]);
                                    notif::create([
                                        'email'        => $users->email,
                                        'read'         => 'no',
                                        'notification' => 'Order sell ' . $jarings->pairs->name . ' ' . $get . ' placed',
                                    ]);
                                } else {
                                    notif::create([
                                        'email'        => $users->email,
                                        'read'         => 'no',
                                        'notification' => $response['error'],
                                    ]);
                                }
                            } else {
                                $get       = $response['return']['order'][$sellpair];
                                $hasilJual = $get - $jarings->modal;
                                $profit    = $jarings->profit + $hasilJual;
                                $data      = [
                                    'method'     => 'trade',
                                    'pair'       => $jarings->pairs->ticker,
                                    'type'       => 'buy',
                                    'price'      => $jarings->buy,
                                    $currency    => $jarings->modal,
                                    'timestamp'  => '1578304294000',
                                    'recvWindow' => '1578303937000',
                                ];

                                $response = TradeHelper::sendServer($key, $secret, $data);
                                if ($response['success'] == 1) {
                                    jaring::where('id', $jarings->id)->update([
                                        'order_id' => $response['return']['order_id'],
                                        'profit'   => $profit,
                                        'status'   => 'buy',
                                    ]);
                                    notif::create([
                                        'email'        => $users->email,
                                        'read'         => 'no',
                                        'notification' => 'Order buy ' . $jarings->pairs->name . ' ' . $jarings->modal . ' placed',
                                    ]);
                                } else {
                                    notif::create([
                                        'email'        => $users->email,
                                        'read'         => 'no',
                                        'notification' => $response['error'],
                                    ]);
                                }
                            }
                        }

                    } else {
                        notif::create([
                            'email'        => $users->email,
                            'read'         => 'no',
                            'notification' => $response['error'],
                        ]);
                    }
                }
                sleep(3);
            }
        }
    }
}
