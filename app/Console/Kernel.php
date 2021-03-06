<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\FetchAmazonSkus::class,
        Commands\AddSourceSku::class,
        Commands\AmazonSkuSpider::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command(Commands\FetchAmazonSkus::class)->everyMinute()->withoutOverlapping()->appendOutputTo('/srv/laravel/storage/logs/amazon-fetch-'.date("Y-m-d").'.log');
         $schedule->command(Commands\AmazonSkuSpider::class)->everyMinute()->withoutOverlapping()->appendOutputTo('/srv/laravel/storage/logs/amazon-sku-spider-'.date("Y-m-d").'.log');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
