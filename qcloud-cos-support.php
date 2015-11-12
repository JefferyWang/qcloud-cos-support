<?php
/*
Plugin Name: 腾讯云COS附件
Plugin URI: http://blog.wangjunfeng.com
Description: 使用腾讯云对象存储服务 COS 作为附件存储空间。（This is a plugin that uses QCloud Cloud Object Service for attachments remote saving.）
Version: 0.1.0
Author: Jeffery Wang
Author URI: http://blog.wangjunfeng.com
License: MIT
*/
require_once('sdk/include.php');
use Qcloud_cos\Auth;
use Qcloud_cos\Cosapi;

if ( !defined('WP_PLUGIN_URL') )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );//  plugin url

define('COS_BASENAME', plugin_basename(__FILE__));
define('COS_BASEFOLDER', plugin_basename(dirname(__FILE__)));
define('COS_FILENAME', str_replace(DFM_BASEFOLDER.'/', '', plugin_basename(__FILE__)));

// 初始化选项
register_activation_hook(__FILE__, 'cos_set_options');

// 初始化选项
function cos_set_options() {
    $options = array(
        'bucket' => "",
        'app_id' => "",
    	'secret_id' => "",
        'secret_key' => "",
		'nothumb' => "false", // 是否上传所旅途
		'nolocalsaving' => "false", // 是否保留本地备份
		//'upload_path' => "", // 存储位置
        'upload_url_path' => "", // URL前缀
    );
    add_option('cos_options', $options, '', 'yes');
}

// 在插件列表页添加设置按钮
function cos_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( dirname(__FILE__).'/qcloud-cos-support.php' ) ) {
		$links[] = '<a href="options-general.php?page=' . COS_BASEFOLDER . '/qcloud-cos-support.php">'.设置.'</a>';
	}
	return $links;
}
add_filter( 'plugin_action_links', 'cos_plugin_action_links', 10, 2 );

// 在导航栏“设置”中添加条目
function cos_add_setting_page() {
    add_options_page('腾讯云COS设置', '腾讯云COS设置', 8, __FILE__, 'cos_setting_page');
}
add_action('admin_menu', 'cos_add_setting_page');

