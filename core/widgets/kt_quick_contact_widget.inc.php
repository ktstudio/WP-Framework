<?php

/**
 * Jednoduché zobrazení základních kontaktních informací v "boxu" (vč. bootstrap)
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Quick_Contact_Widget extends KT_Widget_Base {

    const PHONE_KEY = "kt-quick-contact-phone";
    const EMAIL_KEY = "kt-quick-contact-email";
    const SKYPE_KEY = "kt-quick-contact-skype";

    public function __construct() {
        parent::__construct(
                "KT_Quick_Contact_Widget", __("Rychlý kontakt", KT_DOMAIN), __("Rychlé kontaktní informace včetně nadpisu s informační ikonou.", KT_DOMAIN)
        );
    }

    public function widget($args, $instance) {
        extract($args);

        kt_the_tabs_indent(0, "<div class=\"panel panel-info\>", true);
        kt_the_tabs_indent(1, "<div class=\"panel-heading\">", true);
        $name = mb_strtoupper($this->name);
        kt_the_tabs_indent(2, "<h4 class=\"panel-title\"><span class=\"glyphicon glyphicon-phone-alt\"></span> $name</h4>", true);
        kt_the_tabs_indent(1, "</div>", true);
        kt_the_tabs_indent(1, "<ul class=\"list-group\">", true);
        $phone = htmlspecialchars(trim($instance[self::PHONE_KEY]));
        if (isset($phone) && !empty($phone)) {
            $phoneTitle = __("Tel.", KT_DOMAIN);
            kt_the_tabs_indent(2, "<li class=\"list-group-item\">$phoneTitle: $phone</li>", true);
        }
        $email = htmlspecialchars(trim($instance[self::EMAIL_KEY]));
        if (isset($email) && is_email($email)) {
            $emailValue = self::hideEmail($email);
            kt_the_tabs_indent(2, "<li class=\"list-group-item\">$emailValue</li>", true);
        }
        $skype = htmlspecialchars(trim($instance[self::SKYPE_KEY]));
        if (isset($skype) && !empty($skype)) {
            $skypeTitle = __("Zavolat na Skype");
            $skypeLink = "<a href=\"skype:echo123?call\" title=\"$skypeTitle\">$skypeTitle</a>";
            kt_the_tabs_indent(2, "<li class=\"list-group-item\">$skypeLink</li>", true);
        }
        kt_the_tabs_indent(1, "</ul>", true);
        kt_the_tabs_indent(0, "</div>", true);
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance[self::PHONE_KEY] = strip_tags($new_instance[self::PHONE_KEY]);
        $instance[self::EMAIL_KEY] = strip_tags($new_instance[self::EMAIL_KEY]);
        $instance[self::SKYPE_KEY] = strip_tags($new_instance[self::SKYPE_KEY]);
        return $instance;
    }

    public function form($instance) {
        ?>
        <p>
            <label for="<?php echo $this->get_field_id(self::PHONE_KEY); ?>"><?php _e("Tel.", KT_DOMAIN); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id(self::PHONE_KEY); ?>" name="<?php echo $this->get_field_name(self::PHONE_KEY); ?>" value="<?php echo $instance[self::PHONE_KEY]; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id(self::EMAIL_KEY); ?>"><?php _e("E-mail", KT_DOMAIN); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id(self::EMAIL_KEY); ?>" name="<?php echo $this->get_field_name(self::EMAIL_KEY); ?>" value="<?php echo $instance[self::EMAIL_KEY]; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id(self::SKYPE_KEY); ?>"><?php _e("Skype", KT_DOMAIN); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id(self::SKYPE_KEY); ?>" name="<?php echo $this->get_field_name(self::SKYPE_KEY); ?>" value="<?php echo $instance[self::SKYPE_KEY]; ?>">
        </p>
        <?php
    }

    /**
     * Skrytí e-mailové adresy pro HTML (roboty)
     * 
     * @author Maurits van der Schee <maurits@vdschee.nl>
     * @link http://www.maurits.vdschee.nl/php_hide_email/ PHP hide_email()
     * 
     * @param string $email
     * @return string (HTML/JS)
     */
    public static function hideEmail($email) {
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
        return '<span id="' . $id . '">[javascript protected email address]</span>' . $script;
    }

}
