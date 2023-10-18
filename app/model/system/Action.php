<?php

namespace app\model\system;

use maike\util\Arr;
use app\model\BaseModel;

class Action extends BaseModel
{
	protected $pk = "action_id";

	/**
	 * 获取菜单数据
	 * @return array
	 */
	public static function getMenuData()
	{
		$data = static::getAllByCache();
		if ($data && count($data) > 0) {
			$menu = Arr::Search($data, "is_menu", 1);
			return Arr::Sort($menu, "sort");
		}
		return null;
	}

	/**
	 * 根据动作名返回动作表记录
	 * @param string $action
	 * @return array
	 */
	public static function getByAction($action)
	{
		if (!$action || empty($action)) return null;
		$data = static::getAllByCache();
		if ($data && count($data) > 0) {
			return Arr::Search($data, "action", $action);
		}
		return null;
	}

	/**
	 * 根据动作ID返回动作表记录
	 * @param integer|array $actionId
	 * @return array
	 */
	public static function getAllById($actionId)
	{
		$data = static::getAllByCache();
		$result = [];
		if (is_array($actionId)) {
			$result = Arr::Search($data, 'action_id', $actionId, "in");
		} else {
			$result = Arr::Search($data, 'action_id', $actionId);
		}
		return $result;
	}
}
