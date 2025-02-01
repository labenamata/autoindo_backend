<?php
namespace App\Console\Commands;

use App\Helpers\TradeHelper;
use App\Models\Bstate;
use App\Models\Jaring;
use App\Models\notif;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TradeJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trade:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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

        return Command::SUCCESS;
    }
}
