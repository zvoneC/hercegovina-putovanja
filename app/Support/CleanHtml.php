<?php

namespace App\Support;

class CleanHtml
{
    public static function clean(string $html): string
    {
        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><div>'.$html.'</div>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return self::nodes($document->getElementsByTagName('body')->item(0));
    }

    private static function nodes(\DOMNode $parent): string
    {
        $result = '';
        foreach ($parent->childNodes as $node) {
            if ($node instanceof \DOMText) {
                $result .= htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            } elseif ($node instanceof \DOMElement) {
                $tag = strtolower($node->tagName);
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math'])) {
                    continue;
                }
                if ($tag === 'br') {
                    $result .= '<br>';

                    continue;
                }
                $inside = self::nodes($node);
                $result .= in_array($tag, ['p', 'strong', 'b', 'em', 'i', 'u', 'h2', 'h3', 'ol', 'ul', 'li', 'blockquote']) ? '<'.$tag.'>'.$inside.'</'.$tag.'>' : $inside;
            }
        }

        return $result;
    }
}
