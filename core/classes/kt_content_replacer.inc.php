<?php

/**
 * Obecný pomocný nástroj pro nahrazování zástupných tagů konkrétními hodnotami ze systému v textu
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Content_Replacer {

    const TagPrefix = '{';
    const TagSuffix = '}';

// todo: základní globální proměnné

    private $currentTags;
    private $identifiable;
    private $globalInfo;
    private static $globalTags = array(
        "getName" => "web:název",
        "getDescription" => "web:popis",
        "getWpUrl" => "web:wpurl",
        "getUrl" => "web:url",
        "getAdminEmail" => "admin:email",
        "getUserLogin" => "uživatel:uživatelské-jméno",
        "getUserEmail" => "uživatel:email",
        "getUserFirstName" => "uživatel:jméno",
        "getUserLastName" => "uživatel:příjmení",
        "getUserDisplayName" => "uživatel:zobrazované-jméno",
        "getUserId" => "uživatel:id",
    );

    function __construct(array $currentTags = null, KT_Identifiable $identifiable = null) {
        if (KT::issetAndNotEmpty($currentTags)) {
            $this->currentTags = $currentTags;
        } else {
            error_log("Empty Current Tags for KT_Content_Replacer");
        }
        $this->setItem($identifiable);
    }

    /**
     * Nastavení aktuálního záznamu získání hodnot k nahrazování
     * @param \KT_Identifiable $identifiable
     * @return \KT_Content_Replacer
     */
    public function setItem(KT_Identifiable $identifiable = null) {
        if (KT::issetAndNotEmpty($identifiable)) {
            $this->identifiable = $identifiable;
        }
        return $this;
    }

    /**
     * Nahradí zadaný text případnými hodnotami pro zadaný záznam a tagy
     * @param string $content
     * @return string
     * @throws InvalidArgumentException
     */
    public function update($content) {
        if (KT::issetAndNotEmpty($content) && is_string($content)) {
            $identifiable = $this->identifiable;
            if (KT::issetAndNotEmpty($identifiable)) {
                // aktuální tagy
                $content = self::updateContentByTags($this->currentTags, $content, $identifiable);
            }
            // globální tagy
            $content = self::updateContentByTags(self::$globalTags, $content, $this->getGlobalInfo());
            return $content;
        }
        throw new InvalidArgumentException("content");
    }

    /**
     * Ověření, zda zadaný obsah obsahuje tagy (s největší pravděpodobností :)
     * @param string $content
     * @return boolean ověření existence
     */
    public static function check($content) {
        if (KT::issetAndNotEmpty($content) && is_string($content)) {
            $tagPrefixContains = KT::stringContains($content, self::TagPrefix);
            $tagSuffixContains = KT::stringContains($content, self::TagSuffix);
            return $tagPrefixContains && $tagSuffixContains;
        }
        return false;
    }

    /**
     * Získání aktuálních hodnot tagů pro výpis jako stringové pole
     * @return array
     */
    public function getCurrentTagsValuesText() {
        return $this->getTagsValuesText($this->currentTags);
    }

    /**
     * Získání aktuálních hodnot tagů pro výpis jako stringové pole
     * @return array
     */
    public function getGlobalTagsValuesText() {
        return $this->getTagsValuesText(self::$globalTags);
    }

    private function getTagsValuesText(array $tags) {
        $values = null;
        foreach ($tags as $tag) {
            $values .= "{{$tag}}<br />";
        }
        return $values;
    }

    private function getGlobalInfo() {
        if (KT::issetAndNotEmpty($this->globalInfo)) {
            return $this->globalInfo;
        }
        return ($this->globalInfo = new KT_WP_Info());
    }

    private static function updateContentByTags(array $tags, $content, $item) {
        foreach ($tags as $key => $value) {
            try {
                $tag = self::TagPrefix . $value . self::TagSuffix;
                if (KT::stringContains($content, $tag)) {
                    $result = self::getItemValueResult($item, $key);
                    $content = str_ireplace($tag, $result, $content);
                }
            } catch (Exception $exception) {
                log($exception);
            }
        }
        return $content;
    }

    private static function getItemValueResult($item, $key) {
        $methods = explode("->", $key);
        $result = $item;
        foreach ($methods as $method) {
            $result = $result->$method();
            if (KT::issetAndNotEmpty($result)) {
                continue;
            } else {
                return null;
            }
        }
        return $result;
    }

}
