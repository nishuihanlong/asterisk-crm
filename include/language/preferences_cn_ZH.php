<?php
$db_dbtype		= '数据库类型';
$db_dbhost		= '数据库主机地址';
$db_dbname		= '数据库名称';
$db_username	= '数据库用户名';
$db_password	= '数据库密码';

$as_server		= 'Asterisk服务器地址';
$as_port		= 'Asterisk服务器AMI端口号, 默认情况下是5038';
$as_username	= 'AMI用户名';
$as_secret		= 'AMI密码';
$as_monitorpath		= '录音文件保存的路径, 录音文件将会保存在Asterisk服务器上而不是asterCRM服务器';
$as_monitorformat	= '录音文件的文件格式';

$sys_log_enabled	= '是否启动日志';
$sys_log_file_path	= '日志文件的路径';
$sys_outcontext		= '座席通过asterCRM拨外线号码时使用的asterisk context';
$sys_incontext		= '座席通过asterCRM拨内线号码时使用的asterisk context';
$sys_predialer_context		= '使用预拨号时, 当目标号码被接通后, 连接到哪个context';
$sys_predialer_extension	= '使用预拨号时, 当目标号码被接通后, 连接到context的哪个extension';
$sys_phone_number_length	= '只有当拨入/拨出号码大于此数时, asterCRM才会弹屏';
$sys_trim_prefix			= '需要从拨入/拨出号码中去除的前缀, 使用逗号分隔, 如果没有前缀需要去除, 可以什么都不填写';
$sys_allow_dropcall			= '是否通过生成.call文件的方法发起呼叫, 如果asterCRM和Asterisk不在否则请选择0';
$sys_portal_display_type	= '座席界面显示的客户信息类型, 选择customer将会显示该座席添加过的所有客户, 选择note将只显示添加过备注信息的客户';
$sys_enable_contact			= '是否在坐席界面启用联系人';
$sys_pop_up_when_dial_out	= '座席电话拨出时asterCRM是否弹屏';
$sys_pop_up_when_dial_in	= '座席电话有拨入时asterCRM是否弹屏';
$sys_allow_same_data		= '是否允许重复的客户名称';
$sys_browser_maximize_when_pop_up	= '弹屏时是否浏览器最大化';
$sys_firstring				= '呼叫时先呼叫主叫号码还是先呼叫被叫号码';
$sys_enable_external_crm	= '是否使用第三方CRM';
$sys_open_new_window		= '弹屏时是否弹出新窗口';
$sys_external_crm_default_url = '当使用第三方CRM时, 默认启动的页面';
$sys_external_crm_url		= '当有弹屏事件发生时, 要调用的外部CRM页面, %callerid: 主叫号码, %calleeid: 被叫号码, %method	拨出(dialout)或者拨入(dialin)';
$sys_upload_file_path		= '上传文件的路径, 如 ./upload/ , 此目录需要有写权限';
$save_success				= '保存成功';
$db_connect_failed			= '数据库连接失败, 请检查系统配置';
$db_connect_success			= '数据库连接成功';
$AMI_connect_failed			= 'AMI连接失败, 请检查系统配置';
$AMI_connect_success		= 'AMI连接成功';
$permission_error			= '目录权限错误';
$sys_check_success			= '配置信息检查成功';
?>
