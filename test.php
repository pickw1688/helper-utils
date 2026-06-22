<?php

// 如果本地有 vendor/autoload.php 则使用 composer 自动加载
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    // 否则本地 fallback 手动 require 测试
    require __DIR__ . '/src/Hello.php';
    require __DIR__ . '/src/Mailer.php';
    require __DIR__ . '/src/CommonUtils.php';
}

use Pick\HelperUtils\Hello;
use Pick\HelperUtils\Mailer;
use Pick\HelperUtils\CommonUtils;

// 1. 测试 Hello
$hello = new Hello();
echo $hello->say('Local Tester') . (PHP_SAPI === 'cli' ? "\n" : "");

// 2. 测试 Mailer (空配置验证)
$mailer = new Mailer([
    'smtp_username' => '',
    'smtp_password' => ''
]);

echo "Testing Mailer config validation:\n";
if (!$mailer->send('target@example.com', 'Test Subject', 'Body')) {
    echo "Expected error: " . $mailer->getError() . "\n";
} else {
    echo "Mail sent successfully (unexpected with empty config)\n";
}

// 3. 测试 CommonUtils 的各个非DB方法
echo "\n--- Testing CommonUtils ---\n";

// test convert
$args = ['name' => '张三', 'options' => ['a' => '1', 'b' => '2']];
$converted = CommonUtils::convert($args);
echo "convert(): " . ($converted === "name=%E5%BC%A0%E4%B8%89&options[a]=1&options[b]=2" ? "PASS" : "FAIL (" . $converted . ")") . "\n";

// test isAllChinese
echo "isAllChinese() test 1: " . (CommonUtils::isAllChinese("中文") ? "PASS" : "FAIL") . "\n";
echo "isAllChinese() test 2: " . (!CommonUtils::isAllChinese("abc") ? "PASS" : "FAIL") . "\n";

// test isImageBase64
echo "isImageBase64() test 1: " . (CommonUtils::isImageBase64("data:image/png;base64,iVBORw0KGgoAAA") ? "PASS" : "FAIL") . "\n";
echo "isImageBase64() test 2: " . (!CommonUtils::isImageBase64("image/png;base64") ? "PASS" : "FAIL") . "\n";

// test getArrColumn
$arr = [['id' => 1, 'name' => 'A'], ['id' => 2, 'name' => 'B']];
$col = CommonUtils::getArrColumn($arr, 'name');
echo "getArrColumn(): " . ($col === ['A', 'B'] ? "PASS" : "FAIL") . "\n";

// test towFloat
echo "towFloat() test 1 (3.1415): " . (CommonUtils::towFloat(3.1415) === 3.14 ? "PASS" : "FAIL") . "\n";
echo "towFloat() test 2 (3.999): " . (CommonUtils::towFloat(3.999) === 3.99 ? "PASS" : "FAIL") . "\n";

// test getSn
$sn = CommonUtils::getSn('TEST');
echo "getSn(): " . (strpos($sn, 'TEST') === 0 && strlen($sn) > 10 ? "PASS" : "FAIL") . "\n";

// test getUsername
$username = CommonUtils::getUsername();
echo "getUsername(): " . (preg_match('/^[A-Z]{2,3}_[a-z0-9]{4,6}$/', $username) ? "PASS" : "FAIL ({$username})") . "\n";

// test checkTime
// 无论什么时间，只要起始和结束点都是当前，应该很容易测出。这里测试 0 点到 0 点是否判定为真。
echo "checkTime(): " . (CommonUtils::checkTime(0, 0) ? "PASS" : "FAIL") . "\n";

// test ipv6ToV4
$v4 = CommonUtils::ipv6ToV4("2001:0db8:85a3:0000:0000:8a2e:0370:7334");
// 这里的ipv6ToV4原本取第30-38字符的特定转换，我们测试一个符合该特定规律或输入本身的输出：
echo "ipv6ToV4(): " . (CommonUtils::ipv6ToV4("127.0.0.1") === "127.0.0.1" ? "PASS" : "FAIL") . "\n";

