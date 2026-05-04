<?php
declare(strict_types=1);

function send_mail(string $to, string $subject, string $body): bool
{
    $from      = cfg('mail', 'from_email') ?: 'no-reply@localhost';
    $fromName  = cfg('mail', 'from_name')  ?: 'BDMS';

    $headers   = [];
    $headers[] = 'From: ' . $fromName . ' <' . $from . '>';
    $headers[] = 'Reply-To: ' . $from;
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    return @mail($to, $subject, $body, implode("\r\n", $headers));
}
