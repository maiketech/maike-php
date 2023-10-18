<?php

namespace app\model\system;

use app\model\BaseModel;
use think\Exception;
use think\model\concern\SoftDelete;
use think\facade\Request;
use think\facade\Config;
use maike\util\Arr;
use maike\util\DT;
use maike\trait\JwtAuthModelTrait;

class Admin extends BaseModel
{
	use SoftDelete;
	use JwtAuthModelTrait;

	protected $pk = 'user_id';
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
		return empty($value) ? BaseUrl() . 'console/image/avatar_default.png' : $value;
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
		return !empty($value) && $value != 0 ? DT::Format($value) : '';
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

	/**
	 * 生成菜单
	 * @return array
	 */
	public function getMenuData($userInfo)
	{
		$menu = Action::getMenuData();
		if (!$menu || is_null($menu) || empty($menu)) return [];
		$actionList = $userInfo['actions'];
		$isSuper = isset($userInfo['is_super']) && $userInfo['is_super'] == 1 ? $userInfo['is_super'] : 0;

		if ($isSuper != 1) {
			if ($actionList != null && !empty($actionList)) {
				$temp = [];
				foreach ($menu as $item) {
					if ($item['required'] == 1 || empty($item['action']) || in_array($item['action'], $actionList)) {
						$temp[] = $item;
					}
				}
				$menu = $temp;
			}
		}

		$menu = Arr::ToTree((array)$menu, 0, "action_id", "pid", "sub");
		$new = [];
		foreach ($menu as $k => $item) {
			$newItem = [
				'key' => $item['action_id'],
				'auth' => $item['auth'],
				'title' => $item['title'],
				'icon' => $item['icon'],
				'route' => $item['route'],
				'sub' => []
			];
			if (isset($item['sub']) && count($item['sub']) > 0) {
				foreach ($item['sub'] as $sub) {
					$newItem['sub'][] = [
						'key' => $sub['action_id'],
						'auth' => $sub['auth'],
						'title' => $sub['title'],
						'icon' => $sub['icon'],
						'route' => $sub['route']
					];
				}
			}
			if ((isset($newItem['sub']) && !empty($newItem['sub']) && count($newItem['sub']) > 0) || $item['required'] == 1 || $item['level'] == 1) {
				if (count($newItem['sub']) > 0) {
					$newItem['route'] = $newItem['sub'][0]['route'];
				}
				$new[] = $newItem;
			}
		}
		return $new;
	}

	/**
	 * 微信一键登录
	 *
	 * @param array $params
	 * @return mixed
	 */
	public function wxlogin($params)
	{
		try {
			$config = Config::get("wechat.app");
			if (!$config || empty($config)) {
				throw new Exception("登录失败");
			}

			$userInfo = false;
			$app = new WechatApp($config);
			$session = $app->code2session($params['code']);

			if (!$session) {
				throw new Exception("登录失败");
			}
			$userInfo = [
				'openid' => $session['openid'],
				'unionid' => isset($session['unionid']) ? $session['unionid'] : ''
			];

			$userId = 0;
			$user = false;
			$userId = UserOauth::getUserIdByOauthId($userInfo['openid']);

			if ($userId > 0) {
				$user = static::get($userId);
				if ($user && $user['status'] == 0) {
					throw new Exception("账号已被禁用");
				}
			}

			$oauthData = [];
			if (!$user || empty($user)) {
				//新用户注册                
				$this->startTrans();
				try {
					$oauthData = UserOauth::getByOauthId($userInfo['openid']);
					if (!$oauthData) {
						// 新增用户记录
						$oauthData = [
							'user_id' => 0,
							'oauth_id' => $userInfo['openid'],
							'unionid' => $userInfo['unionid'],
							'oauth_type' => 'wechat'
						];
						UserOauth::create($oauthData);
					}

					$this->commit();
					return $oauthData;
				} catch (\Exception $e) {
					$this->rollback();
					$this->setError($e->getMessage());
					return false;
				}
			}

			$user->tokenExpire = strtotime("+99 days");

			//生成Token
			$token = '';
			$tokenData = $user->createToken();
			if ($tokenData && isset($tokenData['token'])) {
				$token = $tokenData['token'];
			}

			//新增登录记录
			$loginLog = [
				'user_agent' => Request::header('user-agent'),
				'ip' => Request::ip(),
				'token' => $token,
				'user_id' => $user['user_id'],
				'expire_time' => time() + 86400 * 7
			];
			UserLogin::create($loginLog);

			return compact('user', 'token');
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}

	/**
	 * 账号密码登录
	 * @param array $post
	 * @return bool
	 */
	public function login($data, $oauthId = '')
	{
		try {
			if (empty($data['username']) || empty($data['password'])) {
				$this->setError('用户名及密码不能为空');
				return false;
			}
			$info = $this->get([['username|mobile', '=', $data['username']]]);
			if (!$info) {
				$this->setError('用户不存在');
				return false;
			}
			if (isset($info['role_status']) && $info['role_status'] !== 1) {
				$this->setError('用户被禁用，无法登录');
				return false;
			}
			if ($info['status'] !== 1) {
				$this->setError('用户被禁用，无法登录');
				return false;
			}

			if (!password_verify($data['password'], $info['password'])) {
				//密码错误
				$this->setError('密码错误');
				return false;
			}

			if (!empty($oauthId)) {
				//绑定账号
				$oauthInfo = UserOauth::get([['oauth_id', '=', $oauthId]]);
				if ($oauthInfo) {
					if (!$oauthInfo->save(['user_id' => $info['user_id']])) {
						$this->setError('绑定失败');
						return false;
					}
				} else {
					$this->setError('绑定失败');
					return false;
				}
			}

			$info->tokenExpire = strtotime("+99 days");

			//生成Token
			$token = '';
			$tokenData = $info->createToken();
			if ($tokenData && isset($tokenData['token'])) {
				$token = $tokenData['token'];
			}

			//新增登录记录
			$loginLog = [
				'user_agent' => Request::header('user-agent'),
				'ip' => Request::ip(),
				'token' => $token,
				'user_id' => $info['user_id'],
				'expire_time' => time() + 86400 * 7
			];
			UserLogin::create($loginLog);

			return compact('info', 'token');
		} catch (\think\Exception $e) {
			$this->setError('登录发生错误' . $e->getMessage());
			return false;
		}
	}

	public static function getUserIdByName($name)
	{
		return (new static)->where('name', 'like', '%' . $name . '%')->column("user_id");
	}

	public function role()
	{
		return $this->belongsTo("UserRole", "role_id")->bind(['role_name', 'actions', 'auths', 'role_status' => 'status']);
	}
}
