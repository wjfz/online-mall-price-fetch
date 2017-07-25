# online-mall-price-fetch
网上商城价格抓取，基于laravel 5.4

目前只能抓取亚马逊

# 使用姿势

###先配置项目。

* 配置 .env 里的数据库信息
* 执行 `php artisan migrate` 迁移数据

### 两个命令

`php artisan amazon:addSku B071XX5315` 添加亚马逊商品sku

`php artisan amazon:fetch` 执行抓取

### 服务器配置
添加到crontab

    # 抓取亚马逊价格
    * * * * * cd /srv/laravel && /usr/local/bin/php artisan amazon:fetch >> /home/wjfz/amazon-fetch.log

`/www/laravel` 你的项目路径

`/usr/local/bin/php` 你的php路径

`/home/wjfz/amazon-fetch.log` 你的crontab日志想放在哪里