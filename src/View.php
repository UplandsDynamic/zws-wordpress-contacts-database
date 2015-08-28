<?php

namespace ZwsContactsDatabase;

/**
 * View file for ZWS  Contacts Database
 *
 * @copyright Copyright (c) 2015, Zaziork Web Solutions
 * @author    Zaziork Web Solutions
 * @license This plugin uses the Composer library - see composer-license.txt
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.zaziork.com/zws-contacts-database-plugin/
 */
Class View {

    const MAPS_API_BASE_URL = 'https://maps.googleapis.com/maps/api/geocode/json';
    const OPTIONS_LABEL = 'zws_contacts_database_options';

    public static function submission_form($atts = NULL) {

        /*
          // Handle attribues in shortcode tags - translate to variables for use in code.
          // let's sanitize and filter the incoming attributes before we play with them
          if (!empty($atts)) {
          $sanitized_atts = self::filter_shortcodes($atts);
          }

          $a = shortcode_atts(array(
          'url' => get_site_option(self::$plugin_options_name)['zws_api_consumer_api_base_url'],
          'path' => get_site_option(self::$plugin_options_name)['zws_api_consumer_api_path'],
          'protocol' => get_site_option(self::$plugin_options_name)['zws_api_consumer_proto'],
          ), $sanitized_atts);
          $url = $a['url'];
          $path = $a['path'];
          $protocol = $a['protocol'];

         */

// turn on output buffer
        \ob_start();
//  display form or submit if a postback
        self::display_or_action();
        return \ob_get_clean();
    }

    private static function create_form() {
        $privacy_policy_url = get_site_option(self::OPTIONS_LABEL)['zws_contacts_database_plugin_privacy_policy_url'];
        $privacy_blurb = '<small class="form-privacy-checkbox">Please check the box to indicate that you have read and agree to our <a href="' . $privacy_policy_url .
                '" target="_blank">data protection policy</a>&nbsp;</small>';

        // load up the scripts for the 
        wp_enqueue_script('jquery-timepicker', plugins_url('/../vendor/jquery-timepicker/jquery.timepicker.min.js', __FILE__), array('jquery'));
        wp_enqueue_style('jquery-timepicker-css', plugins_url('/../vendor/jquery-timepicker/jquery.timepicker.css', __FILE__));
        wp_enqueue_script('jquery-timepicker-init', plugins_url('/../inc/jquery.timepicker.init.js', __FILE__));

// create the input form
        echo '<form action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="post">';
        echo '<h3>Your details</h3><p>';
        echo 'Your first name (required) <br />';
        echo '<input type="text" name="first_name" required="required" placeholder="First name" pattern="[a-zA-Z0-9]+" value="' . ( isset($_POST["first_name"]) ? esc_attr($_POST["first_name"]) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your last name (required) <br />';
        echo '<input type="text" name="last_name" required="required" placeholder="Last name" pattern="[a-zA-Z0-9]+" value="' . ( isset($_POST["last_name"]) ? esc_attr($_POST["last_name"]) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your postcode (required - no spaces - e.g. AB329BR) <br />';
        echo '<input type="text" name="postcode" required="required" placeholder="Postcode" pattern="[a-zA-Z0-9]+" maxlength="7" value="' . ( isset($_POST["postcode"]) ? esc_attr($_POST["postcode"]) : '' ) . '" size="8" />';
        echo '</p>';
        echo '<p>';
        echo 'Your phone number (required) <br />';
        echo '<input type="text" name="phone" required="required" placeholder="Phone" pattern="[0-9]+" value="' . ( isset($_POST["phone"]) ? esc_attr($_POST["phone"]) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your Email (required) <br />';
        echo '<input type="email" name="email" required="required" placeholder="Email" value="' . ( isset($_POST["email"]) ? esc_attr($_POST["email"]) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<h3>How far can you cover?</h3><p>';
        echo 'Distance from your location you\'d cover (full miles, required)<br />';
        echo '<input type="text" name="max_radius" required="required" placeholder="Distance" pattern="[0-9]+" value="' . ( isset($_POST["max_radius"]) ? esc_attr($_POST["max_radius"]) : '' ) . '" size="9" />';
        echo '</p>';
        echo '<h3>When are you available?</h3><p style="display:inline-block;margin-bottom:1em;font-size:0.7em;">'
        . 'Times are in 24 hour clock format (e.g. 00:00 = midnight; 02:30 = 2.30am, 14:30 = 2.30pm).<br>'
        . '"Unavailable" indicates you are unavailable for the <strong>entire day</strong>.<br>'
        . 'By default, the options below are set to <strong>"Unavailable"</strong> every day. Please adjust as required!<br>'
        . 'Feel free to provide more detail in the "Extra information" section if necessary.</p>';
        foreach (unserialize(DAYS) as $value => $day) {
            echo '<p>';
            echo 'Times available on ' . ucfirst($day) . '<br>';
            echo '<span class="zws-contacts-database-split-input-class" style="display:inline-block;width:35%;margin-right:1em;">';
            echo 'Earliest available<br>';
            echo '<input id="zws-contacts-database-earlist-time-' . $day . '" required="required" type="text" name="earliest_time_' . $day . '" placeholder="Earlist time" value="' . ( isset($_POST["earliest_time_' . $day . '"]) ? esc_attr($_POST["earlist_time_' . $day . '"]) : '' ) . '" size="8" />';
            echo '</span><span class="zws-contacts-database-split-input-class" style="display:inline-block;width:35%;margin-right:1em;">';
            echo 'Latest available<br>';
            echo '<input id="zws-contacts-database-latest-time-' . $day . '" required="required" type="text" name="latest_time_' . $day . '" placeholder="Latest time" value="' . ( isset($_POST["latest_time_' . $day . '"]) ? esc_attr($_POST["latest_time_' . $day . '"]) : '' ) . '"/>';
            echo '</span></p>';
        }
        echo '<h3>Anything else you\'d like to mention?</h3><p>';
        echo 'Any extra information <br />';
        echo '<textarea rows="10" cols="35" name="extra_info" placeholder="Extra information">' . ( isset($_POST["extra_info"]) ? esc_attr($_POST["extra_info"]) : '' ) . '</textarea>';
        echo '</p>';
        echo '<h3>Complete registration</h3><p>';
        echo $privacy_blurb . '<input type="checkbox" name="privacy_accept" value="accept">';
        echo '</p>';
        wp_nonce_field('submit_details_action', 'my_nonce_field');
        echo '<p><input type="submit" name="submitted" value="Submit"/></p>';
        echo '</form>';
    }

    private static function display_or_action() {
        $safe_values = array();
// checks if incoming POST, and that nonce was set, and that nonce details match
        if (isset($_POST['submitted']) &&
                isset($_POST['my_nonce_field']) &&
                wp_verify_nonce($_POST['my_nonce_field'], 'submit_details_action')) {
// sanitise values
            $safe_values['first_name'] = sanitize_text_field($_POST['first_name']);
            $safe_values['last_name'] = sanitize_text_field($_POST['last_name']);
            $safe_values['postcode'] = strtoupper(trim(sanitize_text_field($_POST['postcode']), ' '));
            $safe_values['phone'] = trim(self::removeNonNumeric(sanitize_text_field($_POST['phone'])));
            $safe_values['email'] = sanitize_email($_POST['email']);
            $safe_values['max_radius'] = sanitize_text_field($_POST['max_radius']);
            $safe_values['extra_info'] = apply_filters('zws_filter_text_with_linebreak', $_POST['extra_info']);
            $safe_values['pp_accepted'] = true ? isset($_POST['privacy_accept']) : false;
            foreach (unserialize(DAYS)as $key => $day) {
                if (sanitize_text_field($_POST['earliest_time_' . $day]) !== 'Unavailable') {
                    $safe_values['earliest_time_' . $day] = sanitize_text_field($_POST['earliest_time_' . $day]);
                } else {
                    $safe_values['earliest_time_' . $day] = 'UNAVL';
                }
                $safe_values['latest_time_' . $day] = sanitize_text_field($_POST['latest_time_' . $day]);
            }

// verify privacy policy has been accepted
            if (!$safe_values['pp_accepted']) {
                return self::failure_view('privacy');
            }
// query google maps api to get longitute and latitude for the postcode, to pull back from db when displayed on map
            require_once(__DIR__ . '/QueryAPI.php');
            $path = '?address=' . $safe_values['postcode'] . '&language=en-EN&sensor=false&key=AIzaSyBWWGk2H9NwjOcF07YRXDXBimae3x7Dk9Y';
            $data = \ZwsContactsDatabase\QueryAPI::makeQuery(self::MAPS_API_BASE_URL, $path);
            if ($data['returned_data'] && $data['returned_data']['status'] === 'OK') {
                if ($data['cached']) {
// error_log('THE DATA WAS CACHED ...'); // debug
                }
                $safe_values['lat'] = sanitize_text_field($data['returned_data']['results'][0]['geometry']['location']['lat']);
                $safe_values['lng'] = sanitize_text_field($data['returned_data']['results'][0]['geometry']['location']['lng']);
            } else {
                return self::failure_view();
            }


// send to database
            require_once(__DIR__ . '/Database.php');
            if (\ZwsContactsDatabase\Database::insert($safe_values)) {
                return self::success_view();
            } else {
                return self::failure_view();
            }
        } else {
            return self::create_form();
        }
    }

    private static function success_view() {
        $success_message = '<div class="zws-contacts-db-success-message"><p>Thank you for submitting your details!</p>'
                . '<p>Your name, postcode, contact details, and any addtional information you submitted, have been successfully stored in our database.</p>'
                . '<p>If you would like us to remove your details at any point, just let as know.</p></div>';
        echo $success_message;
    }

    private static function failure_view($reason = null) {
        switch ($reason) {
            case 'privacy':
                $message = '<div class="zws-contacts-db-failure-message"><p>You did not accept our privacy policy, therefore your details have not been submitted to our database.</p></div>'
                        . ' <button onclick="goBack()">Try Again?</button><script>function goBack() { window.history.back();}</script>';
                break;
            default:
                $message = '<div class="zws-contacts-db-failure-message"><p>Unfortunately, an error occurred and your details have not been submitted.</p>'
                        . '<p>Did you already register using the same phone or email? Or, maybe you mistyped your postcode?</p>'
                        . '<p>Please try again, but if you receive this message once more just contact us and we\'ll add your details manually.</p></div>';
                break;
        }
        echo $message;
    }

    // helper
    private static function removeNonNumeric($input) {
        return preg_replace('/\D/', '', $input);
    }

}
