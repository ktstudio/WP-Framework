<?php

/**
 * Třída pro snadnější výpis obrázků <picture> - stačí naplnit požadované parametry a zavolat ->render()
 * K diposizici jsou statické helpery ::print(), ::printSet() a ::printArgs()
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Picture extends KT_Image
{
    private $sizes;

    /** @return array */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @param array $sizes další velikosti ["do velikosti [px]" => $src/$srcset]
     * @return KT_Picture
     */
    public function setSizes(array $sizes)
    {
        $this->sizes = $sizes;
        return $this;
    }

    /**
     * @param array $params
     * @return KT_Picture
     */
    public function initialize(array $params)
    {
        if (array_key_exists("sizes", $params)) {
            $this->setSizes($params["sizes"]);
        }
        return parent::initialize($params);
    }

    /** @return string */
    public function buildHtml()
    {
        $html = "<picture>";
        $transparent = KT::imageGetTransparent();
        foreach ($this->getSizes() as $width => $source) {
            $srcset = is_array($source) ? $this->tryGetSrcsetValue($source) : $source;
            if (KT::isIdFormat($width)) {
                $media = "(max-width: {$width}px)";
            } else {
                $media = "($width)";
            }
            if ($this->getIsLazyLoading()) {
                $html .= "<source srcset=\"$transparent\" data-srcset=\"$srcset\" media=\"$media\">";
            } else {
                $html .= "<source srcset=\"$srcset\" media=\"$media\">";
            }
        }
        $isNoScript = $this->getIsNoScript();
        $this->setIsNoScript(false);
        $html .= parent::buildHtml();
        $html .= "</picture>";
        if ($isNoScript) {
            $this->setIsLazyLoading(false);
            $imageTag = parent::buildHtml();
            $html .= "<noscript>$imageTag</noscript>";
        }
        return $html;
    }

    /**
     * @param array $sizes (další) velikosti ["do velikosti [px]" => $src]
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     * @param bool $isNoScript
     */
    public static function render($sizes, $alt = "", $class = null, $isLazyLoading = true, $isNoScript = true)
    {
        $image = new KT_Picture();
        foreach ($sizes as $width => $fileName) {
            if (KT::stringStartsWith($fileName, "http://") || KT::stringStartsWith($fileName, "https://")) {
                $sizes[$width] = $fileName;
            } else {
                $sizes[$width] = KT::imageGetUrlFromTheme($fileName);
            }
        }
        $image->setSrc(reset($sizes));
        $image->setSizes(KT::arrayRemoveByKey($sizes, ""));
        $image->setAlt($alt);
        $image->setClass($class);
        $image->setIsLazyLoading($isLazyLoading);
        $image->setIsNoScript($isNoScript);
        echo $image->buildHtml();
    }

    /**
     * @param array $sizes (další) velikosti ["do velikosti [px]" => [zoom number => $srcset]]
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     * @param bool $isNoScript
     */
    public static function renderSet(array $sizes, $alt = "", $class = null, $isLazyLoading = true, $isNoScript = true)
    {
        $image = new KT_Picture();
        foreach ($sizes as $width => $srcset) {
            foreach ($srcset as $zoomNumber => $fileName) {
                $srcset[$zoomNumber] = KT::imageGetUrlFromTheme($fileName);
            }
            $sizes[$width] = $srcset;
        }
        $image->setSrcset(reset($sizes));
        $image->setSizes($sizes);
        $image->setAlt($alt);
        $image->setClass($class);
        $image->setIsLazyLoading($isLazyLoading);
        $image->setIsNoScript($isNoScript);
        echo $image->buildHtml();
    }

    /**
     * @param array $params k inicializaci
     */
    public static function printArgs(array $params)
    {
        $image = new KT_Picture();
        $image->initialize($params);
        echo $image->buildHtml();
    }
}