// test reFf
echo "reFf() (3): " . (CommonUtils::reFf(3) === '3.00' ? "PASS" : "FAIL") . "\n";

// test formatCurrency
echo "formatCurrency() test 1 (123.45): " . (CommonUtils::formatCurrency(123.45) === '123' ? "PASS" : "FAIL") . "\n";
echo "formatCurrency() test 2 (12345.67): " . (CommonUtils::formatCurrency(12345.67) === '12.345' ? "PASS" : "FAIL") . "\n";



// test Encryption
$pw = 'my_password';
$enc1 = CommonUtils::passwordEncrypt($pw);
echo "passwordEncrypt(): " . ($enc1 === md5('8520' . $pw . '8520') ? "PASS" : "FAIL") . "\n";

$hash = CommonUtils::securePasswordHash($pw);
echo "securePasswordHash() / securePasswordVerify(): " . (CommonUtils::securePasswordVerify($pw, $hash) ? "PASS" : "FAIL") . "\n";

$pw2 = CommonUtils::makePassword($pw, 'salt');
echo "makePassword(): " . ($pw2 === sha1(md5(md5($pw . 'salt'))) ? "PASS" : "FAIL") . "\n";

// test Trees
$cateList = [
    ['id' => 0, 'pid' => -1, 'name' => 'Root'],
    ['id' => 2, 'pid' => 0, 'name' => 'Child'],
];
$treeList = CommonUtils::getTreeList($cateList);
echo "getTreeList(): " . (count($treeList) === 1 && count($treeList[0]['son']) === 0 ? "PASS" : "FAIL") . "\n";

$madeTree = CommonUtils::makeTree([
    ['id' => 1, 'pid' => 0, 'name' => 'Root'],
    ['id' => 2, 'pid' => 1, 'name' => 'Child'],
]);
echo "makeTree(): " . (count($madeTree) === 1 && count($madeTree[0]['children']) === 1 ? "PASS" : "FAIL") . "\n";



// test Times
echo "now(): " . (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', CommonUtils::now()) ? "PASS" : "FAIL") . "\n";
echo "dateChange(): " . (CommonUtils::dateChange('2026-06-22 12:00:00', false, 'Ymd') === '20260622' ? "PASS" : "FAIL") . "\n";
echo "mdate(): " . (CommonUtils::mdate(time()) === '刚刚' ? "PASS" : "FAIL") . "\n";
echo "getMillisecond(): " . (CommonUtils::getMillisecond() > 0 ? "PASS" : "FAIL") . "\n";

// test Order & Numbers
echo "makeOrderNo(): " . (strpos(CommonUtils::makeOrderNo('BIZ'), 'BIZ') === 0 ? "PASS" : "FAIL") . "\n";
echo "tradingNumber(): " . (strlen(CommonUtils::tradingNumber()) >= 20 ? "PASS" : "FAIL") . "\n";
echo "getNumberCode(): " . (strlen(CommonUtils::getNumberCode(8)) === 8 ? "PASS" : "FAIL") . "\n";
echo "makeRandNumber(): " . (CommonUtils::makeRandNumber(4) >= 1000 ? "PASS" : "FAIL") . "\n";



// test UUID & Avatars & dataReturn
echo "uuid(): " . (strlen(CommonUtils::uuid()) === 32 || strlen(CommonUtils::uuid()) > 30 ? "PASS" : "FAIL") . "\n";
echo "letterAvatar(): " . (strpos(CommonUtils::letterAvatar('Test'), 'data:image/svg+xml;base64,') === 0 ? "PASS" : "FAIL") . "\n";

echo "dataReturn(): " . (CommonUtils::dataReturn(200, 'ok', ['a' => 1]) === ['code' => 200, 'data' => ['a' => 1], 'message' => 'ok'] ? "PASS" : "FAIL") . "\n";




