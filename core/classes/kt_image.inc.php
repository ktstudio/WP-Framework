<?php

/**
 * Třída pro snadnější výpis obrázků - stačí naplnit požadované parametry a zavolat ->render()
 * K diposizici jsou statické helpery ::render() a  ::renderSet()
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Image
{
    private $src;
    private $srcset;
    private $width;
    private $height;
    private $alt;
    private $class;
    private $isLazyLoading;

    /**
     * @param string $src
     * @param string $alt
     * @param string $class
     * @param boolean $isLazyLoading
     */
    public function __construct($src, $alt = "", $class = null, $isLazyLoading = true)
    {
        $this->setSrc($src);
        $this->setAlt($alt);
        $this->setClass($class);
        $this->setIsLazyLoading($isLazyLoading);
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
        return $this->srcset;
    }

    /**
     * @param string $srcset
     * @param string $srcset
     * @return $this
     */
    public function setSrcset($src1x, $src2x)
    {
        $this->srcset = "$src1x 1x, $src2x 2x";
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
        if (array_key_exists("src", $params)) {
            $this->setClass($params["src"]);
        }
        if (array_key_exists("srcset", $params)) {
            $this->setClass($params["srcset"]);
        }
        if (array_key_exists("width", $params)) {
            $this->setClass($params["width"]);
        }
        if (array_key_exists("height", $params)) {
            $this->setClass($params["height"]);
        }
        if (array_key_exists("alt", $params)) {
            $this->setClass($params["alt"]);
        }
        if (array_key_exists("class", $params)) {
            $this->setClass($params["class"]);
        }
        return $this;
    }

    /** @return string */
    public function buildHtml()
    {
        $html = "<img ";
        $html .= $this->tryGetImageParam("src", $this->getSrc());
        $html .= $this->tryGetImageParam("srcset", $this->getSrcset());
        $html .= $this->tryGetImageParam("width", $this->getWidth());
        $html .= $this->tryGetImageParam("height", $this->getHeight());
        $html .= $this->tryGetImageParam("alt", $this->getAlt());
        $html .= $this->tryGetImageParam("class", $this->getClass());
        $html .= "/>";
        if ($this->getIsLazyLoading()) {
            $html = KT::imageReplaceLazySrc($html);
        }
        return $html;
    }

    public static function render($src, $alt = "", $class = null, $isLazyLoading = true)
    {
        $image = new KT_Image($src, $alt, $class, $isLazyLoading);
        echo $image->buildHtml();
    }

    public static function renderSet($src1x, $src2x, $alt = "", $class = null, $isLazyLoading = true)
    {
        $image = new KT_Image($src1x, $alt, $class, $isLazyLoading);
        $image->setSrcset($src1x, $src2x);
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
}
