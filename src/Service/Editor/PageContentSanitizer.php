<?php

declare(strict_types=1);

namespace App\Paging\Service\Editor;

use App\Paging\ServiceInterface\Editor\PageContentSanitizerInterface;

final class PageContentSanitizer implements PageContentSanitizerInterface
{
    /** @var list<string> */
    private const ALLOWED_TAGS = [
        'a', 'blockquote', 'br', 'code', 'div', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'hr', 'li', 'ol', 'p', 'pre', 'span', 'strong', 'table', 'tbody', 'td', 'th', 'thead', 'tr', 'ul',
    ];

    public function sanitizeHtml(string $html): string
    {
        $html = preg_replace('/<\s*(script|style|iframe|object|embed|form|input|button|textarea|select)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $html) ?? '';
        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        $html = preg_replace('/\s+(style|srcdoc)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        $html = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:[^"\']*("|\')/i', '$1="#"', $html) ?? '';

        return trim(strip_tags($html, $this->allowedTagString()));
    }

    public function toPlainText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }

    private function allowedTagString(): string
    {
        return '<'.implode('><', self::ALLOWED_TAGS).'>';
    }
}
