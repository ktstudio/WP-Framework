<?php

/**
 * Třída pro práci se stringem, kde má být aplikovaný markdown 
 * Syntaxe: *text* -> kurzíva, **text** -> tučný text, ~~text~~ -> přeškrtnutý text 
 *
 * @author Jan Pokorný
 */
class KT_String_Markdown extends KT_String_Base {

    /**
     *
     * @var string Původní hodnota 
     */
    private $rawValue;

    /**
     *
     * @var string Hodnota po aplikaci Markdown 
     */
    private $markedValue;

    /**
     *
     * @var string Hodnota zbavena markdown syntaxe
     */
    private $unMarkedValue;

    /**
     * 
     * @param string $value
     */
    public function __construct($value) {
        $this->rawValue = $value;
        parent::__construct($this->getMarkedValue($value));
    }

    /**
     * Vrátí původní hodnotu
     * 
     * @return string
     */
    public function getRawValue() {
        return $this->rawValue;
    }

    /**
     * Vrátí hodnotu po aplikaci Markdown
     * 
     * @return string
     */
    private function getMarkedValue() {
        if (!$this->markedValue) {
            $this->markedValue = self::doMarkdownEmphasis($this->getRawValue());
        }
        return $this->markedValue;
    }

    /**
     * Vrátí hodnotu bez Markdown syntaxe
     * 
     * @return string
     */
    private function getUnMarkedValue() {
        if (!$this->unMarkedValue) {
            $this->unMarkedValue = self::undoMarkdownEmphasis($this->getRawValue());
        }
        return $this->unMarkedValue;
    }

    /**
     * Vratí hodnotu po aplikaci a se sanitizací
     * 
     * @return string
     */
    public function getValue() {
        if (!$this->value) {
            $this->value = self::doMarkdownEmphasis(htmlspecialchars($this->getRawValue()));
        }
        return $this->value;
    }

    /**
     * Vratí hodnotu bez markdownu pro attribute
     * 
     * @return string
     */
    public function getAttrValue() {
        if (!$this->attrValue) {
            $this->attrValue = esc_attr($this->getUnMarkedValue());
        }
        return $this->attrValue;
    }

    /**
     * Provede zvýraznení v text. Syntaxe převzdata z Markdown.
     * *text* -> kurzíva, **text** -> tučný text, ~~text~~ -> přeškrtnutý text 
     * 
     * @author Jan Pokorný
     * @param string $text Vstupní text
     * @return string Zvýrazněný výstupní text
     */
    public static function doMarkdownEmphasis($text) {
        $patterns = [
            "/\*\*(.+?)\*\*/i",
            "/\*(.+?)\*/i",
            "/\~\~(.+?)\~\~/i",
        ];
        $replaces = [
            "<b>$1</b>",
            "<i>$1</i>",
            "<del>$1</del>"
        ];
        return preg_replace($patterns, $replaces, $text);
    }

    /**
     * Reverzní funkce k doMarkdownEmphasis
     * 
     * @param string $text
     * @return string
     */
    public static function undoMarkdownEmphasis($text) {
        $patterns = [
            "/\*\*(.+?)\*\*/i",
            "/\*(.+?)\*/i",
            "/\~\~(.+?)\~\~/i",
        ];
        $replaces = [
            "$1",
            "$1",
            "$1"
        ];
        return preg_replace($patterns, $replaces, $text);
    }

}
