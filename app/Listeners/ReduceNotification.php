<?php

namespace App\Listeners;

use App\Events\PriceReduce;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

class ReduceNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PriceReduce  $event
     *
     * @return bool
     */
    public function handle(PriceReduce $event)
    {
        $sku      = $event->sku;
        $title    = $event->title;
        $oldPrice = $event->oldPrice;
        $newPrice = $event->newPrice;


        $token = Cache::get('WechatSystemToken');
        if (!$token) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_SECRET');
            $tokenJson = file_get_contents($url);
            $tokenArr = json_decode($tokenJson, true);
            if (isset($tokenArr['access_token'])) {
                $token = $tokenArr['access_token'];
                Cache::put('WechatSystemToken', $token, 3600);
            } else {
                exec("echo {$sku} {$title} {$oldPrice} {$newPrice} token failed {$tokenJson} >> /srv/laravel/storage/logs/test.log");
                return false;
            }
        }

        $ch      = curl_init();
        $options = array(
            CURLOPT_URL => "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}",

            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => '{
    "touser": "ozE35t03dFodihB-Bgba1o5oarGM",
    "template_id": "S_rH5w7WOOfvij-wnN-OuFx6xhTQj2ylc4aKq_WIdU4",
    "url": "https://icp0.com",
    "topcolor": "#FF0000",
    "data": {
        "sku": {
            "value": "'.$sku.'",
            "color": "#173177"
        },
        "title": {
            "value": "'.$title.'",
            "color": "#173177"
        },
        "oldPrice": {
            "value": "'.$oldPrice.'",
            "color": "#173177"
        },
        "newPrice": {
            "value": "'.$newPrice.'",
            "color": "#FF0000"
        }
    }
}',
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_AUTOREFERER    => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER         => false,
        );

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        $arr = json_decode($result, true);
        if (!isset($arr['errcode']) || $arr['errcode'] != 0) {
            exec("echo {$sku} {$title} {$oldPrice} {$newPrice} token failed {$result} >> /srv/laravel/storage/logs/test.log");
        }

        return true;
    }
}
