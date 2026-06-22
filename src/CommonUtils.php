<?php

namespace Pick\HelperUtils;



class CommonUtils
{
    /**
     * 将数组转换成 URL 查询字符串格式
     *
     * @param mixed $args
     * @return string|mixed
     */
    public static function convert(&$args)
    {
        $data = '';
        if (is_array($args)) {
            foreach ($args as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $data .= $key . '[' . $k . ']=' . rawurlencode($v) . '&';
                    }
                } else {
                    $data .= "$key=" . rawurlencode($val) . "&";
                }
            }
            return trim($data, "&");
        }
        return $args;
    }

    /**
     * 判断字符串是否全是中文
     *
     * @param string $str
     * @return bool
     */
    public static function isAllChinese($str)
    {
        if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $str, $match)) {
            return true; //全是中文
        } else {
            return false; //不全是中文
        }
    }

    /**
     * 检查图片是不是base64编码的
     *
     * @param string $base64
     * @return bool
     */
    public static function isImageBase64($base64)
    {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 递归检查生成不重复的图片文件名
     *
     * @param string $dir
     * @param string $type_img
     * @return string
     */
    public static function checkPic($dir, $type_img)
    {
        $new_files = $dir . date("YmdHis") . '-' . rand(0, 9999999) . "{$type_img}";
        if (!file_exists($new_files)) {
            return $new_files;
        } else {
            return self::checkPic($dir, $type_img);
        }
    }

    /**
     * 获取数组中的某一列
     *
     * @param array $arr 数组
     * @param string $key_name 列名
     * @return array 返回那一列的数组
     */
    public static function getArrColumn($arr, $key_name)
    {
        if (function_exists('array_column')) {
            return array_column($arr, $key_name);
        }
        $arr2 = array();
        foreach ($arr as $key => $val) {
            $arr2[] = $val[$key_name];
        }
        return $arr2;
    }

    /**
     * 保留两位小数（向下取整）
     *
     * @param float|int $number
     * @return float
     */
    public static function towFloat($number)
    {
        return (floor($number * 100) / 100);
    }

    /**
     * 生成唯一订单号
     *
     * @param string $head 订单号前缀
     * @return string
     */
    public static function getSn($head = '')
    {
        $order_id_main = date('YmdHis') . mt_rand(1000, 9999);
        //唯一订单号码（YYMMDDHHIISSNNN）
        $osn = $head . substr($order_id_main, 2); //生成订单号
        return $osn;
    }



    /**
     * 生成随机用户名
     *
     * @return string
     */
    public static function getUsername()
    {
        $chars1 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $chars2 = "abcdefghijklmnopqrstuvwxyz0123456789";
        $username = "";
        for ($i = 0; $i < mt_rand(2, 3); $i++) {
            $username .= $chars1[mt_rand(0, 25)];
        }
        $username .= '_';

        for ($i = 0; $i < mt_rand(4, 6); $i++) {
            $username .= $chars2[mt_rand(0, 35)];
        }
        return $username;
    }

    /**
     * 判断当前时间是否在指定时间段之内
     *
     * @param string|int $a 起始时间（点数，例如 "8" 或 8）
     * @param string|int $b 结束时间（点数，例如 "22" 或 22）
     * @return boolean
     */
    public static function checkTime($a, $b)
    {
        $nowtime = time();
        $start = strtotime($a . ':00:00');
        $end = strtotime($b . ':00:00');

        if ($nowtime >= $end || $nowtime <= $start) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ipv6转成ipv4
     *
     * @param string $ip
     * @return string
     */
    public static function ipv6ToV4($ip)
    {
        if ($ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
                return $ip;
            }
            if (strpos($ip, ':') !== false) {
                $str = mb_substr($ip, 30, 38);
                $arr = explode(':', $str);
                if (count($arr) >= 2) {
                    $Ip1 = base_convert(mb_substr($arr[0], 0, 2), 16, 10);
                    $Ip2 = base_convert(mb_substr($arr[0], 2, 4), 16, 10);
                    $Ip3 = base_convert(mb_substr($arr[1], 0, 2), 16, 10);
                    $Ip4 = base_convert(mb_substr($arr[1], 2, 4), 16, 10);
                    $IpV4 = $Ip1 . '.' . $Ip2 . '.' . $Ip3 . '.' . $Ip4;
                    return $IpV4;
                }
            }
        }
        return $ip;
    }

    /**
     * 打印调试函数
     *
     * @param mixed $content
     * @param boolean $is_die
     */
    public static function pre($content, $is_die = true)
    {
        if (!headers_sent()) {
            header('Content-type: text/html; charset=utf-8');
        }
        echo '<pre>' . print_r($content, true) . '</pre>';
        if ($is_die) {
            die();
        }
    }

    /**
     * 格式化数字，保留一位小数并补零
     *
     * @param float|int $num
     * @return string
     */
    public static function reFf($num)
    {
        return sprintf("%.1f", $num) . '0';
    }



    /**
     * 格式化货币为千分位格式，去掉小数位
     *
     * @param float|int|string $amount 金额
     * @return string 格式化后的金额字符串
     */
    public static function formatCurrency($amount)
    {
        // 转换为浮点数
        $amount = floatval($amount);
        // 先去掉小数位（取整）
        $amount = floor($amount);
        // 如果少于5位数（小于10000），直接返回去掉小数位的值
        if ($amount < 10000) {
            return (string)$amount;
        }
        // 5位数及以上才进行千分位格式化
        return number_format($amount, 0, '.', '.');
    }



    /**
     * 密码加密的公共方法 (MD5)
     *
     * @param string $password
     * @return string
     */
    public static function passwordEncrypt($password)
    {
        $salt = '8520';
        return md5($salt . $password . $salt);
    }

    /**
     * 安全的密码加密方法（推荐使用）
     *
     * @param string $password
     * @return string
     */
    public static function securePasswordHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * 验证安全的密码
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function securePasswordVerify($password, $hash)
    {
        return password_verify($password, $hash);
    }



    /**
     * 生成密码
     *
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function makePassword($password, $salt = '')
    {
        return sha1(md5(md5($password . $salt)));
    }






    /**
     * 跨域响应头设置
     */
    public static function crossDomain()
    {
        if (!headers_sent()) {
            header("access-control-allow-headers: Authorization, App-Language, Accept-Language, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With");
            header("access-control-allow-methods: OPTIONS,GET, POST, PATCH, PUT, DELETE");
            header("access-control-allow-origin: *");
        }
    }

    /**
     * 获取当前时间 (Y-m-d H:i:s)
     *
     * @return string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 时间格式转换
     *
     * @param string|int $time
     * @param bool $is_timestamp
     * @param string $data_formate
     * @return string
     */
    public static function dateChange($time, $is_timestamp = false, $data_formate = "M j, Y")
    {
        if (!$time) {
            return '';
        } else {
            if ($is_timestamp == false) {
                $time = strtotime($time);
            }
            return date($data_formate, $time);
        }
    }

    /**
     * 友好时间差转换（如“几分钟前”）
     *
     * @param int|null $time
     * @return string
     */
    public static function mdate($time = null)
    {
        $time = $time === null || $time > time() ? time() : intval($time);
        $t = time() - $time;
        $y = date('Y', $time) - date('Y', time());
        switch (true) {
            case $t == 0:
                return '刚刚';
            case $t < 60:
                return $t . '秒前';
            case $t < 60 * 60:
                return floor($t / 60) . '分钟前';
            case $t < 60 * 60 * 24:
                return floor($t / (60 * 60)) . '小时前';
            case $t < 60 * 60 * 24 * 3:
                return floor($t / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time);
            case $t < 60 * 60 * 24 * 30:
                return date('m月d日 H:i', $time);
            case $t < 60 * 60 * 24 * 365 && $y == 0:
                return date('m月d日', $time);
            default:
                return date('Y年m月d日', $time);
        }
    }

    /**
     * 生成订单号
     *
     * @param string $business
     * @return string
     */
    public static function makeOrderNo($business)
    {
        return $business . date('YmdHis') . self::getNumberCode(6);
    }

    /**
     * 交易号生成
     *
     * @return string
     */
    public static function tradingNumber()
    {
        $msec = substr(microtime(), 2, 2);
        $subtle = substr(uniqid('', true), -8);
        return date('YmdHis') . $msec . $subtle;
    }

    /**
     * 随机纯数字字符串生成
     *
     * @param int $length
     * @return string
     */
    public static function getNumberCode($length = 6)
    {
        $code = '';
        for ($i = 0; $i < intval($length); $i++) {
            $code .= rand(0, 9);
        }
        return $code;
    }

    /**
     * 生成随机验证码
     *
     * @param int $len
     * @return int
     */
    public static function makeRandNumber($len)
    {
        $start = pow(10, $len - 1);
        $end = pow(10, $len) - 1;
        return rand($start, $end);
    }



    /**
     * 删除空目录
     *
     * @param string $path
     */
    public static function removeEmptyDir(string $path)
    {
        if (!is_dir($path)) {
            return;
        }
        $path_handle = opendir($path);
        readdir($path_handle);
        readdir($path_handle);

        if (!(bool) readdir($path_handle)) {
            @rmdir($path);
        }
    }



    /**
     * 生成毫秒级时间戳
     *
     * @return float
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 生成唯一 UUID (基于 uniqid 和 md5)
     *
     * @return string
     */
    public static function uuid()
    {
        return uniqid(md5(mt_rand()), true);
    }

    /**
     * curl POST 提交
     *
     * @param string $url
     * @param mixed $data
     * @return mixed
     */
    public static function curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     * curl GET 提交
     *
     * @param string $url
     * @param array $data
     * @return mixed
     */
    public static function curlGet($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     * 根据文字生成字母 SVG 头像 (Base64)
     *
     * @param string $text
     * @return string
     */
    public static function letterAvatar($text)
    {
        $total = unpack('L', hash('adler32', $text, true))[1];
        $hue = $total % 360;
        list($r, $g, $b) = self::hsv2Rgb($hue / 360, 0.3, 0.9);

        $bg = "rgb({$r},{$g},{$b})";
        $color = "#ffffff";
        $first = mb_strtoupper(mb_substr($text, 0, 1));

        $src = base64_encode('<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="100" width="100"><rect fill="' . $bg . '" x="0" y="0" width="100" height="100"></rect><text x="50" y="50" font-size="50" text-copy="fast" fill="' . $color . '" text-anchor="middle" text-rights="admin" dominant-baseline="central">' . $first . '</text></svg>');
        return 'data:image/svg+xml;base64,' . $src;
    }

    /**
     * HSV 转 RGB 辅助方法
     *
     * @param float $h
     * @param float $s
     * @param float $v
     * @return array
     */
    public static function hsv2Rgb($h, $s, $v)
    {
        $r = $g = $b = 0;
        $i = floor($h * 6);
        $f = $h * 6 - $i;
        $p = $v * (1 - $s);
        $q = $v * (1 - $f * $s);
        $t = $v * (1 - (1 - $f) * $s);

        switch ($i % 6) {
            case 0:
                $r = $v;
                $g = $t;
                $b = $p;
                break;
            case 1:
                $r = $q;
                $g = $v;
                $b = $p;
                break;
            case 2:
                $r = $p;
                $g = $v;
                $b = $t;
                break;
            case 3:
                $r = $p;
                $g = $q;
                $b = $v;
                break;
            case 4:
                $r = $t;
                $g = $p;
                $b = $v;
                break;
            case 5:
                $r = $v;
                $g = $p;
                $b = $q;
                break;
        }

        return [
            floor($r * 255),
            floor($g * 255),
            floor($b * 255)
        ];
    }
}
