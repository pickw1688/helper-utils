# Helper Utils

这是一个自定义的 PHP 辅助工具类包，支持通过 Composer 进行管理和安装。

## 安装

你可以通过 Composer 安装此包：

```bash
composer require pick/helper-utils
```

## 使用方法

安装完成后，在你的 PHP 项目中引入 `vendor/autoload.php` 即可使用：

### 示例 1：基础 Hello 示例

```php
<?php

require 'vendor/autoload.php';

use Pick\HelperUtils\Hello;

$hello = new Hello();
echo $hello->say('Packagist');
// 输出: Hello, Packagist! Welcome to your custom Packagist package.
```

### 示例 2：Gmail SMTP 邮件发送（基于 fsockopen STARTTLS）

```php
<?php

require 'vendor/autoload.php';

use Pick\HelperUtils\Mailer;

$mailer = new Mailer([
    'smtp_host'     => 'smtp.gmail.com',
    'smtp_port'     => 587,
    'smtp_username' => 'your_email@gmail.com',
    'smtp_password' => 'your_gmail_app_password', // 您的 Gmail 应用专用密码
    'from_name'     => '您的发件人名字'
]);

$success = $mailer->send('recipient@example.com', '邮件标题', '<h1>邮件正文（HTML格式）</h1>');

if ($success) {
    echo "邮件发送成功！";
} else {
    echo "发送失败，错误原因：" . $mailer->getError();
}
```

## 如何发布到 Packagist

1. 在 GitHub 上新建一个仓库，例如 `yourusername/helper-utils`。
2. 将本地代码推送到该仓库：
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin git@github.com:yourusername/helper-utils.git
   git branch -M main
   git push -u origin main
   ```
3. 访问 [Packagist.org](https://packagist.org/) 并登录你的账号。
4. 点击顶部的 **Submit** 按钮。
5. 输入你的 GitHub 仓库地址（例如 `https://github.com/yourusername/helper-utils`），点击 **Check**，最后点击 **Submit** 提交。
6. 推荐：在 GitHub 仓库中配置 Webhook，使每次推送代码时 Packagist 都会自动同步。
7. 在 GitHub 上创建一个 Release/Tag（例如 `v1.0.0`），以便大家可以直接拉取稳定版本。
