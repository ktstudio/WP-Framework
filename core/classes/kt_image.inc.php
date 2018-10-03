<?php

/**
 * Třída pro snadnější výpis obrázků <img> - stačí naplnit požadované parametry a zavolat ->render()
 * K diposizici jsou statické helpery ::render(), ::renderSet() a ::renderArgs()
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Image
{
    private $id;
    private $src;
    private $srcset;
    private $width;
    private $height;
    private $alt;
    private $title;
    private $class;
    private $data = [];
    private $isLazyLoading;
    private $isNoScript;

    /** @return string */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return KT_Image
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /** @return string */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param string $src
     * @return KT_Image
     */
    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }

    /** @return array|string */
    public function getSrcset()
    {
        return $this->srcset;
    }

    /**
     * @param array $srcset [ZOOM number => image URL] or "ready" string
     * @return KT_Image
     */
    public function setSrcset($srcset)
    {
        $this->srcset = $srcset;
        return $this;
    }

    /** @return int */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return KT_Image
     */
    public function setWidth($width)
    {
        $this->width = KT::tryGetInt($width);
        return $this;
    }

    /** @return int */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return KT_Image
     */
    public function setHeight($height)
    {
        $this->height = KT::tryGetInt($height);
        return $this;
    }

    /** @return string */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     * @return KT_Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /** @return string */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return KT_Image
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /** @return string */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return KT_Image
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /** @return array */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @param string $value
     * @return KT_Image
     */
    public function addData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return KT_Image
     */
    public function removeData($key)
    {
        unset($this->data[$key]);
        return $this;
    }

    /** @return boolean */
    public function getIsLazyLoading()
    {
        return $this->isLazyLoading;
    }

    /**
     * @param boolean $isLazyLoading
     * @return KT_Image
     */
    public function setIsLazyLoading($isLazyLoading)
    {
        $this->isLazyLoading = KT::tryGetBool($isLazyLoading);
        return $this;
    }

    /** @return boolean */
    public function getIsNoScript()
    {
        return $this->isNoScript;
    }

    /**
     * @param boolean $isNoScript
     * @return KT_Image
     */
    public function setIsNoScript($isNoScript)
    {
        $this->isNoScript = KT::tryGetBool($isNoScript);
        return $this;
    }

    /**
     * @param array $params
     * @return KT_Image
     */
    public function initialize(array $params)
    {
        if (array_key_exists("id", $params)) {
            $this->setId($params["id"]);
        }
        if (array_key_exists("src", $params)) {
            $this->setSrc($params["src"]);
        }
        if (array_key_exists("srcset", $params)) {
            $this->setSrcset($params["srcset"]);
        }
        if (array_key_exists("width", $params)) {
            $this->setWidth($params["width"]);
        }
        if (array_key_exists("height", $params)) {
            $this->setHeight($params["height"]);
        }
        if (array_key_exists("alt", $params)) {
            $this->setAlt($params["alt"]);
        }
        if (array_key_exists("title", $params)) {
            $this->setTitle($params["title"]);
        }
        if (array_key_exists("class", $params)) {
            $this->setClass($params["class"]);
        }
        if (array_key_exists("data", $params)) {
            $data = $params["data"];
            if (KT::arrayIssetAndNotEmpty($data)) {
                foreach ($data as $dataKey => $dataValue) {
                    $this->addData($dataKey, $dataValue);
                }
            }
        }
        return $this;
    }

    /** @return string */
    public function buildHtml()
    {
        $srcset = $this->getSrcset();
        if (KT::notIssetOrEmpty($this->getSrc()) && KT::arrayIssetAndNotEmpty($srcset)) {
            $this->setSrc(reset($srcset));
        }

        $imageTag = "<img ";
        $imageTag .= $this->tryGetImageParam("id", $this->getId());
        $imageTag .= $this->tryGetImageParam("src", $this->getSrc());
        $imageTag .= $this->tryGetImageParam("srcset", $this->tryGetSrcsetValue($this->getSrcset()));
        $imageTag .= $this->tryGetImageParam("width", $this->getWidth());
        $imageTag .= $this->tryGetImageParam("height", $this->getHeight());
        $imageTag .= $this->tryGetImageParam("alt", $this->getAlt());
        $imageTag .= $this->tryGetImageParam("title", $this->getTitle());
        $imageTag .= $this->tryGetImageParam("class", $this->getClass());
        $data = $this->getData();
        if (KT::arrayIssetAndNotEmpty($data)) {
            foreach ($data as $dataKey => $dataValue) {
                $imageTag .= $this->tryGetImageParam("data-{$dataKey}", $dataValue);
            }
        }
        $imageTag .= "/>";

        $html = $imageTag;
        if ($this->getIsLazyLoading()) {
            $html = KT::imageReplaceLazySrc($html, true);
        }
        if ($this->getIsNoScript()) {
            $html .= "<noscript>$imageTag</noscript>";
        }
        return $html;
    }

    /**
     * @param string $src relativní cesta pro KT::imageGetUrlFromTheme($src)
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     * @param bool $isNoScript
     */
    public static function render($src, $alt = "", $class = null, $isLazyLoading = true, $isNoScript = true)
    {
        $image = new KT_Image();
        if (KT::stringStartsWith($src, "http://") || KT::stringStartsWith($src, "https://")) {
            $image->setSrc($src);
        } else {
            $image->setSrc(KT::imageGetUrlFromTheme($src));
        }
        $image->setAlt($alt);
        $image->setClass($class);
        $image->setIsLazyLoading($isLazyLoading);
        $image->setIsNoScript($isNoScript);
        echo $image->buildHtml();
    }

    /**
     * @param array $srcset [ZOOM number => image URL] relativní cesta pro KT::imageGetUrlFromTheme($imageUrl)
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     * @param bool $isNoScript
     */
    public static function renderSet(array $srcset, $alt = "", $class = null, $isLazyLoading = true, $isNoScript = true)
    {
        $image = new KT_Image();
        foreach ($srcset as $zoomNumber => $imageUrl) {
            $srcset[$zoomNumber] = KT::imageGetUrlFromTheme($imageUrl);
        }
        $image->setSrcset($srcset);
        $image->setAlt($alt);
        $image->setClass($class);
        $image->setIsLazyLoading($isLazyLoading);
        $image->setIsNoScript($isNoScript);
        echo $image->buildHtml();
    }

    /**
     * @param array $params k inicializaci
     */
    public static function renderArgs(array $params)
    {
        $image = new KT_Image();
        $image->initialize($params);
        echo $image->buildHtml();
    }

    /**
     * @param array $src relativní cesta pro KT::imageGetUrlFromTheme($src), zajistí automaticky srcset 1x a 2x (dle formátu @2x)
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     * @param bool $isNoScript
     */
    public static function renderRetina($src, $alt = "", $class = null, $isLazyLoading = true, $isNoScript = true)
    {
        $image = new KT_Image();
        $src1x = KT::imageGetUrlFromTheme($src);
        $srcInfo = pathinfo($src);
        $srcName = $srcInfo["filename"];
        $src2x = KT::imageGetUrlFromTheme(str_replace($srcName, "{$srcName}@2x", $src));
        $image->setSrcset([1 => $src1x, 2 => $src2x]);
        $image->setAlt($alt);
        $image->setClass($class);
        $image->setIsLazyLoading($isLazyLoading);
        $image->setIsNoScript($isNoScript);
        echo $image->buildHtml();
    }

    /**
     * @param string $key
     * @param string $value
     * @return null|string
     */
    private function tryGetImageParam($key, $value)
    {
        if (isset($value)) {
            return "$key=\"$value\" ";
        }
        return null;
    }

    protected function tryGetSrcsetValue($srcset = null)
    {
        if (KT::issetAndNotEmpty($srcset)) {
            if (is_array($srcset)) {
                $srcsets = [];
                foreach ($srcset as $srcsetKey => $srcsetValue) {
                    $srcsets[] = "$srcsetValue {$srcsetKey}x";
                }
                return implode(", ", $srcsets);
            } else {
                return $srcset;
            }
        }
        return null;
    }
}
