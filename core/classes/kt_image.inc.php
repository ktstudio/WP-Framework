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
    private $srcsets;
    private $width;
    private $height;
    private $alt;
    private $class;
    private $data = array();
    private $isLazyLoading;

    /** @return string */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
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
     * @return $this
     */
    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }

    /** @return string */
    public function getSrcset()
    {
        return $this->srcsets;
    }

    /**
     * @param array $srcset [ZOOM number => image URL]
     * @return $this
     */
    public function setSrcset(array $srcset)
    {
        $this->srcsets = $srcset;
        return $this;
    }

    /** @return int */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /** @return string */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this
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
     * @return $this
     */
    public function addData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
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
     * @return $this
     */
    public function setIsLazyLoading($isLazyLoading)
    {
        $this->isLazyLoading = KT::tryGetBool($isLazyLoading);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
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
        if (KT::notIssetOrEmpty($this->getSrc()) && KT::issetAndNotEmpty($srcset)) {
            $this->setSrc(reset($srcset));
        }
        $html = "<img ";
        $html .= $this->tryGetImageParam("id", $this->getId());
        $html .= $this->tryGetImageParam("src", $this->getSrc());
        $html .= $this->tryGetImageParam("srcset", $this->tryGetSrcsetValue($srcset));
        $html .= $this->tryGetImageParam("width", $this->getWidth());
        $html .= $this->tryGetImageParam("height", $this->getHeight());
        $html .= $this->tryGetImageParam("alt", $this->getAlt());
        $html .= $this->tryGetImageParam("class", $this->getClass());
        $data = $this->getData();
        if (KT::arrayIssetAndNotEmpty($data)) {
            foreach ($data as $dataKey => $dataValue) {
                $html .= $this->tryGetImageParam("data-{$dataKey}", $dataValue);
            }
        }
        $html .= "/>";
        if ($this->getIsLazyLoading()) {
            $html = KT::imageReplaceLazySrc($html);
        }
        return $html;
    }

    /**
     * @param string $src relativní cesta pro KT::imageGetUrlFromTheme($src)
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     */
    public static function render($src, $alt = "", $class = null, $isLazyLoading = true)
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
        echo $image->buildHtml();
    }

    /**
     * @param array $srcset [ZOOM number => image URL] relativní cesta pro KT::imageGetUrlFromTheme($imageUrl)
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     */
    public static function renderSet(array $srcset, $alt = "", $class = null, $isLazyLoading = true)
    {
        $image = new KT_Image();
        foreach ($srcset as $zoomNumber => $imageUrl) {
            $srcset[$zoomNumber] = KT::imageGetUrlFromTheme($imageUrl);
        }
        $image->setSrcset($srcset);
        $image->setAlt($alt);
        $image->setClass($class);
        $image->setIsLazyLoading($isLazyLoading);
        echo $image->buildHtml();
    }

    /**
     * @param array $params k inicializaci
     * @param bool $isLazyLoading
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
     */
    public static function renderRetina($src, $alt = "", $class = null, $isLazyLoading = true)
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

    protected function tryGetSrcsetValue(array $srcset = null)
    {
        if (KT::arrayIssetAndNotEmpty($srcset)) {
            $srcsets = [];
            foreach ($srcset as $srcsetKey => $srcsetValue) {
                $srcsets[] = "$srcsetValue {$srcsetKey}x";
            }
            return implode(", ", $srcsets);
        }
        return null;
    }
}
