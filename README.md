## 注意
如有问题可联系:
* 邮箱: garen.goh@qq.com
* 主页: [wqiang.net](wqiang.net)

##如何使用依赖包
```
composer require garengoh/yii2-umeng-push:dev-master
```

## 配置
```
'umengPushService' => [
     'class' => 'garengoh\umeng\UmengPush',
     'android_app_key' => '你的友盟安卓应用的 app_key',
     'android_app_master_secret' => '你的友盟安卓应用的 app_master_secret',
     'android_production_mode' => false, //true=生产;false=测试(测试模式下,只有在友盟控制台添加的测试设备才能收到推送)
     'ios_app_key' => '你的友盟IOS应用的 app_key',
     'ios_app_master_secret' => '你的友盟IOS应用的 app_master_secret',
     'ios_production_mode' => false, //true=生产;false=测试(测试模式下,只有在友盟控制台添加的测试设备才能收到推送)
]
 ```
#### 例如
 
 ```
'umengPushService' => [
     'class' => 'garengoh\umeng\UmengPush',
     'android_app_key' => '5dfaeafc5704f32f9e003ac7',
     'android_app_master_secret' => 'pcflsyjfxz8ibogn6ug1qqj0p003d9fu',
     'android_production_mode' => false,
     'ios_app_key' => '5df9e37e0cafb2feb9001096',
     'ios_app_master_secret' => 'na1hhhwjle9qx5kkdg7kr5p5yalpjlna',
     'ios_production_mode' => false,
]
  ```
  
## 如何使用
#### 发送安卓消息
##### 单发(通过别名发送)
```
Yii::$app->umengPushService->androidCustomizedCast("你的别名", "你的别名类型")
     ->setPredefinedKeyValue("ticker", "单次别名发送的提示")
     ->setPredefinedKeyValue("title", "单次别名发送(后端)")
     ->setPredefinedKeyValue("text", "单次别名发送的简介")
     ->setPredefinedKeyValue("after_open", "go_custom")
     ->setPredefinedKeyValue("custom", "2")
     ->send();
```

##### 组发(根据过滤条件发送)
过滤方式有很多,建议参考官方文档([过滤条件示例](https://developer.umeng.com/docs/66632/detail/68343#h2--g-14))
```
$filter = [
   "where" => [
       "and" => [
           ["tag" => "122"]
       ]
   ]
];
Yii::$app->umengPushService->androidGroupCast($filter)
     ->setPredefinedKeyValue("ticker", "分组发送提示")
     ->setPredefinedKeyValue("title", "分组发送(后端)")
     ->setPredefinedKeyValue("text", "分组发送的简介")
     ->setPredefinedKeyValue("after_open", "go_custom")
     ->setPredefinedKeyValue("custom", "2")
     ->send();
```

#### 发送IOS消息
##### 单发(通过别名发送) 
```
Yii::$app->umengPushService->iosCustomizedCast("你的别名", "你的别名类型")
    ->setPredefinedKeyValue("alert", [
        "title" => "IOS 单发发(后端)_" . date("Y-m-d H:i"),
        "subtitle" => "副标题",
        "body" => "【立减500元】找我领券买车有优惠了！！！",
    ])
    ->setPredefinedKeyValue("badge", 1)
    ->setPredefinedKeyValue("sound", "chime")
    ->setCustomizedField("custom", "2")
    ->send();
```
##### 组发(根据过滤条件发送)
过滤方式有很多,建议参考官方文档([过滤条件示例](https://developer.umeng.com/docs/66632/detail/68343#h2--g-14))
```
$filter = [
    "where" => [
        "and" => [
            [
                'or' => [
                    ["tag" => "122"]
                ]
            ]
        ]
    ]
];
Yii::$app->umengPushService->iosGroupCast($filter)
    ->setPredefinedKeyValue("alert", [
        "title" => "IOS 组发(后端)_" . date("Y-m-d H:i"),
        "subtitle" => "副标题",
        "body" => "【立减500元】找我领券买车有优惠了！！！",
    ])
    ->setPredefinedKeyValue("description", "IOS 组发简介(后端)_" . date("Y-m-d H:i"))
    ->setPredefinedKeyValue("badge", 1)
    ->setPredefinedKeyValue("sound", "chime")
    ->setCustomizedField("custom", "2")
    ->send();
```