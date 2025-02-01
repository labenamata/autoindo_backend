<?php
namespace App\Helpers;

use App\Models\jaring;
use App\Models\notif;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TradeHelper
{
    public static function sendServer(
        string $key,
        string $secretKey,
        array $data
    ) {
        $url = 'https://indodax.com/tapi';

        // $key = 'PMFS6SWY-L77DGDUY-LH58H6NJ-CHZQL2YQ-CJNFS4QZ';
        // $secretKey = '801eba2a35851b32887aadbc76d34c9e2714facf68248ecc6580b9d4301484532b355447d75f953d';

        $post_data = http_build_query($data, '', '&');
        $sign = hash_hmac('sha512', $post_data, $secretKey);
        $headers = ['Key' => $key, 'Sign' => $sign];
        $response = Http::accept('application/x-www-form-urlencoded')->withHeaders($headers)->asForm()->post($url, $data);
        return $response;
    }

    // public static function autoTrade()
    // {
    //     $mytime = Carbon::parse(Carbon::now())->format('Y-m-d H:i:s');

    //     $jaring = jaring::with('pair')->get();
    //     foreach ($jaring as $jarings) {
    //         if ($jarings->pair->currency == 'idr') {
    //             $currency = 'idr';
    //         } else {
    //             $currency = 'usdt';
    //         }
    //         if ($jarings->status == 'pending') {
    //             $data = [
    //                 'method' => 'trade',
    //                 'pair' => $jarings->pair->ticker,
    //                 'type' => 'buy',
    //                 'price' => $jarings->buy,
    //                 $currency => $jarings->modal,
    //                 'timestamp' => '1578304294000',
    //                 'recvWindow' => '1578303937000',
    //             ];

    //             $response = TradeHelper::sendServer($data);
    //             if ($response['success'] == 1) {
    //                 jaring::where('id', $jarings->id)->update([
    //                     'order_id' => $response['return']['order_id'],
    //                     'status' => 'buy',
    //                 ]);
    //                 notif::create([
    //                     'time' => $mytime,
    //                     'message' => 'Order buy ' . $jarings->pair->name . ' ' . $jarings->modal . ' placed',
    //                 ]);
    //             } else {
    //                 notif::create([
    //                     'time' => $mytime,
    //                     'message' => $response['error'],
    //                 ]);
    //             }
    //         } else {
    //             $data = [
    //                 'method' => 'getOrder',
    //                 'pair' => $jarings->pair->ticker,
    //                 'order_id' => $jarings->order_id,
    //                 'timestamp' => '1578304294000',
    //                 'recvWindow' => '1578303937000',
    //             ];
    //             $response = TradeHelper::sendServer($data);
    //             if ($response['success'] == 1) {
    //                 if ($response['return']['order']['status'] == 'filled') {
    //                     $get = last($response['return']['order']);
    //                     if ($jarings->status == 'buy') {

    //                         $data = [
    //                             'method' => 'trade',
    //                             'pair' => $jarings->pair->ticker,
    //                             'type' => 'sell',
    //                             'price' => $jarings->sell,
    //                             Str::lower($jarings->pair->name) => $get,
    //                             'timestamp' => '1578304294000',
    //                             'recvWindow' => '1578303937000',
    //                         ];
    //                         $response = TradeHelper::sendServer($data);
    //                         if ($response['success'] == 1) {
    //                             jaring::where('id', $jarings->id)->update([
    //                                 'order_id' => $response['return']['order_id'],
    //                                 'status' => 'sell',
    //                             ]);
    //                             notif::create([
    //                                 'time' => $mytime,
    //                                 'message' => 'Order sell ' . $jarings->pair->name . ' ' . $get . ' placed',
    //                             ]);
    //                         } else {
    //                             notif::create([
    //                                 'time' => $mytime,
    //                                 'message' => $response['error'],
    //                             ]);
    //                         }
    //                     } else {
    //                         $hasilJual = $get - $jarings->modal;
    //                         $profit = $jarings->profit + $hasilJual;
    //                         $data = [
    //                             'method' => 'trade',
    //                             'pair' => $jarings->pair->ticker,
    //                             'type' => 'buy',
    //                             'price' => $jarings->buy,
    //                             $currency => $jarings->modal,
    //                             'timestamp' => '1578304294000',
    //                             'recvWindow' => '1578303937000',
    //                         ];

    //                         $response = TradeHelper::sendServer($data);
    //                         if ($response['success'] == 1) {
    //                             jaring::where('id', $jarings->id)->update([
    //                                 'order_id' => $response['return']['order_id'],
    //                                 'profit' => $profit,
    //                                 'status' => 'buy',
    //                             ]);
    //                             notif::create([
    //                                 'time' => $mytime,
    //                                 'message' => 'Order buy ' . $jarings->pair->name . ' ' . $jarings->modal . ' placed',
    //                             ]);
    //                         } else {
    //                             notif::create([
    //                                 'time' => $mytime,
    //                                 'message' => $response['error'],
    //                             ]);
    //                         }
    //                     }
    //                 }

    //             } else {
    //                 notif::create([
    //                     'time' => $mytime,
    //                     'message' => $response['error'],
    //                 ]);
    //             }
    //         }
    //         sleep(5);
    //     }
    // }

}
