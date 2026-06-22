<?php

// 如果本地有 vendor/autoload.php 则使用 composer 自动加载
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    // 否则本地 fallback 手动 require 测试
    require __DIR__ . '/src/Hello.php';
    require __DIR__ . '/src/Mailer.php';
}

use Pick\HelperUtils\Hello;
use Pick\HelperUtils\Mailer;

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

/**
 * 您的项目中集成此包后的 send_email 示例方法
 *
 * 您可以直接在您的 ThinkPHP 辅助函数文件（如 common.php）中这样重构您的 send_email 方法：
 */
function send_email_example($email, $code, $msg)
{
    // 1. 模拟短信状态与模板逻辑。
    // if (smsStatus($code) == 0) {
    //     return reSmsCode('001');
    // }
    // $email_template = Db::name('LcSms')->where(['code' => $code])->find();
    // if (empty($email_template)) {
    //     return reSmsCode('002');
    // }
    //
    // $email_code = $msg;
    // $sign = "【" . sysconf('yunpian_sign') . "】";
    // $msg = str_replace('【', '[', $msg);
    // $msg = str_replace('】', ']', $msg);
    // $emailMsg = str_replace('###', $msg, $sign . $email_template['msg']);
    // $emailSubject = $email_template['subject'] ??  lang('Verification_Code');

    // 2. 实例化并配置此包中的 Mailer
    $mailer = new Mailer([
        'smtp_host'     => 'smtp.gmail.com', // 或者使用 env('gmail.smtp_host', 'smtp.gmail.com')
        'smtp_port'     => 587,              // 或者使用 env('gmail.smtp_port', 587)
        'smtp_username' => 'your_gmail_username@gmail.com', // 对应您的配置
        'smtp_password' => 'your_app_password',             // 对应您的配置
        'from_name'     => 'YourAppName'
    ]);

    // 3. 发送邮件
    $subject = "测试主题";
    $body = "<h1>测试内容</h1>";
    $success = $mailer->send($email, $subject, $body);

    if ($success) {
        $recode = '000';
    } else {
        // 可以从 getError() 中获取具体的错误日志
        $error_msg = $mailer->getError();
        // Log::error("Gmail发送邮件失败：{$error_msg}");
        $recode = '009';
    }

    // 4. 后续记录日志和返回格式逻辑
    // $data = array(
    //     'email' => $email,
    //     'msg' => $emailMsg,
    //     'code' => $recode . '#' . reSmsCode($recode)['msg'],
    //     'time' => date('Y-m-d H:i:s'),
    //     'ip' => $email_code
    // );
    // Db::name('LcEmailList')->insert($data);
    // ...

    return $recode;
}
