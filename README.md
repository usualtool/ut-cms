#### 简要说明
UTCMS V9版本是基于UT框架构建的内容管理系统，能够快速搭建各种类型的应用。（该版本不兼容2021年停止维护的V8版本）
#### 系统要求
Nginx/Apache/IIS.  
PHP5/PHP7/PHP8  
UTFramework 5+
#### 安装方式 
- 下载安装包并解压
- 运行应用域名/IP进行初始配置
- 初始超级账户密码，均为admin
#### 敏感安全
.config配置包含敏感信息，为防止远程访问和下载，您必须在站点配置文件中设置禁止非本地访问.config。
Nginx
```
location ~* \.(log|config|cms)$ {
deny all;
}
```
Apache
```
<Files ~ "\.config$">
Order allow,deny
Deny from all
</Files>
```
#### [演示](http://cms.frame.usualtool.com/example/)