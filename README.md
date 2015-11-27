# qcloud-cos-support
使用腾讯云对象存储服务 COS 作为附件存储空间的Wordpress插件，本插件核心功能使用了腾讯云COS官方SDK。

该插件实现以下功能:

* 使用腾讯云对象存储服务存储wordpress站点图片等多媒体文件

* 可配置是否保留本地备份和是否上传缩略图

* 支持配置图片等存储地址，并可支持腾讯云COS绑定的个性域名（需已备案）

github项目地址:  [https://github.com/JefferyWang/qcloud-cos-support](https://github.com/JefferyWang/qcloud-cos-support)

Git@OSC项目地址:  [http://git.oschina.net/wangjunfeng/qcloud-cos-support](http://git.oschina.net/wangjunfeng/qcloud-cos-support)

## 安装
### 直接下载源码
从github下载源码，通过wordpress后台上传安装，或者直接将源码上传到wordpress插件目录`wp-content\plugins`，然后在后台启用。

## 修改配置
* 方法一：在wordpress插件管理页面有设置按钮，进行设置
* 方法二：在wordpress后台管理左侧导航栏`设置`下`腾讯云cos设置`，点击进入设置页面

## 特别说明
* 本插件仅支持`PHP 5.3+`版本

## 截图
![设置页面](http://i12.tietuku.com/93f74b6ccf98e0b7.jpg)
![媒体中心](http://i12.tietuku.com/c1cef7ae6dbdcb2c.jpg)
