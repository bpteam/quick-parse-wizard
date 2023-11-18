<?php

namespace bpteam\QuickParserWizard;

class TextExtractor
{
    public function betweenTags(string $text, string $tag, string $encoding = 'utf-8'): string
    {
        $tagName = $this->getTagName($tag);
        $startTag = $this->findTagLike($tag, $text);

        if (empty($startTag)) {
            return '';
        }

        $startPos = mb_strpos($text, $startTag, 0, $encoding);
        $text = mb_substr($text, $startPos, null, $encoding);
        $posEnd = $this->getClosingTagPosition($text, $tagName, $encoding);
        $startTagLen = mb_strlen($startTag, $encoding);
        return mb_substr($text, $startTagLen, $posEnd - $startTagLen, $encoding);
    }

    public function divideTextToSentences(string $text, int $partSize, int $offset = 0, string $encoding = 'utf-8'): array
    {
        $parts = [];
        if (mb_strlen($text, $encoding) >= $partSize) {
            for ($i = 0; ($offset === 0 || $i < $offset) && $text; $i++) {
                $partText = mb_substr($text, 0, $partSize, $encoding);
                preg_match('~^(.+[.?!]|$)~imsuU', $partText, $match);
                if (mb_strlen($match[1], $encoding) === 0) {
                    break;
                }
                $parts[] = $match[1];
                $text = trim(preg_replace('~' . preg_quote($match[1], '~') . '~ums', '', $text, 1));
            }
        } else {
            $parts[] = $text;
        }

        return $parts;
    }

    public function findTagLike(string $tag, string $inText): ?string
    {
        $tagName = $this->getTagName($tag);
        if (preg_match_all('~(?<attribute>([\w-]+\s?=\s?([\"\'][^\'\">]*[\"\']|[^\'\">\s]+)|(checked|disabled)))~im', $tag, $matches)) {
            $reg = '~(?<tag><' . preg_quote($tagName, '~');
            foreach ($matches['attribute'] as $value) {
                $reg .= '[^>]*' . preg_quote($value, '~') . '[^>]*';
            }
            $reg .= '>)~uim';
            preg_match($reg, $inText, $match);
        } else {
            preg_match('~(?<tag><' . preg_quote($tagName, '~') . '[^>]*>)~iu', $inText, $match);
        }

        return $match['tag'] ?? null;
    }

    private function getTagName(string $startTag): ?string
    {
        preg_match('~<(?<tag>\w+)[^>]*>~im', $startTag, $tag);

        return $tag['tag'] ?? null;
    }

    private function getClosingTagPosition(
        string $text,
        string $tagName,
        string $encoding = 'utf-8'
    ): int {
        $openTag = "<" . $tagName;
        $closeTag = "</" . $tagName;
        $countOpenTag = 0;
        $posEnd = 0;
        $countTag = preg_match_all('~' . preg_quote($openTag, '~') . '~uims', $text);
        $i = 0;
        do {
            $posOpenTag = mb_strpos($text, $openTag, $posEnd, $encoding);
            $posCloseTag = mb_strpos($text, $closeTag, $posEnd, $encoding);

            if ($posOpenTag !== false && $posOpenTag < $posCloseTag) {
                $countOpenTag++;
                $posEnd = $posOpenTag + 1;
            } else {
                $countOpenTag--;
                $posEnd = $posCloseTag + 1;
            }

            $i++;
        } while($i <= ($countTag * 2) && $countOpenTag !== 0);

        return $posEnd - 1;
    }
}