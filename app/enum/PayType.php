<?php

namespace app\enum;

enum PayType: string
{
    case WECHAT = 'wechat';
    case ALIPAY = 'alipay';
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    case OFFLINE_WECHAT = 'offline_wechat';
    case OFFLINE_ALIPAY = 'offline_alipay';
    case OTHER = 'other';

    // 支付方式：wechat微信支付、 alipay支付宝、cash现金、transfer银行转账、offline_wechat线下微信、offline_alipay线下支付宝、other其他

    public function text(): string
    {
        return match ($this) {
            PayType::WECHAT => '微信支付',
            PayType::ALIPAY => '支付宝',
            PayType::CASH => '现金',
            PayType::TRANSFER => '银行转账',
            PayType::OFFLINE_WECHAT => '线下微信',
            PayType::OFFLINE_ALIPAY => '线下支付宝',
            PayType::OTHER => '其他'
        };
    }

    public static function list(): array
    {
        $cases = PayType::cases();
        $list = [];
        foreach ($cases as $item) {
            $name = $item->name;
            $list[$name] = [
                'name' => $name,
                'text' => $item->text(),
                'value' => $item->value
            ];
        }
        return $list;
    }
}
