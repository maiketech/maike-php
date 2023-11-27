<?php

namespace app\model\user;

use think\model\concern\SoftDelete;
use maike\util\DateTimeUtil;
use maike\trait\JwtAuthModelTrait;
use app\model\BaseModel;

class User extends BaseModel
{
	use SoftDelete;
	use JwtAuthModelTrait;

	protected $pk = 'user_id';
	protected $append = ['status_text'];

	public function setPasswordAttr($value)
	{
		if (!empty($value)) {
			return password_hash($value, PASSWORD_DEFAULT);
		}
	}

	public function getStatusTextAttr($value, $data)
	{
		return isset($data['status']) && $data['status'] == 1 ? '正常' : '禁用';
	}

	public function getAvatarAttr($value)
	{
		return empty($value) ? baseUrl() . 'static/image/avatar_default.png' : $value;
	}

	public function getAuthsAttr($value, $data)
	{
		if (isset($data['is_super']) && $data['is_super'] == 1) return ['ALL'];
		return $value;
	}

	public function getActionsAttr($value, $data)
	{
		if (isset($data['is_super']) && $data['is_super'] == 1) return ['ALL'];
		return $value;
	}

	public function getLastLoginTimeAttr($value, $data)
	{
		return !empty($value) && $value != 0 ? DateTimeUtil::Format($value) : '';
	}

	public static function getUserIdByName($name)
	{
		return (new static)->where('name', 'like', '%' . $name . '%')->column("user_id");
	}
}
