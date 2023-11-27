<?php

namespace app\enum;

enum PayStatus: int
{
    case WAIT = 0;
    case SUCCESS = 1;

    // 付款状态(0未付款/待付款 1已付款)

    public function text(): string
    {
        return match ($this) {
            PayStatus::WAIT => '待付款',
            PayStatus::SUCCESS => '已付款'
        };
    }

    public function color(): string
    {
        return match ($this) {
            PayStatus::WAIT => '#FF951A',
            PayStatus::SUCCESS => '#00B767'
        };
    }

    public static function list(): array
    {
        $cases = PayStatus::cases();
        $list = [];
        foreach ($cases as $item) {
            $name = $item->name;
            $list[$name] = [
                'name' => $name,
                'text' => $item->text(),
                'color' => $item->color(),
                'value' => $item->value
            ];
        }
        return $list;
    }
}
