<?php
include_once __DIR__ . '/UmengPush.php';

$demo = new UmengPush();

// -------安卓测试------
// 群发
$cast = $demo->androidBroadCast()
    ->setPredefinedKeyValue("title", "全部群体发送(后端)")
    ->setPredefinedKeyValue("ticker", "全部群体发送(后端)")
    ->setPredefinedKeyValue("text", "全部群体发送的简介")
    ->setPredefinedKeyValue("after_open", "go_custom")
    //->setExtraField("test", "helloworld")
    ->setPredefinedKeyValue("custom", "2");

$result = $cast->send();
print_r($result);
exit;

// 组发
$filter = [
    "where" => [
        "and" => [
            ["tag" => "122"]
        ]
    ]
];
$cast = $demo->androidGroupCast($filter)
    ->setPredefinedKeyValue("ticker", "分组发送提示")
    ->setPredefinedKeyValue("title", "分组发送(后端)")
    ->setPredefinedKeyValue("text", "分组发送的简介")
    ->setPredefinedKeyValue("after_open", "go_custom")
    ->setPredefinedKeyValue("custom", "2");



// 单发
$cast = $demo->androidCustomizedCast("100000", "userId")
    ->setPredefinedKeyValue("ticker", "单次别名发送的提示")
    ->setPredefinedKeyValue("title", "单次别名发送(后端)")
    ->setPredefinedKeyValue("text", "单次别名发送的简介")
    ->setPredefinedKeyValue("after_open", "go_custom")
    ->setPredefinedKeyValue("custom", "2");


// -------IOS测试------
// 群发
$cast = $demo->iosBroadCast()
    ->setPredefinedKeyValue("alert", [
        "title" => "IOS 群发(后端)_" . date("Y-m-d H:i", time() + 8 * 3600),
        "subtitle" => "副标题",
        "body" => "【立减500元】找我领券买车有优惠了！！！",
    ])
    ->setPredefinedKeyValue("badge", 1)
    ->setPredefinedKeyValue("sound", "chime")
    ->setPredefinedKeyValue("description", "IOS 群发简介(后端)_" . date("Y-m-d H:i", time() + 8 * 3600))
    ->setCustomizedField("custom", "1");

// 组发
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
$cast = $demo->iosGroupCast($filter)
    ->setPredefinedKeyValue("alert", [
        "title" => "IOS 组发(后端)_" . date("Y-m-d H:i", time() + 8 * 3600),
        "subtitle" => "副标题",
        "body" => "【立减500元】找我领券买车有优惠了！！！",
    ])
    ->setPredefinedKeyValue("description", "IOS 组发简介(后端)_" . date("Y-m-d H:i", time() + 8 * 3600))
    ->setPredefinedKeyValue("badge", 1)
    ->setPredefinedKeyValue("sound", "chime")
    ->setCustomizedField("custom", "2");

// 单发
$cast = $demo->iosCustomizedCast("50", "userId")
    ->setPredefinedKeyValue("alert", [
        "title" => "IOS 单发发(后端)_" . date("Y-m-d H:i", time() + 8 * 3600),
        "subtitle" => "副标题",
        "body" => "【立减500元】找我领券买车有优惠了！！！",
    ])
    ->setPredefinedKeyValue("badge", 1)
    ->setPredefinedKeyValue("sound", "chime")
    ->setCustomizedField("custom", "2");

$result = $cast->send();
print_r($result);
exit;
?>