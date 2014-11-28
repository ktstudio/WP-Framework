<?php

/**
 * Jednoduché zobrazení základních kontaktních informací v "boxu"
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Quick_Contact_Widget extends KT_Widget_Base {

    public function __construct() {
        parent::__construct(
                "KT_Quick_Contact_Widget", __("Rychlý kontakt", KT_DOMAIN), __("Rychlé kontaktní informace včetně nadpisu s informační ikonou.", KT_DOMAIN)
        );
    }

    public function widget($args, $instance) {
        extract($args);
        //echo $before_widget . "\r\n";
        echo "\t\t\t\t" . '<div class="panel panel-info">' . "\r\n";
        echo "\t\t\t\t" . '<div class="panel-heading">' . "\r\n";
        echo "\t\t\t\t\t" . '<h4 class="panel-title"><span class="glyphicon glyphicon-phone-alt"></span> ' . mb_strtoupper($this->name) . '</h4>' . "\r\n";
        echo "\t\t\t\t" . '</div>' . "\r\n";
        echo "\t\t\t\t\t" . '<ul class="list-group">' . "\r\n";
        $phone = trim($instance['phone']);
        if (isset($phone) && !empty($phone)) {
            echo "\t\t\t\t\t\t" . '<li class="list-group-item">' . __("Tel.", KT_DOMAIN) . ': ' . $phone . '</li>' . "\r\n";
        }
        $email = trim($instance['email']);
        if (isset($email) && is_email($email)) {
            echo "\t\t\t\t\t\t" . '<li class="list-group-item">' . kt_hide_email($email) . '</li>' . "\r\n";
        }
        $skype = trim($instance['skype']);
        if (isset($skype) && !empty($skype)) {
            echo "\t\t\t\t\t\t" . '<li class="list-group-item">' . kt_get_skype_link($skype) . '</li>' . "\r\n";
        }
        echo "\t\t\t\t\t" . '</ul>' . "\r\n";
        echo "\t\t\t\t" . '</div>' . "\r\n";
        unset($phone);
        unset($email);
        unset($skype);
        //echo "\t\t\t" . $after_widget;
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance["phone"] = strip_tags($new_instance["phone"]);
        $instance["email"] = strip_tags($new_instance["email"]);
        $instance["skype"] = strip_tags($new_instance["skype"]);
        return $instance;
    }

    public function form($instance) {
        ?>
        <p>
            <label for="<?php echo $this->get_field_id("phone"); ?>"><?php _e("Tel.", KT_DOMAIN); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id("phone"); ?>" name="<?php echo $this->get_field_name("phone"); ?>" value="<?php echo $instance["phone"]; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id("email"); ?>"><?php _e("E-mail", KT_DOMAIN); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id("email"); ?>" name="<?php echo $this->get_field_name("email"); ?>" value="<?php echo $instance["email"]; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id("skype"); ?>"><?php _e("Skype", KT_DOMAIN); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id("skype"); ?>" name="<?php echo $this->get_field_name("skype"); ?>" value="<?php echo $instance["skype"]; ?>">
        </p>
        <?php
    }

}
