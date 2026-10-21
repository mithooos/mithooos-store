<?php
/**
 * MITHOOOS — Email Service
 * Uses PHPMailer-compatible SMTP. Templates rendered as HTML.
 */
class Email {
    private array $config;

    public function __construct() {
        $this->config = [
            'host'     => defined('SMTP_HOST')  ? SMTP_HOST  : 'smtp.sendgrid.net',
            'port'     => defined('SMTP_PORT')  ? SMTP_PORT  : 587,
            'user'     => defined('SMTP_USER')  ? SMTP_USER  : 'apikey',
            'pass'     => defined('SMTP_PASS')  ? SMTP_PASS  : '',
            'from'     => defined('FROM_EMAIL') ? FROM_EMAIL : 'hello@mithooos.com',
            'from_name'=> defined('FROM_NAME')  ? FROM_NAME  : 'Mithooos',
            'base_url' => defined('APP_URL')    ? APP_URL    : 'https://mithooos.com',
        ];
    }

    /* ── Send via PHPMailer + Gmail SMTP ── */
    public function send(string $to, string $subject, string $html, string $text = ''): bool {
        $dir = __DIR__ . '/phpmailer';
        require_once $dir . '/Exception.php';
        require_once $dir . '/PHPMailer.php';
        require_once $dir . '/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $this->config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['user'];
            $mail->Password   = $this->config['pass'];
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)$this->config['port'];
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom($this->config['from'], $this->config['from_name']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text ?: strip_tags($html);
            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log('Email send failed: ' . $e->getMessage());
            return false;
        }
    }

    /* ── Order Confirmation ── */
    public function orderConfirmation(array $order, array $user): bool {
        $subject = "Order Confirmed #{$order['order_number']} — Mithooos";
        $firstName = htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $items   = array_map(function($i) {
            $pname = htmlspecialchars($i['product_name'] ?? '', ENT_QUOTES, 'UTF-8');
            $qty   = (int)($i['quantity'] ?? 1);
            $total = number_format((float)($i['item_total'] ?? 0), 2);
            return "<tr><td style='padding:8px 0;border-bottom:1px solid #F1F4F8'>{$pname}<br><small style='color:#9CA3AF'>Qty: {$qty}</small></td>
             <td style='padding:8px 0;border-bottom:1px solid #F1F4F8;text-align:right;font-weight:600'>PKR {$total}</td></tr>";
        }, $order['items'] ?? []);
        $subtotal  = number_format((float)($order['subtotal'] ?? 0), 2);
        $shipping  = (float)($order['shipping_cost'] ?? 0);
        $tax       = number_format((float)($order['tax'] ?? 0), 2);
        $total     = number_format((float)($order['total_amount'] ?? 0), 2);
        $shippingStr = $shipping == 0 ? 'FREE' : 'PKR ' . number_format($shipping, 2);
        $orderNum  = htmlspecialchars($order['order_number'] ?? '', ENT_QUOTES, 'UTF-8');
        $html = $this->wrap($subject, "
            <h2 style='color:#1A1A2E;margin-bottom:8px'>Your order is confirmed!</h2>
            <p style='color:#6B7280'>Hi {$firstName}, thank you for shopping with us.</p>
            <div style='background:#F8F9FC;border-radius:12px;padding:20px;margin:20px 0'>
                <p style='font-size:.8rem;color:#9CA3AF;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px'>Order Number</p>
                <p style='font-family:monospace;font-size:1.2rem;font-weight:800;color:#FF6B35'>{$orderNum}</p>
            </div>
            <table width='100%' style='border-collapse:collapse;margin-bottom:16px'>" . implode('', $items) . "</table>
            <table width='100%'>
                <tr><td>Subtotal</td><td style='text-align:right'>PKR {$subtotal}</td></tr>
                <tr><td>Shipping</td><td style='text-align:right'>{$shippingStr}</td></tr>
                <tr><td>Tax</td><td style='text-align:right'>PKR {$tax}</td></tr>
                <tr style='font-weight:800;font-size:1.1rem'><td>Total</td><td style='text-align:right;color:#FF6B35'>PKR {$total}</td></tr>
            </table>
            <div style='margin-top:24px;text-align:center'>
                <a href='{$this->config['base_url']}/account' style='display:inline-block;background:linear-gradient(135deg,#FF6B35,#FF3D7F);color:white;padding:12px 28px;border-radius:50px;text-decoration:none;font-weight:700'>Track Your Order</a>
            </div>
        ");
        return $this->send($user['email'], $subject, $html);
    }

    /* ── Shipping Notification ── */
    public function shippingNotification(array $order, array $user): bool {
        $subject   = "Your order #{$order['order_number']} has shipped!";
        $firstName = htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $tracking  = htmlspecialchars($order['tracking_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $html = $this->wrap($subject, "
            <h2 style='color:#1A1A2E'>Your order is on its way!</h2>
            <p style='color:#6B7280'>Hi {$firstName}, great news — your order has shipped.</p>
            <div style='background:#D1FAE5;border-radius:12px;padding:20px;margin:20px 0;border-left:4px solid #10B981'>
                <p style='font-size:.8rem;color:#065F46;text-transform:uppercase;letter-spacing:1px'>Tracking Number</p>
                <p style='font-family:monospace;font-size:1.1rem;font-weight:800;color:#065F46'>{$tracking}</p>
            </div>
            <p style='color:#6B7280'>Estimated delivery: <strong>3–5 business days</strong></p>
            <div style='margin-top:24px;text-align:center'>
                <a href='{$this->config['base_url']}/account' style='display:inline-block;background:linear-gradient(135deg,#FF6B35,#FF3D7F);color:white;padding:12px 28px;border-radius:50px;text-decoration:none;font-weight:700'>Track Package</a>
            </div>
        ");
        return $this->send($user['email'], $subject, $html);
    }

    /* ── Welcome / Registration ── */
    public function welcome(array $user, string $verifyToken): bool {
        $subject   = "Welcome to Mithooos!";
        $firstName = htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $link      = "{$this->config['base_url']}/api/auth/verify?token=" . urlencode($verifyToken);
        $html      = $this->wrap($subject, "
            <h2 style='color:#1A1A2E'>Welcome, {$firstName}!</h2>
            <p style='color:#6B7280'>We're so excited to have you join the Mithooos community.</p>
            <p style='color:#6B7280;margin-top:16px'>Please verify your email to unlock your exclusive <strong style='color:#FF6B35'>20% welcome discount</strong> with code <strong style='color:#FF6B35'>WELCOME20</strong>.</p>
            <div style='margin:28px 0;text-align:center'>
                <a href='{$link}' style='display:inline-block;background:linear-gradient(135deg,#FF6B35,#FF3D7F);color:white;padding:14px 32px;border-radius:50px;text-decoration:none;font-weight:700;font-size:1rem'>Verify My Email</a>
            </div>
            <p style='color:#9CA3AF;font-size:.8rem;text-align:center'>This link expires in 24 hours.</p>
        ");
        return $this->send($user['email'], $subject, $html);
    }

    /* ── Newsletter Confirmation ── */
    public function newsletterConfirm(string $email, string $token): bool {
        $subject     = "You're subscribed! Here's your 20% off code";
        $unsubscribe = "{$this->config['base_url']}/api/newsletter/unsubscribe?token=" . urlencode($token);
        $html        = $this->wrap($subject, "
            <h2 style='color:#1A1A2E'>You're in!</h2>
            <p style='color:#6B7280'>Thanks for subscribing to the Mithooos newsletter.</p>
            <div style='background:linear-gradient(135deg,#FFF0EB,#FFE4F0);border-radius:16px;padding:28px;margin:24px 0;text-align:center;border:2px dashed rgba(255,107,53,.3)'>
                <p style='font-size:.8rem;color:#9CA3AF;text-transform:uppercase;letter-spacing:2px;margin-bottom:8px'>Your Welcome Code</p>
                <p style='font-family:monospace;font-size:2rem;font-weight:900;color:#FF6B35;letter-spacing:4px'>WELCOME20</p>
                <p style='color:#6B7280;font-size:.8rem;margin-top:8px'>20% off your first order</p>
            </div>
            <div style='text-align:center'>
                <a href='{$this->config['base_url']}/shop' style='display:inline-block;background:linear-gradient(135deg,#FF6B35,#FF3D7F);color:white;padding:14px 32px;border-radius:50px;text-decoration:none;font-weight:700'>Shop Now</a>
            </div>
        ", $unsubscribe);
        return $this->send($email, $subject, $html);
    }

    /* ── Password Reset ── */
    public function passwordReset(array $user, string $token): bool {
        $resetUrl = $this->config['base_url'] . '/reset-password?token=' . urlencode($token);
        $subject  = 'Reset Your Password — Mithooos';
        $html     = $this->wrap($subject, "
            <h2 style='color:#1A1A2E;margin-bottom:8px'>Reset your password</h2>
            <p style='color:#6B7280;margin-bottom:20px'>Hi {$user['first_name']}, we received a request to reset your password.</p>
            <div style='text-align:center;margin:24px 0'>
                <a href='{$resetUrl}'
                   style='display:inline-block;background:linear-gradient(135deg,#FF6B35,#FF3D7F);color:white;padding:14px 32px;border-radius:50px;text-decoration:none;font-weight:700;font-size:1rem'>
                   Reset Password
                </a>
            </div>
            <p style='color:#9CA3AF;font-size:.8rem;text-align:center'>This link expires in <strong>1 hour</strong>.</p>
            <p style='color:#9CA3AF;font-size:.8rem;text-align:center;margin-top:16px'>
                If you didn't request this, you can safely ignore this email.<br>Your password will not change.
            </p>
            <div style='background:#F8F9FC;border-radius:10px;padding:12px 16px;margin-top:20px;word-break:break-all'>
                <p style='color:#9CA3AF;font-size:.72rem;margin:0'>Or paste this link in your browser:</p>
                <p style='color:#FF6B35;font-size:.72rem;margin:4px 0 0'>{$resetUrl}</p>
            </div>
        ");
        return $this->send($user['email'], $subject, $html);
    }

    /* ── HTML wrapper template ── */
    private function wrap(string $title, string $content, ?string $unsubscribeUrl = null): string {
        $unsubLink = $unsubscribeUrl
            ? htmlspecialchars($unsubscribeUrl, ENT_QUOTES, 'UTF-8')
            : htmlspecialchars($this->config['base_url'] . '/account', ENT_QUOTES, 'UTF-8');
        $baseUrl = htmlspecialchars($this->config['base_url'], ENT_QUOTES, 'UTF-8');
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
        <title>{$title}</title></head>
        <body style="margin:0;padding:0;background:#F8F9FC;font-family:'Segoe UI',sans-serif">
          <div style="max-width:560px;margin:40px auto;background:white;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#1A1A2E,#2D2D44);padding:28px 32px;text-align:center">
              <p style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.3rem;color:white;letter-spacing:2px;margin:0">MITHOOOS</p>
              <p style="color:rgba(255,255,255,.4);font-size:.65rem;letter-spacing:3px;margin:4px 0 0">SINDH KI KHUSHBOO</p>
            </div>
            <!-- Body -->
            <div style="padding:32px">{$content}</div>
            <!-- Footer -->
            <div style="background:#F8F9FC;padding:20px 32px;text-align:center;border-top:1px solid #E8ECF0">
              <p style="color:#9CA3AF;font-size:.75rem;margin:0">© 2026 Mithooos. All rights reserved.</p>
              <p style="color:#9CA3AF;font-size:.72rem;margin:6px 0 0">
                <a href="{$baseUrl}" style="color:#FF6B35;text-decoration:none">Visit Store</a> ·
                <a href="{$unsubLink}" style="color:#9CA3AF;text-decoration:none">Unsubscribe</a>
              </p>
            </div>
          </div>
        </body></html>
        HTML;
    }
}
