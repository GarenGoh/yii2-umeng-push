<?php
namespace garengoh\umeng;
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCustomizedcast.php');

class UmengPush
{
    public $android_app_key;
    public $android_app_master_secret;
    public $android_production_mode;// 是否是生产模型(true=正式;false=测试. 处于测试模型时,只有在友盟工作台添加了测试设备,该测试设备才能收到通知)

    public $ios_app_key;
    public $ios_app_master_secret;
    public $ios_production_mode;// 是否是生产模型(true=正式;false=测试. 处于测试模型时,只有在友盟工作台添加了测试设备,该测试设备才能收到通知)

    private function getTime()
    {
        return strval(time());
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
     * @throws Exception
     */
    function androidUniCast($device_tokens)
    {
        if (strpos($device_tokens, ',') !== false && count(explode(',', $device_tokens)) > 500) {
            throw new Exception('device_token 超出上限!');
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