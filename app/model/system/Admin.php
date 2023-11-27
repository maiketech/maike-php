<?php

namespace app\model\system;

use app\model\BaseModel;
use think\model\concern\SoftDelete;
use maike\util\DateTimeUtil;
use maike\trait\JwtAuthModelTrait;

class Admin extends BaseModel
{
	use SoftDelete;
	use JwtAuthModelTrait;

	protected $pk = 'admin_id';
	protected $with = ['role'];
	protected $append = ['status_desc', 'not_allow_view', 'not_allow_edit', 'not_allow_delete'];

	public function setPasswordAttr($value)
	{
		if (!empty($value)) {
			return password_hash($value, PASSWORD_DEFAULT);
		}
	}

	public function getStatusDescAttr($value, $data)
	{
		return isset($data['status']) && $data['status'] == 1 ? '正常' : '禁用';
	}

	public function getAvatarAttr($value)
	{
		return empty($value) ? BaseUrl() . 'static/image/avatar_default.png' : $value;
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

	public function getNotAllowViewAttr($value, $data)
	{
		return isset($data['is_super']) && $data['is_super'] == 1;
	}

	public function getNotAllowEditAttr($value, $data)
	{
		return isset($data['is_super']) && $data['is_super'] == 1;
	}

	public function getNotAllowDeleteAttr($value, $data)
	{
		return isset($data['is_super']) && $data['is_super'] == 1;
	}

	public static function getUserIdByName($name)
	{
		return (new static)->where('name', 'like', '%' . $name . '%')->column("user_id");
	}

	public function role()
	{
		return $this->belongsTo("AdminRole", "role_id")->bind(['role_name', 'actions', 'auths', 'role_status' => 'status']);
	}
}
