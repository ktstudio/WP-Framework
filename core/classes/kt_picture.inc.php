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
     * @return $this
     */
    public function setSizes(array $sizes)
    {
        $this->sizes = $sizes;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
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
        foreach ($this->getSizes() as $maxWidth => $source) {
            if (KT::isIdFormat($maxWidth)) {
                if (is_array($source)) {
                    $html .= "<source srcset=\"{$this->tryGetSrcsetValue($source)}\" media=\"(max-width: {$maxWidth}px)\">";
                } else {
                    $html .= "<source src=\"$source\" media=\"(max-width: {$maxWidth}px)\">";
                }
            }
        }
        $html .= parent::buildHtml();
        $html .= "</picture>";
        return $html;
    }

    /**
     * @param array $sizes (další) velikosti ["do velikosti [px]" => $src]
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     */
    public static function render($sizes, $alt = "", $class = null, $isLazyLoading = true)
    {
        $image = new KT_Picture();
        foreach ($sizes as $maxWidth => $fileName) {
            if (!KT::stringStartsWith($fileName, "http://")) {
                $sizes[$maxWidth] = KT::imageGetUrlFromTheme($fileName);
            }
        }
        $image->setSrc(reset($sizes));
        $image->setSizes(KT::arrayRemoveByKey($sizes, ""));
        $image->setAlt($alt);
        $image->setClass($class);
        $image->setIsLazyLoading($isLazyLoading);
        echo $image->buildHtml();
    }

    /**
     * @param array $sizes (další) velikosti ["do velikosti [px]" => [zoom number => $srcset]]
     * @param string $alt
     * @param string $class
     * @param bool $isLazyLoading
     */
    public static function renderSet(array $sizes, $alt = "", $class = null, $isLazyLoading = true)
    {
        $image = new KT_Picture();
        foreach ($sizes as $maxWidth => $srcset) {
            foreach ($srcset as $zoomNumber => $fileName) {
                $srcset[$zoomNumber] = KT::imageGetUrlFromTheme($fileName);
            }
            $sizes[$maxWidth] = $srcset;
        }
        $image->setSrcset(reset($sizes));
        $image->setSizes($sizes);
        $image->setAlt($alt);
        $image->setClass($class);
        $image->setIsLazyLoading($isLazyLoading);
        echo $image->buildHtml();
    }

    /**
     * @param array $params k inicializaci
     * @param bool $isLazyLoading
     */
    public static function printArgs(array $params)
    {
        $image = new KT_Picture();
        $image->initialize($params);
        echo $image->buildHtml();
    }
}
