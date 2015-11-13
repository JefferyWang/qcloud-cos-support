<?php
namespace Qcloud_cos;

class Conf
{
    const PKG_VERSION = '1.0.0';

    const API_IMAGE_END_POINT = 'http://web.image.myqcloud.com/photos/v1/';
    const API_VIDEO_END_POINT = 'http://web.video.myqcloud.com/videos/v1/';
    const API_COSAPI_END_POINT = 'http://web.file.myqcloud.com/files/v1/';
    //请到http://console.qcloud.com/cos去获取你的appid、sid、skey
    const APPID = 'your appid';
    const SECRET_ID = 'your secretId';
    const SECRET_KEY = 'your secretKey';

    public static $APPID;
    public static $SECRET_ID;
    public static $SECRET_KEY;

    public function __construct()
    {
        $cos_options = get_option('cos_options', TRUE);
        self::$APPID = esc_attr($cos_options['app_id']);
        self::$SECRET_ID = esc_attr($cos_options['secret_id']);
    	self::$SECRET_KEY = esc_attr($cos_options['secret_key']);
    }

    public static function getUA() {
        return 'QcloudPHP/'.self::PKG_VERSION.' ('.php_uname().')';
    }
}


//end of script
