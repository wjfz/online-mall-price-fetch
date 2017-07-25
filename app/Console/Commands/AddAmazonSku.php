<?php

namespace App\Console\Commands;

use App\AmazonSku;
use Illuminate\Console\Command;

class AddAmazonSku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amazon:addSku {sku}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sku = $this->argument('sku');
        AmazonSku::create(['sku' => $sku]);

        echo "$sku add success.\n";
        return true;
    }
}
