<?php
namespace garengoh\umeng;

use garengoh\umeng\notification\android\AndroidBroadcast;
use garengoh\umeng\notification\android\AndroidGroupcast;
use garengoh\umeng\notification\android\AndroidUnicast;
use garengoh\umeng\notification\android\AndroidCustomizedcast;
use garengoh\umeng\notification\AndroidNotification;
use garengoh\umeng\notification\ios\IOSBroadcast;
use garengoh\umeng\notification\ios\IOSGroupcast;
use garengoh\umeng\notification\ios\IOSUnicast;
use garengoh\umeng\notification\ios\IOSCustomizedcast;
use garengoh\umeng\notification\IOSNotification;

class UmengPush
{
    public $android_app_key;
    public $android_app_master_secret;
    public $android_production_mode;// 是否是生产模型(true=正式;false=测试. 处于测试模型时,只有在友盟工作台添加了测试设备,该测试设备才能收到通知)

    public $ios_app_key;
    public $ios_app_master_secret;
    public $ios_production_mode;// 是否是生产模型(true=正式;false=测试. 处于测试模型时,只有在友盟工作台添加了测试设备,该测试设备才能收到通知)

    const SYSTEM_ANDROID = 1;
    const SYSTEM_IOS = 2;

    private function getTime()
    {
        return strval(time());
    }

    /**
     * 友盟API请求通用接口
     * 如果你不习惯使用封装好的方法,可以使用这个通用方法请求所有友盟的接口
     *
     * @param integer $system 设备类型(1=安卓,2=IOS)
     * @param String $path API路径(如:/api/status, 任务类消息状态查询)
     * @param array $data 请求体
     * @param string $method 方法
     * @return mixed
     * @throws \Exception
     */
    function send($system, $path, $data, $method = 'POST')
    {
        if (!in_array($system, [self::SYSTEM_ANDROID, self::SYSTEM_IOS])) {
            throw new \Exception('设备类型选择错误!');
        }

        $method = strtoupper($method);
        $host = 'https://msgapi.umeng.com';
        $url = $host . $path;
        $secret = $system == 1 ? $this->android_app_master_secret : $this->ios_app_master_secret;
        $data = json_encode($data);
        $sign = md5($method . $url . $data . $secret);
        $url = $url . "?sign=" . $sign;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * 安卓通用发送
     *
     * @param AndroidNotification $androidCast
     * @return AndroidNotification
     */
    function androidCommonCast(AndroidNotification $androidCast)
    {
        return $androidCast->setAppMasterSecret($this->android_app_master_secret)
            ->setPredefinedKeyValue("appkey", $this->android_app_key)
            ->setPredefinedKeyValue("timestamp", $this->getTime())
            ->setPredefinedKeyValue("production_mode", $this->android_production_mode);
    }

    /**
     * 安卓全部用户群发
     * @return AndroidBroadcast
     */
    function androidBroadCast()
    {
        $cast = new AndroidBroadcast();

        $cast = $this->androidCommonCast($cast);

        return $cast;
    }

    /**
     * 安卓 单个用户 或 多个用户 通过 device_token 发送.
     * 注意: 多个device_token用英文逗号分隔,且不能超过500个
     *
     * @param string $device_tokens
     * @return AndroidUnicast
     * @throws \Exception
     */
    function androidUniCast($device_tokens)
    {
        if (strpos($device_tokens, ',') !== false && count(explode(',', $device_tokens)) > 500) {
            throw new \Exception('device_token 超出上限!');
        }

        /**
         * @var $cast AndroidUnicast
         */
        $cast = new AndroidUnicast();
        $cast = $this->androidCommonCast($cast)
            ->setPredefinedKeyValue("device_tokens", $device_tokens);

        return $cast;
    }

    /**
     * 安卓部分用户发送(通过过滤条件筛选用户发送)
     *
     * @param $filter array 过滤条件(参考官方文档: https://developer.umeng.com/docs/66632/detail/68343#h2--g-14)
     * @return AndroidGroupcast
     */
    function androidGroupCast(array $filter)
    {
        /**
         * @var $cast AndroidGroupcast
         */
        $cast = new AndroidGroupcast();
        $cast = $this->androidCommonCast($cast)
            ->setPredefinedKeyValue("filter", $filter);

        return $cast;
    }

    /**
     * 通过别名单发
     * @param string $alias 别名
     * @param string $alias_type 别名类型
     * @return AndroidCustomizedcast
     */
    function androidCustomizedCast($alias, $alias_type)
    {
        /**
         * @var $cast AndroidCustomizedcast
         */
        $cast = new AndroidCustomizedcast();
        $cast = $this->androidCommonCast($cast)
            ->setPredefinedKeyValue("alias", (string)$alias)
            ->setPredefinedKeyValue("alias_type", (string)$alias_type);

        return $cast;
    }

    /**
     * IOS 通用发送
     * @param IOSNotification $iosCast
     * @return IOSNotification
     */
    function iosCommonCast(IOSNotification $iosCast)
    {
        return $iosCast->setAppMasterSecret($this->ios_app_master_secret)
            ->setPredefinedKeyValue("appkey", $this->ios_app_key)
            ->setPredefinedKeyValue("timestamp", $this->getTime())
            ->setPredefinedKeyValue("production_mode", $this->ios_production_mode);
    }


    /**
     * IOS全部用户群发
     *
     * @return IOSBroadcast
     */
    function iosBroadCast()
    {
        $cast = new IOSBroadcast();
        $cast = $this->iosCommonCast($cast);

        return $cast;
    }

    /**
     * IOS 通过 device_token 发送给 单个用户 或 多个用户.
     * 注意: 多个device_token用英文逗号分隔,且不能超过500个
     * @param String $device_tokens
     * @return IOSUnicast
     */
    function iosUniCast($device_tokens)
    {
        /**
         * @var $cast IOSUnicast
         */
        $cast = new IOSUnicast();
        $cast = $this->iosCommonCast($cast)
            ->setPredefinedKeyValue("device_tokens", $device_tokens);

        return $cast;
    }

    /**
     * IOS 通过过滤条件发送给部分用户
     *
     * @param array $filter 过滤条件
     * @return IOSGroupcast
     */
    function iosGroupCast(array $filter)
    {
        /**
         * @var $cast IOSGroupcast
         */
        $cast = new IOSGroupcast();
        $cast = $this->iosCommonCast($cast)
            ->setPredefinedKeyValue("filter", $filter);

        return $cast;
    }

    /**
     * IOS 通过别名发送给单个用户
     *
     * @param String $alias 别名
     * @param String $alias_type 别名类型
     * @param bool $production_mode 是否是生产环境
     * @return IOSCustomizedcast
     */
    function iosCustomizedCast($alias, $alias_type)
    {
        /**
         * @var $cast IOSCustomizedcast
         */
        $cast = new IOSCustomizedcast();
        $cast = $this->iosCommonCast($cast)
            ->setPredefinedKeyValue("alias", (string)$alias)
            ->setPredefinedKeyValue("alias_type", (string)$alias_type);

        return $cast;
    }
}