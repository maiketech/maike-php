SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for m_action
-- ----------------------------
DROP TABLE IF EXISTS `mks_action`;
CREATE TABLE `mks_action`  (
  `action_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级菜单id',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '菜单标题',
  `icon` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
  `auth` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '权限标识',
  `route` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  COMMENT '前端路由标识',
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '后端动作标识',
  `desc` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `is_menu` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否菜单，用于生成菜单数据',
  `sort` int(11) NOT NULL DEFAULT 100 COMMENT '排序',
  `base_auth` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '默认权限',
  PRIMARY KEY (`action_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限菜单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_admin
-- ----------------------------
DROP TABLE IF EXISTS `mks_admin`;
CREATE TABLE `mks_admin`  (
  `admin_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '手机号码',
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `password` varchar(96) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '邮箱地址',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `is_super` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否超级管理员',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`admin_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_admin_login
-- ----------------------------
DROP TABLE IF EXISTS `mks_admin_login`;
CREATE TABLE `mks_admin_login`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'TOKEN值',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'TOKEN关联的用户ID',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `ip_desc` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `user_agent` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `invalid_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '手动设置失效时间，强制注销',
  `expire_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'TOKEN过期时间',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT 'TOKEN创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员登录记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_admin_oauth
-- ----------------------------
DROP TABLE IF EXISTS `mks_admin_oauth`;
CREATE TABLE `mks_admin_oauth`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `oauth_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '第三方登陆类型(wechat、alipay)',
  `oauth_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '第三方用户唯一标识 (openid、userid)',
  `unionid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信unionID',
  `nickname` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_id`(`admin_id`) USING BTREE,
  INDEX `oauth_type`(`oauth_type`) USING BTREE,
  INDEX `oauth_type_2`(`oauth_type`, `oauth_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '绑定第三方登录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `mks_admin_role`;
CREATE TABLE `mks_admin_role`  (
  `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `role_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '角色描述',
  `action_ids` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `auths` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '前端权限名',
  `actions` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '后端权限动作',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1启用，0禁用',
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否系统默认角色',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (`role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_article
-- ----------------------------
DROP TABLE IF EXISTS `mks_article`;
CREATE TABLE `mks_article`  (
  `article_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `title` varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文章标题',
  `category_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `cover_image` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '封面图片地址',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '文章内容',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章排序(数字越小越靠前)',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '文章状态(0隐藏 1显示)',
  `views` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际阅读量',
  `author` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `create_admin_id` int(20) NOT NULL DEFAULT 0,
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`article_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '文章表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_article_category
-- ----------------------------
DROP TABLE IF EXISTS `mks_article_category`;
CREATE TABLE `mks_article_category`  (
  `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `category_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `icon` varchar(500) NULL DEFAULT '',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序方式(数字越小越靠前)',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '文章分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_attachment
-- ----------------------------
DROP TABLE IF EXISTS `mks_attachment`;
CREATE TABLE `mks_attachment`  (
  `attach_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件分组ID',
  `storage` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '存储方式',
  `domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '存储域名',
  `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'image' COMMENT '文件类型(image图片 video视频 file文件)',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件名称(上传后)',
  `path` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件路径',
  `thumb_path` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '缩略图路径',
  `size` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小(字节)',
  `ext` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件扩展名',
  `file_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上传时的文件名',
  `module` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `creator_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传者用户ID',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`attach_id`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '附件库表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_attachment_group
-- ----------------------------
DROP TABLE IF EXISTS `mks_attachment_group`;
CREATE TABLE `mks_attachment_group`  (
  `group_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `group_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分组名称',
  `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'image' COMMENT '文件类型(image图片 video视频 file文件)',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序(数字越小越靠前)',
  `module` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `creator_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者用户ID',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '附件库分组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_dict
-- ----------------------------
DROP TABLE IF EXISTS `mks_dict`;
CREATE TABLE `mks_dict`  (
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `group` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `desc` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sort` int(10) NULL DEFAULT 100,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '数据字典表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_help
-- ----------------------------
DROP TABLE IF EXISTS `mks_help`;
CREATE TABLE `mks_help`  (
  `help_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '帮助标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '帮助内容',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序(数字越小越靠前)',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '文章状态(0隐藏 1显示)',
  `views` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际阅读量',
  `creator_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者用户ID',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除(1已删除)',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`help_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '帮助中心记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_notice
-- ----------------------------
DROP TABLE IF EXISTS `mks_notice`;
CREATE TABLE `mks_notice`  (
  `notice_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `title` varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文章标题',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '文章内容',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章排序(数字越小越靠前)',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '文章状态(0隐藏 1显示)',
  `views` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际阅读量',
  `creator_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者用户ID',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`notice_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '文章记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_setting
-- ----------------------------
DROP TABLE IF EXISTS `mks_setting`;
CREATE TABLE `mks_setting`  (
  `key` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置名称',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置说明',
  `input_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'text' COMMENT '配置输入类型',
  `options` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置值',
  `help` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置说明/描述',
  `value` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '配置值',
  `group` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '配置分组',
  `sort` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`key`) USING BTREE,
  UNIQUE INDEX `key`(`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_system_log
-- ----------------------------
DROP TABLE IF EXISTS `mks_system_log`;
CREATE TABLE `mks_system_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一性标识',
  `action_id` int(10) NOT NULL DEFAULT 0 COMMENT '行为ID',
  `method` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '请求类型',
  `module` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模块(APP)',
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作方法',
  `url` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作页面',
  `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '请求参数(JSON格式)',
  `desc` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '日志描述',
  `ip` varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'IP地址',
  `ip_desc` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'IP地址信息',
  `user_agent` varchar(360) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User-Agent',
  `operator_name` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作人名',
  `operator_id` int(11) NOT NULL DEFAULT 0 COMMENT '操作人',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`, `create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统行为日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_user
-- ----------------------------
DROP TABLE IF EXISTS `mks_user`;
CREATE TABLE `mks_user`  (
  `user_id` int(20) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `gender` tinyint(1) NOT NULL DEFAULT 0 COMMENT '性别：0未知，1男，2女',
  `age` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '年龄',
  `birth_date` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `idcard` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '身份证号',
  `address` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '家庭地址',
  `avatar` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '1正常，0停用',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_user_login
-- ----------------------------
DROP TABLE IF EXISTS `mks_user_login`;
CREATE TABLE `mks_user_login`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'TOKEN值',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'TOKEN关联的用户ID',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `ip_desc` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `user_agent` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `invalid_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '手动设置失效时间，强制注销',
  `expire_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'TOKEN过期时间',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT 'TOKEN创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for m_user_oauth
-- ----------------------------
DROP TABLE IF EXISTS `mks_user_oauth`;
CREATE TABLE `mks_user_oauth`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `oauth_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '第三方登陆类型(wechat、alipay)',
  `oauth_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '第三方用户唯一标识 (openid、userid)',
  `unionid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信unionID',
  `nickname` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `oauth_type`(`oauth_type`) USING BTREE,
  INDEX `oauth_type_2`(`oauth_type`, `oauth_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '第三方用户信息表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
