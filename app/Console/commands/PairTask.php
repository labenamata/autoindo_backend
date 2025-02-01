<?php
namespace App\Console\Commands;

use App\Models\Koin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PairTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pair:task';

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

        }

    }
}
