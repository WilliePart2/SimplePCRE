<?php
namespace SimplePCRE;

/**
 * children
 * anchor
 * firstChildren
 * lastChildren
*/

class RegExp
{
    private $divider = '#';
    private $startPartRegExp;
    private $endPartRegExp = 'isxJ';

    public function __construct()
    {
        $this->startPartRegExp = $this->divider;
        $this->endPartRegExp = $this->divider . $this->endPartRegExp;
    }
    public function getTagContent($tagName, $params = false, $final = true)
    {
        $start = $this->startTag($tagName, $params, false);
        $end = $this->closeTag($tagName);

        $content = isset($params['children']) ? '[^<>]*?' . $params['children'] . '[^<>]*?' : "(?<{$tagName}_content> .*?)";
        $anchor = isset($params['anchor']) && $params['anchor'] ? $this->closeTag($tagName) . $params['anchor'] : '';
        $firstChild = isset($params['firstChild']) && $params['firstChild'] ? $params['firstChild'] : '';
        $lastChild = isset($params['lastChild']) && $params['lastChild'] ? $params['lastChild'] : '';

        $content = !empty($firstChild) ? $firstChild . $content : $content;
        $content = !empty($lastChild) ? $content . $lastChild : $content;

        $result = ($final === true ? $this->startPartRegExp : '')
            . $start
            . $content
            . $end
            . ($final === true ? $this->endPartRegExp : '');
        return $result;
    }
    public function startTag($tagName, $params = false, $final = true)
    {
        $any = ' [^<>]*? ';
        $startTag = "< $any $tagName $any";
        if($params !== false){
            $count = count($params);
            $counter = 0;
            foreach($params as $attrName => $attrValue){
                $counter+=1;
                if(isset($params['firstChildren']) && !empty($params['firstChildren'])) continue;
                if(isset($params['lastChildren']) && !empty($params['lastChildren'])) continue;
                if(isset($params['children']) && !empty($params['children'])) continue;
                if(isset($params['children']) && !empty($params['anchor'])) continue;

                if(!empty($attrValue))
                    preg_replace('#\\s#ixs', '\\s', $attrValue); // Тут могут быть проблемы

                if(!empty($attrValue))
                    $attrValue = " (?<$attrName> $attrValue) $any ";
                if(empty($attrValue))
                    $attrValue = "(?<$attrName> .*? )";
                $startTag .= ($counter === 0 ? $any : '') . $attrName . ' \\s* = \\s* (?<quot>[\'"])' . '\\s*' . $attrValue . '\g{quot} \\s* ';
            }
        }
        $startTag .= ((isset($counter) && isset($count)) && ($counter === $count) && $count !== 0 ? '' : '') . '/?>';
        $finish = ($final === true ? $this->startPartRegExp : '') . $startTag . ($final === true ? $this->endPartRegExp : '');
        return $finish;
    }
    public function closeTag($tagName)
    {
        return "< \\s* / \\s* $tagName \\s* >";
    }
    public function fetchSingleData($html, $regExp)
    {
        $result = [];
        preg_match($regExp, $html, $result);
        if($result != false){
            return $result;
        }
        return false;
    }
    public function fetchAllData($html, $regExp)
    {
        $result = [];
        preg_match_all($regExp, $html, $result);
        if($result != false){
            return $result;
        }
        return false;
    }
    public function setMode($mode)
    {
        $mode = trim($mode, '/\\#@!$%^&*~()[]{}|.,:;');
        $this->endPartRegExp = $this->divider . $mode;
    }
    public function setDivider($divider)
    {
        $this->divider = trim($divider);
    }
    public function write()
    {
        var_dump($this->startPartRegExp);
        var_dump($this->endPartRegExp);
    }
}