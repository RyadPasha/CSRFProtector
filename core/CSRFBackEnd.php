<?php

/**
 * CSRFProtector
 * A standalone php library for csrf mitigation in web applications.
 * @package
 * @author Mohamed Riyad
 * @copyright Mohamed Riyad
 * @version 1.0-2018
 * @access public
 * @license GNU v3
 */
class CSRFBackEnd
{
    private $dom;
    private $tokenManager;
    private $jsPath;

    public function __construct(TokenManager $tokenManager, $jsPath)
    {
        $this->dom = new DOMDocument();
        $this->tokenManager = $tokenManager;
        $this->jsPath = $jsPath;
    }


    public function loadObContents()
    {
        @$this->dom->loadHTML(ob_get_clean());
        ob_start();
    }

    public function saveObContents()
    {
        echo $this->dom->saveHTML();
    }

    public function protectLinks()
    {
        foreach ($this->dom->getElementsByTagName("a") as $domNode)
        {
            $href = $this->protectUrl($domNode->getAttribute('href'));
            if ($href == false)
            {
                continue;
            }
            $domNode->setAttribute('href', $href);
        }
    }

    public function protectForms()
    {
        foreach ($this->dom->getElementsByTagName("form") as $domNode)
        {
            $action = $domNode->getAttribute('action');
            if (strtolower($domNode->getAttribute("method")) == "get")
            {
                $href = $this->protectUrl($action);
                if ($href == false)
                {
                    continue;
                }
                $domNode->setAttribute('action', $href);
            }
            else
            {
                $element = $this->dom->createElement('input', '');
                $element->setAttribute('type', 'hidden');
                $element->setAttribute('name', 'csrftoken');
                $element->setAttribute('value', $this->tokenManager->applyNewToken());
                $domNode->appendChild($element);
            }
        }
    }

    public function protectUrl($href)
    {
        if (!$this->isHrefToThisServer($href))
        {
            return false;
        }
        $token = $this->tokenManager->applyNewToken();
        $href = (strpos($href, '?') !== false) ? "$href&" : "$href?";
        $href .= "csrftoken=$token";
        return $href;
    }

    public function addHistoryScript()
    {
        $token = $this->tokenManager->applyNewToken();

        $history = $this->dom->createElement("script");
        $history->setAttribute('src', $this->jsPath . '/native.history.js');

        $titleElement = $this->dom->getElementsByTagName('title');
        $title = (!empty($titleElement)) ? $titleElement->item(0)->nodeValue : null;

        $scriptText = "window.onload=function(){
            (function(window,undefined){
                History.pushState({state:1}, '$title', '?csrftoken=$token');
            })(window);
        };";
        $script = $this->dom->createElement("script");
        $script->appendChild($this->dom->createTextNode($scriptText));

        $body = $this->dom->getElementsByTagName("body")->item(0);
        $body->appendChild($history);
        $body->appendChild($script);
    }
    public function protectRedirect()
    {
        //clean redirect called by header() function
        foreach (apache_response_headers() as $key => $value)
        {
            if (strtolower($key) == "location")
            {
                $href = $this->protectUrl($value);
                if ($href !== false)
                {
                    header("$key: $href");
                    return;
                }
            }
        }
        //clean redirect called by meta tag
        $metaElements = $this->dom->getElementsByTagName('meta');
        foreach ($metaElements as $metaElement)
        {
            $httpeq = $metaElement->getAttribute("http-equiv");
            $content = $metaElement->getAttribute("content");
            if (!empty($httpeq) && strtolower($httpeq) == "refresh" && !empty($content))
            {
                $a = split(";", $content);
                $seconds = $a[0];
                if (count($a) > 1)
                {
                    $url = $a[1];
                    if (!empty($url))
                    {
                        $url = substr($url, strpos($url, "=") + 1);
                        $url = $this->protectUrl($url);
                        $metaElement->setAttribute('content', "$seconds;URL=$url");
                    }
                }
                break;
            }
        }

        //clean redirect called by javascript
        //to do

    }
    private function isHrefToThisServer($href)
    {
        return parse_url($href, PHP_URL_HOST) == $_SERVER["HTTP_HOST"] || parse_url($href, PHP_URL_HOST) == null;
    }
}

?>
