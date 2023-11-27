<?php

namespace app\service\user;

use think\Exception;
use think\facade\Request;
use think\facade\Db;
use maike\wechat\WechatApp;
use maike\trait\ErrorTrait;
use app\model\user\User as UserModel;
use app\model\user\UserOauth;
use app\model\user\UserLogin;

class User
{
	use ErrorTrait;

	/**
	 * 用户名/手机号密码登录
	 * @param array $data
	 * @return bool
	 */
	public function login($data)
	{
		try {
			if (empty($data['username'])) {
				$this->setError('用户名/手机号不能为空');
				return false;
			}
			if (empty($data['password'])) {
				$this->setError('密码不能为空');
				return false;
			}
			$info = UserModel::get([['username|mobile', '=', $data['username']]]);
			if (!$info) {
				$this->setError('用户不存在');
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

			$info->tokenExpire = strtotime("+1 days");

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
				'expire_time' => $info->tokenExpire
			];
			UserLogin::create($loginLog);

			return ['userInfo' => $info->hidden(['password']), 'token' => $token, 'tokenExpire' => $info->tokenExpire];
		} catch (\think\Exception $e) {
			$this->setError('登录发生错误' . $e->getMessage());
			return false;
		}
	}

	/**
	 * 微信一键登录
	 *
	 * @param array $params
	 * @return mixed
	 */
	public function wechatLogin($params, $isGetMobile = false)
	{
		try {
			$userInfo = false;
			$userId = 0;
			$user = false;
			$mobile = '';

			$app = WechatApp::init();
			$session = $app->getSession($params['code']);

			if (!$session) {
				throw new Exception("登录失败");
			}

			if ($isGetMobile && isset($params['mobile_params']) && isset($params['mobile_params']['code'])) {
				$mobile = $app->getMobile($params['mobile_params']['code']);
			}

			$userInfo = [
				'openid' => $session['openid'],
				'unionid' => isset($session['unionid']) ? $session['unionid'] : '',
				'mobile' => $mobile
			];
			//已登录过的，直接登录
			$oauthData = UserOauth::getByOauthId($userInfo['openid']);
			if ($oauthData && $oauthData['user_id'] > 0) {
				$user = UserModel::get($oauthData['user_id']);
				if ($user && $user['status'] == 0) {
					throw new Exception("账号已被禁用");
				}
			}

			if ($isGetMobile && (!isset($userInfo['mobile']) || empty($userInfo['mobile']))) {
				throw new Exception("请授权手机号登录");
			}

			if (!$user || empty($user)) {
				//新用户注册                
				Db::startTrans();
				try {
					//创建用户信息
					$user = UserModel::create([
						'nickname' => GetNickname("XF", 7),
						'mobile' => $userInfo['mobile'],
					]);
					//创建微信关联登录
					if (!$oauthData) {
						// 新增用户记录
						$oauthData = [
							'user_id' => $user['user_id'],
							'oauth_id' => $userInfo['openid'],
							'unionid' => $userInfo['unionid'],
							'oauth_type' => 'wechat'
						];
						UserOauth::create($oauthData);
					} else {
						$oauthData->save(['user_id' => $user['user_id']]);
					}

					Db::commit();
				} catch (\Exception $e) {
					Db::rollback();
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

			return ['userInfo' => $user->hidden(['password']), 'token' => $token, 'tokenExpire' => $user->tokenExpire];
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
}
