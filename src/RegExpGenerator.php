<?php

namespace bpteam\QuickParserWizard;

class RegExpGenerator
{
    /**
     * @param string $htmlTag
     * @param bool $strict if true, then the regexp must be strictly equal to the tag
     * @return string
     */
    public function regexpByHtml(string $htmlTag, bool $strict = false): string
    {
        if ($strict) {
            return preg_quote($htmlTag, '~');
        } else {
            $replaceData = [
                '~</(\w+)>~' => '(.*?)</$1>',
                '~(\S+\s*=)~' => '[^>]*$1',
                '~>$~ms' => '[^>]*>',
                '~^<(\w+)~ms' => '<$1[^>]*',
                '~\s*=\s*["\']?([^"\']+)["\']?~ms' => '\s*=\s*["\']?[^"\']*$1[^"\']*["\']?[^>]*',
                '~\s+~ms' => '\s*',
            ];

            return preg_replace(array_keys($replaceData), array_values($replaceData), $htmlTag);
        }
    }

    public function regexpByCssSelector(string $selector): string
    {
        $html = $this->generateHtmlFromSelector($selector);

        return $this->regexpByHtml($html);
    }

    private function generateHtmlFromSelector($selector) {
        $parts = explode(' ', $selector);
        $html = '';

        foreach ($parts as $part) {
            $tag = $this->getTag($part);
            $attributes = $this->getAttributes($part);

            $html .= "<$tag" . $attributes . ">";
        }

//        for ($i = 0; $i < count($parts); $i++) {
//            $html .= "</" . $this->getTag($parts[$i]) . ">";
//        }

        return $html;
    }

    private function getTag($selectorPart) {
        if (str_starts_with($selectorPart, '.') || str_starts_with($selectorPart, '#')) {
            return 'div';
        }
        // Getting the tag name (e.g., 'div', 'span')
        $tag = explode('.', $selectorPart)[0];
        $tag = explode('#', $tag)[0];

        return $tag;
    }

    private function getAttributes($selectorPart) {
        $attributes = '';

        // Handling class
        if ($pos = strpos($selectorPart, '.')) {
            $class = substr($selectorPart, $pos + 1);
            // Removing id part if present
            $class = explode('#', $class)[0];
            $attributes .= " class='$class'";
        }

        // Handling id
        if ($pos = strpos($selectorPart, '#')) {
            $id = substr($selectorPart, $pos + 1);
            $attributes .= " id='$id'";
        }

        return $attributes;
    }
}