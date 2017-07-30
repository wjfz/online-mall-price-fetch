<?php

namespace App\Events;

//use Illuminate\Broadcasting\Channel;
//use Illuminate\Queue\SerializesModels;
//use Illuminate\Broadcasting\PrivateChannel;
//use Illuminate\Foundation\Events\Dispatchable;
//use Illuminate\Broadcasting\InteractsWithSockets;

class PriceReduce
{
//    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sku;
    public $title;
    public $newPrice;
    public $oldPrice;

    /**
     * PriceReduce constructor.
     *
     * @param $sku
     * @param $title
     * @param $newPrice
     *
     */
    public function __construct($sku, $title, $oldPrice, $newPrice)
    {
        $this->sku      = $sku;
        $this->title    = $title;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
    }

//    /**
//     * Get the channels the event should broadcast on.
//     *
//     * @return Channel|array
//     */
//    public function broadcastOn()
//    {
//        return new PrivateChannel('wjfz-channel');
//    }
}
