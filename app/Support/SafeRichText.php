<?php

namespace App\Support;

class SafeRichText
{
    /**
     * Render-time sanitizer for public rich text. Allows Quill-style rich text
     * and YouTube embeds, while removing scripts, event handlers, and unsafe URLs.
     */
    public static function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '<p><br></p>') {
            return null;
        }

        if (! class_exists(\DOMDocument::class)) {
            return strip_tags($value, '<p><br><strong><b><em><i><u><s><strike><sub><sup><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><pre><code><span><div>');
        }

        $document = new \DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><!DOCTYPE html><html><body>'.$value.'</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $body = $document->getElementsByTagName('body')->item(0);

        if (! $body) {
            return null;
        }

        self::sanitizeNode($body);

        $html = '';

        foreach ($body->childNodes as $childNode) {
            $html .= $document->saveHTML($childNode);
        }

        $html = trim($html);

        return $html !== '' ? $html : null;
    }

    private static function sanitizeNode(\DOMNode $node): void
    {
        if ($node instanceof \DOMComment) {
            $node->parentNode?->removeChild($node);

            return;
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            self::sanitizeNode($child);
        }

        if (! $node instanceof \DOMElement || in_array(strtolower($node->tagName), ['html', 'body'], true)) {
            return;
        }

        $tagName = strtolower($node->tagName);
        $allowedTags = self::allowedTags();

        if (in_array($tagName, ['script', 'style'], true)) {
            $node->parentNode?->removeChild($node);

            return;
        }

        if (! array_key_exists($tagName, $allowedTags)) {
            self::unwrapNode($node);

            return;
        }

        if ($tagName === 'iframe' && ! self::hasSafeYoutubeSource($node)) {
            $node->parentNode?->removeChild($node);

            return;
        }

        foreach (iterator_to_array($node->attributes) as $attribute) {
            $attributeName = strtolower($attribute->name);

            if (! in_array($attributeName, $allowedTags[$tagName], true)) {
                $node->removeAttributeNode($attribute);

                continue;
            }

            if ($attributeName === 'class') {
                self::sanitizeClassAttribute($node);
            }

            if ($attributeName === 'style') {
                self::sanitizeStyleAttribute($node);
            }

            if ($tagName === 'a' && $attributeName === 'href' && ! self::isSafeLinkUrl($attribute->value)) {
                $node->removeAttribute('href');
            }

            if ($tagName === 'iframe' && $attributeName === 'src' && ! self::isSafeYoutubeEmbedUrl($attribute->value)) {
                $node->parentNode?->removeChild($node);

                return;
            }
        }

        if ($tagName === 'a' && $node->hasAttribute('target')) {
            $node->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private static function unwrapNode(\DOMElement $node): void
    {
        $parent = $node->parentNode;

        if (! $parent) {
            return;
        }

        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }

    private static function sanitizeClassAttribute(\DOMElement $node): void
    {
        $classes = array_values(array_filter(
            preg_split('/\s+/', trim($node->getAttribute('class'))) ?: [],
            fn (string $class): bool => $class === 'ql-code-block'
                || in_array($class, [
                    'ql-lineheight-tight',
                    'ql-lineheight-normal',
                    'ql-lineheight-relaxed',
                    'ql-lineheight-loose',
                ], true)
                || preg_match('/^ql-indent-[1-8]$/', $class)
        ));

        if ($classes === []) {
            $node->removeAttribute('class');

            return;
        }

        $node->setAttribute('class', implode(' ', $classes));
    }

    private static function sanitizeStyleAttribute(\DOMElement $node): void
    {
        $styles = [];
        $allowedProperties = [
            'background-color',
            'color',
            'text-align',
        ];

        foreach (explode(';', $node->getAttribute('style')) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);

            if (! in_array($property, $allowedProperties, true) || ! self::isSafeCssValue($value)) {
                continue;
            }

            $styles[] = $property.': '.$value;
        }

        if ($styles === []) {
            $node->removeAttribute('style');

            return;
        }

        $node->setAttribute('style', implode('; ', $styles).';');
    }

    private static function isSafeCssValue(string $value): bool
    {
        $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($value === '' || preg_match('/(?:url\s*\(|expression\s*\(|javascript:|@import|behavior\s*:)/i', $value)) {
            return false;
        }

        return preg_match('/^[#(),.%\sa-z0-9-]+$/i', $value) === 1;
    }

    private static function hasSafeYoutubeSource(\DOMElement $node): bool
    {
        return $node->hasAttribute('src') && self::isSafeYoutubeEmbedUrl($node->getAttribute('src'));
    }

    private static function isSafeLinkUrl(string $value): bool
    {
        $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return str_starts_with($value, '/')
            || str_starts_with($value, '#')
            || preg_match('/^(https?:|mailto:|tel:)/i', $value);
    }

    private static function isSafeYoutubeEmbedUrl(string $value): bool
    {
        $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if (! preg_match('/^https:\/\//i', $value)) {
            return false;
        }

        $parts = parse_url($value);
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        return in_array($host, ['www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'youtube-nocookie.com'], true)
            && preg_match('#^/embed/[A-Za-z0-9_-]+$#', $path);
    }

    private static function allowedTags(): array
    {
        return [
            'a' => ['href', 'title', 'target', 'rel', 'style'],
            'b' => [],
            'blockquote' => ['class', 'style'],
            'br' => [],
            'code' => ['class'],
            'div' => ['class', 'style'],
            'em' => [],
            'h1' => ['class', 'style'],
            'h2' => ['class', 'style'],
            'h3' => ['class', 'style'],
            'h4' => ['class', 'style'],
            'h5' => ['class', 'style'],
            'h6' => ['class', 'style'],
            'i' => [],
            'iframe' => ['src', 'title', 'width', 'height', 'allow', 'allowfullscreen', 'frameborder', 'loading', 'referrerpolicy'],
            'li' => ['class', 'style'],
            'ol' => ['class', 'style'],
            'p' => ['class', 'style'],
            'pre' => ['class'],
            's' => [],
            'span' => ['class', 'style'],
            'strike' => [],
            'strong' => [],
            'sub' => [],
            'sup' => [],
            'u' => [],
            'ul' => ['class'],
        ];
    }

}
