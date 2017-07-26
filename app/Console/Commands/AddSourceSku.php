<?php

namespace App\Console\Commands;

use App\Sku;
use Illuminate\Console\Command;

class AddSourceSku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skus:add  {source} {sku}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'skus:add {source} {sku}
                       skus:add amazon B071XX5315';

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
        $source = $this->argument('source');
        $sku    = $this->argument('sku');

        $saved = Sku::addSourceSku($source, $sku);

        if ($saved) {
            echo "$sku add success.\n";
        } else {
            echo "$sku add failed.\n";
        }

        return true;
    }
}
