<?php
declare(strict_types=1);

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function flash_pull(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $messages;
}

function render_flashes(): string
{
    $html = '';
    foreach (flash_pull() as $f) {
        $type = in_array($f['type'], ['success','error','info'], true) ? $f['type'] : 'info';
        $html .= '<div class="alert alert-' . $type . '">' . e($f['message']) . '</div>';
    }
    return $html;
}
