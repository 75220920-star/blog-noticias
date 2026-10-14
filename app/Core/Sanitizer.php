<?php

declare(strict_types=1);

namespace App\Core;

use DOMDocument;
use DOMNode;

final class Sanitizer
{
    public static function html(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $old = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($old);
        $clean = function (DOMNode $node) use (&$clean): string {
            if ($node->nodeType === XML_TEXT_NODE) {
                return \e($node->textContent);
            }
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                return '';
            }
            $tag = strtolower($node->nodeName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math', 'form', 'input', 'video', 'audio'], true)) {
                return '';
            }
            $content = '';
            foreach ($node->childNodes as $child) {
                $content .= $clean($child);
            }
            if (!in_array($tag, ['p', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'strong', 'em', 'blockquote', 'a', 'br'], true)) {
                return $content;
            }
            $attributes = '';
            if ($tag === 'a') {
                $href = trim($node->getAttribute('href'));
                if (preg_match('~^https?://~i', $href) && filter_var($href, FILTER_VALIDATE_URL)) {
                    $attributes = ' href="' . \e($href) . '" rel="nofollow noopener"';
                }
            }
            return '<' . $tag . $attributes . '>' . $content . ($tag === 'br' ? '' : '</' . $tag . '>');
        };
        $result = '';
        foreach ($document->childNodes as $node) {
            $result .= $clean($node);
        }
        return $result;
    }

    public static function headings(string $html): bool
    {
        if (preg_match('/<h[15-6]\b/i', $html)) {
            return false;
        }
        preg_match_all('/<h([2-4])\b/i', $html, $matches);
        $previous = 1;
        foreach ($matches[1] as $level) {
            if ((int) $level > $previous + 1) {
                return false;
            }
            $previous = (int) $level;
        }
        return true;
    }
}
