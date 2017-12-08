<?php
/** OA管理后台配置 */
return [
	'user_auth_on'       => true,
	// 默认认证类型 1 登录认证 2 实时认证
	'user_auth_type'     => 2,
	// 用户认证session标记
	'user_auth_key'      => 'authid',
	// 超级管理员标志
	'admin_auth_key'     => 'administrator',
	// 用户认证密码加密方式
	'auth_pwd_encoder'   => 'md5',
	// 默认认证网关
	'user_auth_gateway'  => '/public/login',

	// 默认验证数据表模型
	'user_auth_model'    => 'user',
	// 是否开启游客授权访问
	'guest_auth_on'      => false,
	// 游客的用户id
	'guest_auth_id'      => 0,

	// 数据库连接
	'rbac_db_connection' => '',
	// 角色表名称
	'rbac_role_table'    => 'role',
	// 权限表名称
	'rbac_access_table'  => 'access',
	// 节点表名称
	'rbac_node_table'    => 'node',
];