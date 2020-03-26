# AliSMS - For Laravel5
---
###配置方法:
1. 从终端或命令行进入您的项目目录,运行
```
composer require iscms/alisms-for-laravel
```
2. 将
```
iscms\Alisms\AlidayuServiceProvider::class
```
加入config\app.php的Providers下
类似
```
        ...
        /*
         * Application Service Providers...
        */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        ...
        iscms\Alisms\AlidayuServiceProvider::class
```
3. 运行
```
php artisan vendor:publish
```
将配置文件发布到您的配置目录中
4. 您的config目录应该增加了alisms.php配置文件
```php
<?php
    return [
        'KEY' =>env('ALISMS_KEY',null),
        'SECRETKEY'=>env('ALISMS_SECRETKEY',null),
    ];
```

5.然后您需要在您项目的 ENV 配置文件中写入 SMS 配置
例如
```
   ALISMS_KEY=23305789
   ALISMS_SECRETKEY=**************
```
此处ALISMS_SECRETKEY是指您的账户应用密码,请勿透露给他人

---
###开始使用
1. 在您需要调用短信服务的控制器中,引用SMS
```
    use iscms\Alisms\SendsmsPusher as Sms;
```

```php

    public function __construct(Sms $sms)
    {
       $this->sms=$sms;
    }

    public function index()
    {
       $result=$this->sms->send("$phone","$name","$content","$code");
    }
```
2. 返回执行发送的结果
---

###参数说明
在开始使用中 send 方法一共加入了4个参数
>$phone,$name,$content,$code

1. $phone 指接受短信方的短信号码,
2. $name 指短信签名 可以在阿里大鱼短信签名 <http://www.alidayu.com/admin/service/sign> 找到
3. $content 是指短信模板中的变量内容.举个例子

在自己的阿里大鱼模板里面有下面一个短信模板
```
    模板名称: 身份验证验证码
    模板ID: SMS_3910275
    *模板内容:
    验证码${code}，您正在进行${product}身份验证，打死不要告诉别人哦！
```
那么里面存在着2个变量需要替换,一个是${code} ,一个是${product}

那么对应的我们的$content 就应该为
```php
    {
      code:"生成的验证码",
      product:"示例项目"
    }
```
4. $code 指在阿里云中的模板ID,上面的例子中使用了一个身份验证模板,那$code 应该填写
> SMS_3910275
5. 到这里应该没有问题了,开始发送短信吧~ ^_^
###By:秋綾

>如果你在使用短信服务时遇到程序上的问题,可以加 发送邮件<yoruchiaki@foxmail.com>到
