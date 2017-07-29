# online-mall-price-fetch
网上商城价格抓取，基于laravel 5.4

目前只能抓取亚马逊

# 使用姿势

### 先配置项目。

* 执行`composer install`
* 配置 .env 里的数据库信息
* 执行 `php artisan migrate` 迁移数据

### 添加sku（暂）

`php artisan skus:add {source} {sku}`

如：

`skus:add amazon B071XX5315`



### 服务器配置
添加到crontab

    # Laravel任务调度
    * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1

`/path/to/artisan` 你的项目artisan路径