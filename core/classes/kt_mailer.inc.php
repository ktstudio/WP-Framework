<?php

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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
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
        if (kt_not_isset_or_empty($this->header)) {
            $this->setupHeader();
        }

        return $this->header;
    }

    /**
     * @return array
     */
    private function getAttachments() {
        return $this->attachments;
    }

    // --- settery -------------------------

    /**
     * Nastaví emailu příslušný content replacer, pro nahrazení hash tagů za reálné data
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
     * 
     * @param string $subject
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setSubject($subject) {
        if (kt_isset_and_not_empty($subject)) {
            $this->subject = strip_tags(htmlspecialchars($subject));
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception('subject');
    }

    /**
     * Nastaví příjemce emailu - nepřidá, pouze setne
     * Provede validaci emailové adresy
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
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

        throw new InvalidArgumentException(__("Příjmence \"$recipientEmail\" není platnný e-mail!", KT_DOMAIN));
    }

    /**
     * Nastaví jako odesílatele emailu
     * Provede validaci emailové adresy
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
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
        throw new InvalidArgumentException(__("Odesílatel \"$senderEmail\" není platnný e-mail!", KT_DOMAIN));
    }

    /**
     * Nastaveí jméno odesílatele
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz  
     * 
     * @param type $sender_name
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setSenderName($sender_name) {
        if (kt_isset_and_not_empty($sender_name)) {
            $this->senderName = $sender_name;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception('sender_name');
    }

    /**
     * Nastaví obsah emailu - HTML type
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
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
        throw new InvalidArgumentException(__("Příjmence \"$recipientEmail\" není platnný e-mail!", KT_DOMAIN));
    }

    /**
     * Vyprázdní aktuální obsah emailu (content)
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
     * 
     * @param string $html
     * @return \KT_Mailer
     * @throws KT_Not_Set_Argument_Exception
     */
    public function addContent($html) {
        if (kt_isset_and_not_empty($html)) {
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
     * 
     * @param array $attachments
     * @return \KT_Mailer
     */
    public function addAtachments(array $attachments) {
        if (kt_not_isset_or_empty($attachments)) {
            return $this;
        }

        $currentAttachmentCollection = $this->getAttachments();

        if (kt_isset_and_not_empty($currentAttachmentCollection)) {
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
     *
     * @return boolean
     */
    public function send() {
        $this->validate();

        $content = $this->getContent();
        if (kt_isset_and_not_empty($content) && KT_Content_Replacer::check($content)) { // pokud je zadaný obsah a exitují v něm tagy
            $contentReplacer = $this->getContentReplacer();
            if (kt_isset_and_not_empty($contentReplacer)) { // a je zadán nahrazovač obsahu
                $content = $contentReplacer->update($content); // tak nahradit tagy
            }
        }
        $email = wp_mail($this->getRecipients(), $this->getSubject(), $content, $this->getHeader(), $this->getAttachments());
        if ($email) {
            return true;
        }
        return false;
    }

    // --- privátní funkce ----------------

    /**
     * Vytvoří html string hlavičku emailu, nezbytnou pro odeslání emailu dle nastavení maileru
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz  
     *
     * @return \KT_Mailer
     */
    private function setupHeader() {
        $header .= "Content-Type: text/html; charset=UTF-8\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Transfer-Encoding: 8bit\r\n";
        $header .= "From: " . self::getHeaderEmail($this->getSenderEmail(), $this->getSenderName()) . "\r\n";
        $header .= "Reply-To: {$this->getSenderEmail()}\r\n";
        $header .= "Return-Path: {$this->getSenderEmail()}\r\n";

        $this->setHeader($header);

        return $this;
    }

    /**
     * Zkontroluje, zda jsou všechny hodnoty nastavené.
     * Musí být nastaveno - recipients, content, sender_email, sender_name
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz 
     *
     * @throws KT_Not_Set_Argument_Exception
     */
    private function validate() {
        $mustBeSetup = array('recipients', 'content', 'senderName', 'senderName');

        foreach ($mustBeSetup as $value) {
            if (kt_not_isset_or_empty($this->$value)) {
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
        if (kt_isset_and_not_empty($value) && is_email($value)) {
            return true;
        }
        return false;
    }

    /**
     * Podle zadaných parametrů vrátí e-mail ve správném formátu pro hlavičku
     * 
     * @author Martin Hlaváč <hlavac@ktstudio.cz>
     * @link http://www.ktstudio.cz
     * 
     * @param string $email
     * @param string $name
     * @return type
     */
    public static function getHeaderEmail($email, $name = null) {
        $result = null;

        if (kt_isset_and_not_empty($email)) {
            if (kt_isset_and_not_empty($name)) {
                $result .= "$name ";
            }
            $result .= "<$email>";
        }

        return $result;
    }

    /**
     * Skrytí e-mailové adresy pomocí javascriptu
     * 
     * @author Maurits van der Schee
     * @link http://www.maurits.vdschee.nl/php_hide_email/
     * 
     * @param string $email
     * @return html/text
     */
    public static function hideEmailString($email) {
        $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';

        $key = str_shuffle($character_set);
        $cipher_text = '';
        $id = 'e' . rand(1, 999999999);

        for ($i = 0; $i < strlen($email); $i+=1) {
            $cipher_text.= $key[strpos($character_set, $email[$i])];
        }

        $script = 'var a="' . $key . '";var b=a.split("").sort().join("");var c="' . $cipher_text . '";var d="";';
        $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
        $script.= 'document.getElementById("' . $id . '").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';
        $script = "eval(\"" . str_replace(array("\\", '"'), array("\\\\", '\"'), $script) . "\")";
        $script = '<script type="text/javascript">/*<![CDATA[*/' . $script . '/*]]>*/</script>';

        return '<span id="' . $id . '">' . __("E-mail", KT_DOMAIN) . '</span>' . $script;
    }

}
