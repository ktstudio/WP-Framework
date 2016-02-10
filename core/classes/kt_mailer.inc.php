<?php

/**
 * Založí nový objekt pro odeslání emailu
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_Mailer {

    private $senderEmail = null;
    private $senderName = null;
    private $content = null;
    private $recipients = null;
    private $subject = null;
    private $header = null;
    private $contentReplacer = null;
    private $attachments = array();

    /**
     * Založí nový objekt pro odeslání emailu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $recipient - platný email
     * @param string $subject - předmět emailu
     * @throws KT_Not_Set_Argument_Exception
     * @throws invalidArgumentException
     */
    public function __construct($recipientEmail, $recipientName = null, $subject = null) {
        $this->setRecipients($recipientEmail, $recipientName)->setSubject($subject);
    }

    // --- gettery -------------------------

    /**
     * @return \KT_Content_Replacer
     */
    protected function getContentReplacer() {
        return $this->contentReplacer;
    }

    /**
     * @return string
     */
    private function getSenderEmail() {
        return $this->senderEmail;
    }

    /**
     * @return string
     */
    private function getSenderName() {
        return $this->senderName;
    }

    /**
     * @return string
     */
    private function getContent() {
        return $this->content;
    }

    /**
     * @return string
     */
    private function getRecipients() {
        return $this->recipients;
    }

    /**
     * @return string
     */
    private function getSubject() {
        return $this->subject;
    }

    /**
     * @return string
     */
    private function getHeader() {
        if (KT::notIssetOrEmpty($this->header)) {
            $this->setupHeader();
        }

        return $this->header;
    }

    /**
     * @deprecated since version 1.6
     * @return array
     */
    private function getAttachments() {
        return $this->attachments;
    }

    // --- settery -------------------------

    /**
     * Nastaví emailu příslušný content replacer, pro nahrazení hash tagů za reálné data
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param KT_Content_Replacer $contentReplacer
     * @return \KT_Mailer
     */
    public function setContentReplacer(KT_Content_Replacer $contentReplacer) {
        $this->contentReplacer = $contentReplacer;
        return $this;
    }

    /**
     * Nastaveí předmět odesílaného emailu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $subject
     * @return \KT_Mailer
     */
    public function setSubject($subject) {
        if (KT::issetAndNotEmpty($subject)) {
            $this->subject = strip_tags(htmlspecialchars($subject));
        }
        return $this;
    }

    /**
     * Nastaví příjemce emailu - nepřidá, pouze setne
     * Provede validaci emailové adresy
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $recipientEmail
     * @param string $recipientName
     * @return \KT_Mailer
     * @throws InvalidArgumentException
     */
    public function setRecipients($recipientEmail, $recipientName = null) {
        if (self::isEmail($recipientEmail)) {
            $this->recipients = self::getHeaderEmail($recipientEmail, $recipientName);
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Příjemce \"%s\" není platnný e-mail!", "KT_CORE_DOMAIN"), $recipientEmail));
    }

    /**
     * Nastaví jako odesílatele emailu
     * Provede validaci emailové adresy
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param type $senderEmail
     * @return \KT_Mailer
     * @throws InvalidArgumentException
     */
    public function setSenderEmail($senderEmail) {
        if (self::isEmail($senderEmail)) {
            $this->senderEmail = $senderEmail;
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Odesílatel \"%s\" není platnný e-mail!", "KT_CORE_DOMAIN"), $senderEmail));
    }

    /**
     * Nastaveí jméno odesílatele
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     * @param type $senderName
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setSenderName($senderName) {
        if (KT::issetAndNotEmpty($senderName)) {
            $this->senderName = $senderName;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception('senderName');
    }

    /**
     * Nastaví odesílatele: email + jméno najednou
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param string $senderEmail
     * @param string $senderName
     * @return \KT_ITH_Mailer
     * @throws KT_ITH_Not_Set_Argument_Exception
     */
    public function setSender($senderEmail, $senderName) {
        $this->setSenderEmail($senderEmail);
        $this->setSenderName($senderName);
        return $this;
    }

    /**
     * Nastaví obsah emailu - HTML type
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $content
     * @return \KT_Mailer
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * Nastaveí hlavičku emailu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $header
     * @return \KT_Mailer
     */
    public function setHeader($header) {
        $this->header = $header;
        return $this;
    }

    /**
     * Nastaví kolekci všech příloh, které budou odeslány společně s emailem
     * 
     * @deprecated since version 1.6
     * @author Tomáš Kocifaj
     * @link http://www.ktstduio.cz
     * 
     * @param array $attachments
     * @return \KT_Mailer
     */
    public function setAttachments(array $attachments) {
        $this->attachments = $attachments;
        return $this;
    }

    // --- veřejné funkce ------------------

    /**
     * Přidá dalšího recipienta - nepřepíše původního!
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param type $senderEmail
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function addRecipient($recipientEmail, $recipientName = null) {
        if (self::isEmail($recipientEmail)) {
            $recipients = $this->getRecipients();
            $recipients .= "; " . self::getHeaderEmail($recipientEmail, $recipientName);
            $this->setRecipients($recipients);
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Příjmence \"%s\" není platnný e-mail!", "KT_CORE_DOMAIN"), $recipientEmail));
    }

    /**
     * Vyprázdní aktuální obsah emailu (content)
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return \KT_Mailer
     */
    public function clearContent() {
        $this->setContent("");
        return $this;
    }

    /**
     * Přidá do contentu další část html obsahu a oddělí ho <br /> na konci
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $html
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function addContent($html) {
        if (KT::issetAndNotEmpty($html)) {
            $content = $this->getContent();
            $content .= $html . "<br />";
            $this->setContent($content);
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception('html');
    }

    /**
     * Přidá do kolekci příloh jeden soubor
     * 
     * @deprecated since version 1.6
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $attachment - celá cesta k souboru na serveru
     * @return \KT_Mailer
     */
    public function addAttachment($attachment) {
        if (is_string($attachment)) {
            $currentAttachmentCollection = $this->getAttachments();
            array_push($currentAttachmentCollection, $attachment);
            $this->setAttachments($currentAttachmentCollection);
        }
        return $this;
    }

    /**
     * Přidá do kolekcí příloh další kolekci (merge).
     * 
     * @deprecated since version 1.6
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param array $attachments
     * @return \KT_Mailer
     */
    public function addAtachments(array $attachments) {
        if (KT::notIssetOrEmpty($attachments)) {
            return $this;
        }
        $currentAttachmentCollection = $this->getAttachments();
        if (KT::issetAndNotEmpty($currentAttachmentCollection)) {
            $newAttachmentCollection = array_merge($currentAttachmentCollection, $attachments);
            $this->setAttachments($newAttachmentCollection);
        } else {
            $this->setAttachments($attachments);
        }
        return $this;
    }

    /**
     * Odešle email dle zvolených parametrů. Funguje pouze ve WP
     * Používá se funkce wp_mail()
     * Provádí validaci, zda jsou všechny nutné údaje vyplněné
     * 
     * return true při úspěšném odelsání emailu
     * return false při chybě při odesílání
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     *
     * @return boolean
     */
    public function send() {
        $this->validate();
        $content = $this->getContent();
        if (KT::issetAndNotEmpty($content) && KT_Content_Replacer::check($content)) { // pokud je zadaný obsah a exitují v něm tagy
            $contentReplacer = $this->getContentReplacer();
            if (KT::issetAndNotEmpty($contentReplacer)) { // a je zadán nahrazovač obsahu
                $content = $contentReplacer->update($content); // tak nahradit tagy
            }
        }
        $email = mail($this->getRecipients(), self::getMimeHeaderEncode($this->getSubject()), $content, $this->getHeader());
        if ($email) {
            return true;
        }
        return false;
    }

    /**
     * Zakódování e-mailové hlavičky podle RFC 2047
     * 
     * @copyright Jakub Vrána, http://php.vrana.cz/
     * 
     * @param string text k zakódování
     * @param string kódování, výchozí je utf-8
     * @return string řetězec pro použití v e-mailové hlavičce
     */
    public static function getMimeHeaderEncode($text, $encoding = "utf-8") {
        if (KT::issetAndNotEmpty($text)) {
            if (function_exists("imap_8bit")) {
                $encodedText = imap_8bit($text);
                return "=?$encoding?Q?{$encodedText}?=";
            } else {
                return $text;
            }
        }
        return null;
    }

    // --- privátní funkce ----------------

    /**
     * Vytvoří html string hlavičku emailu, nezbytnou pro odeslání emailu dle nastavení maileru
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     *
     * @return \KT_Mailer
     */
    private function setupHeader() {
        $header = "MIME-Version: 1.0" . PHP_EOL;
        $header .= "Content-Type: text/html; charset=utf-8" . PHP_EOL;
        $header .= "Content-Transfer-Encoding: 8bit" . PHP_EOL;
        $header .= "From: " . self::getHeaderEmail($this->getSenderEmail(), $this->getSenderName()) . "" . PHP_EOL;
        $header .= "Reply-To: {$this->getSenderEmail()}" . PHP_EOL;
        $header .= "Return-Path: {$this->getSenderEmail()}" . PHP_EOL;
        $this->setHeader($header);
        return $this;
    }

    /**
     * Zkontroluje, zda jsou všechny hodnoty nastavené.
     * Musí být nastaveno - recipients, content, sender_email, sender_name
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     *
     * @throws KT_Not_Set_Argument_Exception
     */
    private function validate() {
        $mustBeSetup = array("recipients", "content");
        foreach ($mustBeSetup as $value) {
            if (KT::notIssetOrEmpty($this->$value)) {
                throw new KT_Not_Set_Argument_Exception($value);
            }
        }
    }

    // --- statické funkce ----------------

    /**
     * Vlastní funkce pro validaci emailu
     *
     * @param string $value
     * @return boolean
     */
    public static function isEmail($value) {
        if (KT::issetAndNotEmpty($value) && is_email($value)) {
            return true;
        }
        return false;
    }

    /**
     * Podle zadaných parametrů vrátí e-mail ve správném formátu pro hlavičku
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $email
     * @param string $name
     * @return type
     */
    public static function getHeaderEmail($email, $name = null) {
        $result = null;
        if (KT::issetAndNotEmpty($email)) {
            if (KT::issetAndNotEmpty($name)) {
                $result .= self::getMimeHeaderEncode($name) . " ";
            }
            $result .= "<$email>";
        }
        return $result;
    }

}
