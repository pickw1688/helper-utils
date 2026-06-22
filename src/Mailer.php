<?php

namespace Pick\HelperUtils;

class Mailer
{
    protected $smtp_host;
    protected $smtp_port;
    protected $smtp_username;
    protected $smtp_password;
    protected $from_email;
    protected $from_name;
    protected $error = '';

    /**
     * Mailer constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->smtp_host = $config['smtp_host'] ?? 'smtp.gmail.com';
        $this->smtp_port = $config['smtp_port'] ?? 587;
        $this->smtp_username = $config['smtp_username'] ?? '';
        $this->smtp_password = $config['smtp_password'] ?? ''; // App-specific password
        $this->from_email = $config['from_email'] ?? $this->smtp_username;
        $this->from_name = $config['from_name'] ?? 'Mailer';
    }

    /**
     * Get the last error message.
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Send email via SMTP (STARTTLS)
     *
     * @param string $to_email
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function send($to_email, $subject, $body)
    {
        if (empty($this->smtp_username) || empty($this->smtp_password)) {
            $this->error = 'Gmail配置错误：用户名或密码为空';
            return false;
        }

        $socket = null;

        try {
            $socket = @fsockopen($this->smtp_host, $this->smtp_port, $errno, $errstr, 30);
            if (!$socket) {
                $this->error = "Gmail连接失败：{$errstr} ({$errno})";
                return false;
            }

            stream_set_timeout($socket, 30);

            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '220') {
                $this->error = "服务器响应错误：{$response}";
                fclose($socket);
                return false;
            }

            fputs($socket, "EHLO " . $this->smtp_host . "\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) == ' ') break;
            }

            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '220') {
                $this->error = "STARTTLS失败：{$response}";
                fclose($socket);
                return false;
            }

            $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                $crypto_method = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            }

            if (!@stream_socket_enable_crypto($socket, true, $crypto_method)) {
                $this->error = "TLS加密启用失败";
                fclose($socket);
                return false;
            }

            fputs($socket, "EHLO " . $this->smtp_host . "\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) == ' ') break;
            }

            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '334') {
                $this->error = "AUTH LOGIN失败：{$response}";
                fclose($socket);
                return false;
            }

            fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '334') {
                $this->error = "用户名认证失败：{$response}";
                fclose($socket);
                return false;
            }

            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '235') {
                $this->error = "密码认证失败：{$response}";
                fclose($socket);
                return false;
            }

            fputs($socket, "MAIL FROM: <" . $this->from_email . ">\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                $this->error = "设置发件人失败：{$response}";
                fclose($socket);
                return false;
            }

            fputs($socket, "RCPT TO: <" . $to_email . ">\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                $this->error = "设置收件人失败：{$response}";
                fclose($socket);
                return false;
            }

            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '354') {
                $this->error = "DATA命令失败：{$response}";
                fclose($socket);
                return false;
            }

            $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
            $headers .= "To: " . $to_email . "\r\n";
            $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: base64\r\n";
            $headers .= "\r\n";

            $email_content = $headers . chunk_split(base64_encode($body)) . "\r\n.\r\n";
            fputs($socket, $email_content);

            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                $this->error = "发送邮件内容失败：{$response}";
                fclose($socket);
                return false;
            }

            fputs($socket, "QUIT\r\n");
            fclose($socket);

            return true;
        } catch (\Exception $e) {
            $this->error = "异常：" . $e->getMessage();
            if ($socket) {
                @fclose($socket);
            }
            return false;
        }
    }
}