// 插件设置页面
function cos_setting_page() {

	$options = array();
	if($_POST['bucket']) {
		$options['bucket'] = trim(stripslashes($_POST['bucket']));
	}
	if($_POST['app_id']) {
		$options['app_id'] = trim(stripslashes($_POST['app_id']));
	}
	if($_POST['secret_id']) {
		$options['secret_id'] = trim(stripslashes($_POST['secret_id']));
	}
	if($_POST['secret_key']) {
		$options['host'] = trim(stripslashes($_POST['secret_key']));
	}
	if($_POST['nothumb']) {
		$options['nothumb'] = (isset($_POST['nothumb']))?'true':'false';
	}
	if($_POST['nolocalsaving']) {
		$options['nolocalsaving'] = (isset($_POST['nolocalsaving']))?'true':'false';
	}
	if($_POST['upload_url_path']) {
		//仅用于插件卸载时比较使用
		$options['upload_url_path'] = trim(stripslashes($_POST['upload_url_path']));
	}

	//检查提交的AK/SK是否有管理该bucket的权限
	$flag = 0;
	if($_POST['bucket']&&$_POST['app_id']&&$_POST['secret_id']&&$_POST['secret_key']){

	}

	if($options !== array() ){
		//更新数据库
		update_option('cos_options', $options);

		$upload_path = trim(trim(stripslashes($_POST['upload_path'])),'/');
		$upload_path = ($upload_path == '') ? ('wp-content/uploads') : ($upload_path);
		update_option('upload_path', $upload_path );

		$upload_url_path = trim(trim(stripslashes($_POST['upload_url_path'])),'/');
		update_option('upload_url_path', $upload_url_path );

?>
<div class="updated"><p><strong>设置已保存！
<?php
	if($flag==0)
		echo '<span style="color:#F00">注意：您的AK/SK没有管理该Bucket的权限，因此不能正常使用！</span>';
	elseif($flag == -1)
		echo '<span style="color:#F00">注意：网络通信错误，未能校验您的AK/SK是否对该bucket是否具有管理权限</span>';
	elseif($flag == -11)
		echo '<span style="color:#F00">注意：该BUCKET现在处于“公开读写”状态，会有安全隐患哦！设置成“公开读”就足够了。</span>';
	elseif($flag == -12)
		echo '<span style="color:#F00">注意：该BUCKET现在处于“私有”状态，不能被其他人访问哦！建议将BUKET权限设置成“公开读”。</span>';
	elseif($flag == -2)
		echo '<span style="color:#F00">注意：该BUCKET的“存储地域”或“HOST主机”可能搞错了，请再次确认下。</span>';
?>
</strong></p></div>
<?php
    }

    $cos_options = get_option('cos_options', TRUE);
	$upload_path = get_option('upload_path');
	$upload_url_path = get_option('upload_url_path');

    $cos_bucket = attribute_escape($cos_options['bucket']);
    $cos_app_id = attribute_escape($cos_options['app_id']);
    $cos_secret_id = attribute_escape($cos_options['secret_id']);
	$cos_secret_key = attribute_escape($cos_options['secret_key']);

	$cos_nothumb = attribute_escape($cos_options['nothumb']);
	$cos_nothumb = ( $cos_nothumb == 'true' );

	$cos_nolocalsaving = attribute_escape($cos_options['nolocalsaving']);
	$cos_nolocalsaving = ( $cos_nolocalsaving == 'true' );
?>
<div class="wrap" style="margin: 10px;">
    <h2>腾讯云 COS 附件设置</h2>
    <form name="form1" method="post" action="<?php echo wp_nonce_url('./options-general.php?page=' . COS_BASEFOLDER . '/qcloud-cos-support.php'); ?>">
		<table class="form-table">
			<tr>
				<th><legend>Bucket 设置</legend></th>
				<td>
					<input type="text" name="bucket" value="<?php echo $cos_bucket;?>" size="50" placeholder="BUCKET"/>
					<p>请先访问 <a href="http://console.qcloud.com/cos" target="_blank">腾讯云控制台</a> 创建 <code>bucket</code> ，再填写以上内容。</p>
				</td>
			</tr>
            <tr>
				<th><legend>APP ID 设置</legend></th>
				<td>
					<input type="text" name="app_id" value="<?php echo $cos_app_id;?>" size="50" placeholder="APP ID"/>
					<p>请先访问 <a href="http://console.qcloud.com/cos" target="_blank">腾讯云控制台</a> 点击<code>获取secretKey</code>获取 <code>APP ID、secretID、secretKey</code></p>
				</td>
			</tr>
			<tr>
				<th><legend>secretID</legend></th>
				<td><input type="text" name="secret_id" value="<?php echo $cos_secret_id;?>" size="50" placeholder="secretID"/></td>
			</tr>
			<tr>
				<th><legend>secretKey</legend></th>
				<td>
					<input type="text" name="secret_key" value="<?php echo $cos_secret_key;?>" size="50" placeholder="secretKey"/>
				</td>
			</tr>
			<tr>
				<th><legend>不上传缩略图</legend></th>
				<td><input type="checkbox" name="nothumb" <?php if($cos_nothumb) echo 'checked="TRUE"';?> /></td>
			</tr><tr>
				<th><legend>不在本地保留备份</legend></th>
				<td><input type="checkbox" name="nolocalsaving" <?php if($cos_nolocalsaving) echo 'checked="TRUE"';?> /></td>
			</tr><tr>
				<th><legend>本地文件夹：</legend></th>
				<td>
					<input type="text" name="upload_path" value="<?php echo $upload_path;?>" size="50" placeholder="请输入上传文件夹"/>
					<p>附件在服务器上的存储位置，例如： <code>wp-content/uploads</code> （注意不要以“/”开头和结尾），根目录请输入<code>.</code>。</p>
				</td>
			</tr>
			<tr>
				<th><legend>URL前缀：</legend></th>
				<td>
					<input type="text" name="upload_url_path" value="<?php echo $upload_url_path;?>" size="50" placeholder="请输入URL前缀"/>
					<p><b>注意：</b></p>
					<p>1）URL前缀的格式为 <code>http://{cos域名}</code> （“本地文件夹”为 <code>.</code> 时），或者 <code>http://{cos域名}/{本地文件夹}</code> ，“本地文件夹”务必与上面保持一致（结尾无 <code>/</code> ）。</p>
					<p>2）cos中的存放路径（即“文件夹”）与上述 <code>本地文件夹</code> 中定义的路径是相同的（出于方便切换考虑）。</p>
					<p>3）如果需要使用 <code>独立域名</code> ，直接将 <code>{cos域名}</code> 替换为 <code>独立域名</code> 即可。</p>
				</td>
			</tr>
			<tr>
				<th><legend>更新选项</legend></th>
				<td><input type="submit" name="submit" value="更新" /></td>
			</tr>
		</table>
    </form>
</div>
<?php
}
?>
