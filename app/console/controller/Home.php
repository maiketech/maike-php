<?php

namespace app\console\controller;

use app\model\system\Action as ActionModel;
use app\model\system\Setting as SettingModel;
use app\model\fee\OrderPay;
use app\model\fee\Order;
use app\model\wuye\Owner;
use app\model\wuye\Store;

class Home extends Base
{
    public function total()
    {
        $data = [
            'today_pay_money' => OrderPay::getSum("total_money", [['pay_time', '=', 'today']]),
            'yesterday_pay_money' => OrderPay::getSum("total_money", [['pay_time', '=', 'yesterday']]),

            'pay_order_count' => Order::getCount(['pay_status' => 1, 'status' => 2]),
            'wait_pay_order_count' => Order::getCount(['pay_status' => 0, 'status' => 1]),
            'order_count' => Order::getCount(),

            'store_count' => Store::getCount(),
            'stop_store_count' => Store::getCount(['status' => 0]),

            'owner_bind_count' => Owner::getCount([['user_id', '>', 0]]),
            'owner_count' => Owner::getCount()
        ];
        return $this->success($data);
    }

    public function order_pay()
    {
        $data = OrderPay::getLast();
        return $this->success($data);
    }

    public function chart()
    {
        return $this->success();
    }

    public function clear_cache()
    {
        (new ActionModel)->deleteAllCache();
        (new SettingModel)->deleteAllCache();
        return $this->success(null, "缓存清除成功");
    }
}
