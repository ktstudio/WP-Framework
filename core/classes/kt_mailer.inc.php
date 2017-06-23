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
    private $replyToEmail;
    private $content = null;
    private $recipients = null;
    private $carbonCopies = null;
    private $blindCarbonCopies = null;
    private $subject = null;
    private $header = null;
    private $contentReplacer = null;
    private $isWpMail = false;
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
        $this->setRecipient($recipientEmail, $recipientName)->setSubject($subject);
    }

    // --- gettery -------------------------

    /** @return \KT_Content_Replacer */
    protected function getContentReplacer() {
        return $this->contentReplacer;
    }

    /** @return string */
    public function getSenderEmail() {
        if (!$this->senderEmail) {
            $siteUrl = get_option("siteurl", $_SERVER['SERVER_NAME']);
            $regex = "/^(?:https?\\:\\/\\/)?(?:www\\.)?([^\\/|?|#]+).*$/i";
            $matches = [];
            if (preg_match($regex, $siteUrl, $matches)) {
                $this->senderEmail = "no-reply@{$matches[1]}";
            }
        }
        return $this->senderEmail;
    }

    /** @return string */
    public function getReplyToEmail() {
        return $this->replyToEmail;
    }

    /** @return string */
    public function getSenderName() {
        return $this->senderName;
    }

    /** @return string */
    public function getContent() {
        return $this->content;
    }

    /** @return string */
    public function getRecipients() {
        return $this->recipients;
    }

    /** @return string */
    public function getCarbonCopies() {
        return $this->carbonCopies;
    }

    /** @return string */
    public function getBlindCarbonCopies() {
        return $this->blindCarbonCopies;
    }

    /** @return string */
    public function getSubject() {
        return $this->subject;
    }

    /** @return string */
    public function getHeader() {
        if (KT::notIssetOrEmpty($this->header)) {
            $this->setupHeader();
        }

        return $this->header;
    }

    /** @return boolean */
    public function getIsWpMail() {
        return $this->isWpMail;
    }

    /**
     * @deprecated since version 1.6
     * @return array
     */
    public function getAttachments() {
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
     * Nastaví předmět odesílaného emailu
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
     * Nastaví jednoho příjemce emailu - nepřidá, pouze setne + provede validaci emailové adresy
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $recipientEmail
     * @param string $recipientName
     * @return \KT_Mailer
     * @throws InvalidArgumentException
     */
    public function setRecipient($recipientEmail, $recipientName = null) {
        if (self::isEmail($recipientEmail)) {
            $this->recipients = self::getHeaderEmail($recipientEmail, $recipientName);
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Reciver \"%s\" is not valid e-mail address!", "KT_CORE_DOMAIN"), $recipientEmail));
    }

    /**
     * @deprecated since version 1.8
     * @see setRecipient
     * @see addRecipient
     */
    public function setRecipients($recipientEmail, $recipientName = null) {
        $this->setRecipient($recipientEmail, $recipientName);
    }

    /**
     * Nastaví jednu kopii emailu - nepřidá, pouze setne + provede validaci emailové adresy
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $copyEmail
     * @param string $copyName
     * @return \KT_Mailer
     * @throws InvalidArgumentException
     */
    public function setCarbonCopy($copyEmail, $copyName = null) {
        if (self::isEmail($copyEmail)) {
            $this->carbonCopies = self::getHeaderEmail($copyEmail, $copyName);
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Copy \"%s\" is not valid email address!", "KT_CORE_DOMAIN"), $recipientEmail));
    }

    /**
     * Nastaví jednu skrytou kopii emailu - nepřidá, pouze setne + provede validaci emailové adresy
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $copyEmail
     * @param string $copyName
     * @return \KT_Mailer
     * @throws InvalidArgumentException
     */
    public function setBlindCarbonCopy($copyEmail, $copyName = null) {
        if (self::isEmail($copyEmail)) {
            $this->blindCarbonCopies = self::getHeaderEmail($copyEmail, $copyName);
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Hidden copy \"%s\" is not valid email address!", "KT_CORE_DOMAIN"), $recipientEmail));
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
        throw new InvalidArgumentException(sprintf(__("Sender \"%s\" is not valid email address!", "KT_CORE_DOMAIN"), $senderEmail));
    }

    /**
     * Nastaví reply to email
     * 
     * @author Jan Pokorný
     * @param string $email
     * @return \KT_Mailer
     * @throws InvalidArgumentException
     */
    public function setReplyToEmail($email) {
        if (self::isEmail($email)) {
            $this->replyToEmail = $email;
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Reply to \"%s\" is not valid email address!", "KT_CORE_DOMAIN"), $email));
    }

    /**
     * Nastaví jméno odesílatele
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
     * Nastaví zda se pro odesílání použije funkce wp_mail() z WP (true), či mail() přímo z PHP (false)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstduio.cz
     *
     * @param boolean $isWpMail
     * @return \KT_Mailer
     */
    public function setIsWpMail($isWpMail) {
        $this->isWpMail = KT::tryGetBool($isWpMail);
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
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function addRecipient($recipientEmail, $recipientName = null) {
        if (self::isEmail($recipientEmail)) {
            $recipients = $this->getRecipients();
            $recipients .= "; " . self::getHeaderEmail($recipientEmail, $recipientName);
            $this->recipients = $recipients;
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Reciver \"%s\" is not valid email address!", "KT_CORE_DOMAIN"), $recipientEmail));
    }

    /**
     * Přidá další kopii - nepřepíše původní!
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function addCarbonCopy($copyEmail, $copyName = null) {
        if (self::isEmail($copyEmail)) {
            $copies = $this->getCarbonCopies();
            $copies .= "; " . self::getHeaderEmail($copyEmail, $copyName);
            $this->carbonCopies = $copies;
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Copy \"%s\" is not valid email address!", "KT_CORE_DOMAIN"), $copyEmail));
    }

    /**
     * Přidá další skrytou kopii - nepřepíše původní!
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function addBlindCarbonCopy($copyEmail, $copyName = null) {
        if (self::isEmail($copyEmail)) {
            $copies = $this->getBlindCarbonCopies();
            $copies .= "; " . self::getHeaderEmail($copyEmail, $copyName);
            $this->blindCarbonCopies = $copies;
            return $this;
        }
        throw new InvalidArgumentException(sprintf(__("Hidden copy \"%s\" is not valid email address!", "KT_CORE_DOMAIN"), $copyEmail));
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
        if ($this->getIsWpMail()) {
            $email = wp_mail($this->getRecipients(), self::getMimeHeaderEncode($this->getSubject()), $content, $this->getHeader());
        } else {
            $email = mail($this->getRecipients(), self::getMimeHeaderEncode($this->getSubject()), $content, $this->getHeader());
        }
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
        if (KT::issetAndNotEmpty($this->getReplyToEmail())) {
            $header .= "Reply-To: {$this->getReplyToEmail()}" . PHP_EOL;
            $header .= "Return-Path: {$this->getReplyToEmail()}" . PHP_EOL;
        }
        $carbonCopies = $this->getCarbonCopies();
        if (KT::issetAndNotEmpty($carbonCopies)) {
            $header .= "Cc: $carbonCopies" . PHP_EOL;
        }
        $blindCarbonCopies = $this->getBlindCarbonCopies();
        if (KT::issetAndNotEmpty($blindCarbonCopies)) {
            $header .= "Bcc: $blindCarbonCopies" . PHP_EOL;
        }
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
