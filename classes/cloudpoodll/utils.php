<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 *
 * @package   qtype_speakautograde
 * @copyright 2018 Justin Hunt {@link http://www.poodll.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_speakautograde\cloudpoodll;

defined('MOODLE_INTERNAL') || die();

/**
 * Utility class for handling interactions with the Poodll cloud service.
 *
 * This class provides various helper functions for:
 * - Fetching available recorder, transcriber, and skin options.
 * - Managing API tokens for authentication with Poodll services.
 * - Sending and receiving data via cURL.
 * - Retrieving transcripts and captions for audio/video recordings.
 */
class utils {

    /**
     * Retrieves the available recorder options.
     *
     * @return array An associative array of recorder types.
     */
    public static function fetch_options_recorders() {
        $rec_options = [
            constants::REC_AUDIO => get_string('recorderaudio', constants::M_COMPONENT),
            constants::REC_VIDEO  => get_string('recordervideo', constants::M_COMPONENT),
        ];
        return $rec_options;
    }

    /**
     * Retrieves the available fallback options for recording.
     *
     * @return array An associative array of fallback options.
     */
    public static function fetch_options_fallback() {
        $options = [
            constants::FALLBACK_UPLOAD => get_string('fallbackupload', constants::M_COMPONENT),
            constants::FALLBACK_IOSUPLOAD  => get_string('fallbackiosupload', constants::M_COMPONENT),
            constants::FALLBACK_WARNING  => get_string('fallbackwarning', constants::M_COMPONENT)
        ];
        return $options;
    }

    /**
     * Retrieves the available transcriber options.
     *
     * @return array An associative array of transcriber options.
     */
    public static function fetch_options_transcribers() {
        $options = [
            constants::TRANSCRIBER_CHROME => get_string('transcriber_chrome', constants::M_COMPONENT),
            constants::TRANSCRIBER_AMAZON_TRANSCRIBE => get_string('transcriber_amazontranscribe', constants::M_COMPONENT),
        ];
        return $options;
    }

    /**
     * Retrieves the available time limit options for recordings.
     *
     * @return array An associative array of time limit options.
     */
    public static function get_timelimit_options() {
        return [
            0 => get_string('notimelimit', constants::M_COMPONENT),
            30 => get_string('xsecs', constants::M_COMPONENT, '30'),
            45 => get_string('xsecs', constants::M_COMPONENT, '45'),
            60 => get_string('onemin', constants::M_COMPONENT),
            90 => get_string('oneminxsecs', constants::M_COMPONENT, '30'),
            120 => get_string('xmins', constants::M_COMPONENT, '2'),
            150 => get_string('xminsecs', constants::M_COMPONENT, array('minutes' => 2, 'seconds' => 30)),
            180 => get_string('xmins', constants::M_COMPONENT, '3')
        ];
    }

    /**
     * Retrieves available skin options for the recorder.
     *
     * @param string $rectype The recorder type (audio or video).
     * @return array An associative array of available skins for the specified recorder type.
     */
    public static function fetch_options_skins($rectype=constants::REC_VIDEO) {
        $rec_options = array();
        switch($rectype) {
            case constants::REC_AUDIO:
                $rec_options = [
                    constants::SKIN_PLAIN => get_string('skinplain', constants::M_COMPONENT),
                    constants::SKIN_BMR => get_string('skinbmr', constants::M_COMPONENT),
                    constants::SKIN_123 => get_string('skin123', constants::M_COMPONENT),
                    constants::SKIN_FRESH => get_string('skinfresh', constants::M_COMPONENT),
                    constants::SKIN_ONCE => get_string('skinonce', constants::M_COMPONENT),
                    constants::SKIN_UPLOAD => get_string('skinupload', constants::M_COMPONENT),
                ];
                break;
            case constants::REC_VIDEO:
            default:
                $rec_options = [
                    constants::SKIN_PLAIN => get_string('skinplain', constants::M_COMPONENT),
                    constants::SKIN_BMR => get_string('skinbmr', constants::M_COMPONENT),
                    constants::SKIN_123 => get_string('skin123', constants::M_COMPONENT),
                    constants::SKIN_ONCE => get_string('skinonce', constants::M_COMPONENT),
                    constants::SKIN_UPLOAD => get_string('skinupload', constants::M_COMPONENT),
                ];
        }
        return $rec_options;
    }

    /**
     * Retrieves available region options for cloud processing.
     *
     * @return array An associative array of region identifiers and their corresponding labels.
     */
    public static function get_region_options() {
        return array(
            constants::REGION_USEAST1 => get_string('useast1', constants::M_COMPONENT),
            constants::REGION_TOKYO => get_string('tokyo', constants::M_COMPONENT),
            constants::REGION_SYDNEY => get_string('sydney', constants::M_COMPONENT),
            constants::REGION_DUBLIN => get_string('dublin', constants::M_COMPONENT),
            constants::REGION_OTTAWA => get_string('ottawa', constants::M_COMPONENT),
            constants::REGION_FRANKFURT => get_string('frankfurt', constants::M_COMPONENT),
            constants::REGION_LONDON => get_string('london', constants::M_COMPONENT),
            constants::REGION_SAOPAULO => get_string('saopaulo', constants::M_COMPONENT)
        );
    }

    /**
     * Retrieves expiration period options for stored recordings.
     *
     * @return array An associative array of expiration days.
     */
    public static function get_expiredays_options() {
        return array(
            '1' => '1',
            '3' => '3',
            '7' => '7',
            '30' => '30',
            '90' => '90',
            '180' => '180',
            '365' => '365',
            '730' => '730',
            '9999' => get_string('forever', constants::M_COMPONENT)
        );
    }

    /**
     * Retrieves available language options for speech recognition.
     *
     * @return array An associative array of language codes and their corresponding labels.
     */
    public static function get_lang_options() {
        return array(
            'en-US' => get_string('en-us', constants::M_COMPONENT),
            'en-UK' => get_string('en-uk', constants::M_COMPONENT),
            'en-AU' => get_string('en-au', constants::M_COMPONENT),
            'fr-CA' => get_string('fr-ca', constants::M_COMPONENT),
            'es-US' => get_string('es-us', constants::M_COMPONENT),
        );
    }

    /**
     * Determines whether transcription is supported for a given instance.
     *
     * @param object $instance The instance containing AWS region settings.
     * @return bool True if transcription is supported, false otherwise.
     */
    public static function can_transcribe($instance) {
        //we default to true
        //but it only takes one no ....
        $ret = true;

        //The regions that can transcribe
        switch($instance->awsregion) {
            case 'useast1':
            case 'dublin':
            case 'sydney':
            case 'ottawa':
                break;
            default:
                $ret = false;
        }
        return $ret;
    }

    /**
     * Fetches transcripts from AWS and Tokens from cloudpoodll from a given URL using cURL.
     *
     * @param string $url The URL to fetch data from.
     * @param mixed $postdata Optional data to send in a POST request.
     * @return mixed The response data from the request.
     */
    public static function curl_fetch($url, $postdata=false) {
        global $CFG;

        require_once($CFG->libdir.'/filelib.php');
        $curl = new \curl();

        $result = $curl->get($url, $postdata);
        return $result;
    }

    /**
     * Retrieves the API token for display purposes.
     *
     * This method does not call the Poodll API directly but instead checks 
     * cached tokens to prevent unnecessary external requests.
     *
     * It is called from the settings page. Note that and we do not want to make calls out
     * to cloud.poodll.com on settings page load, for performance and stability reasons.
     * Therefore, if the cache is empty and/or no token, we just show "refresh token" links
     *
     * @param string $apiuser The API username.
     * @param string $apisecret The API secret key.
     * @return string The token display message or an error message.
     */
    public static function fetch_token_for_display($apiuser, $apisecret) {
        global $CFG;

        //First check that we have an API id and secret
        //refresh token
        $refresh = \html_writer::link($CFG->wwwroot.constants::REFRESH_URL,
                get_string('refreshtoken', constants::M_COMPONENT)).'<br>';


        $message = '';
        $apiuser = trim($apiuser);
        $apisecret = trim($apisecret);
        if (empty($apiuser)) {
            $message .= get_string('noapiuser', constants::M_COMPONENT).'<br>';
        }
        if (empty($apisecret)) {
            $message .= get_string('noapisecret', constants::M_COMPONENT);
        }

        if (!empty($message)) {
            return $refresh.$message;
        }

        //Fetch from cache and process the results and display
        $cache = \cache::make_from_params(\cache_store::MODE_APPLICATION, constants::M_COMPONENT, 'token');
        $tokenobject = $cache->get('recentpoodlltoken');

        //if we have no token object the creds were wrong ... or something
        if (!($tokenobject)) {
            $message = get_string('notokenincache', constants::M_COMPONENT);
            //if we have an object but its no good, creds werer wrong ..or something
        }elseif (!property_exists($tokenobject, 'token') || empty($tokenobject->token)) {
            $message = get_string('credentialsinvalid', constants::M_COMPONENT);
            //if we do not have subs, then we are on a very old token or something is wrong, just get out of here.
        }elseif (!property_exists($tokenobject, 'subs')) {
            $message = 'No subscriptions found at all';
        }
        if (!empty($message)) {
            return $refresh.$message;
        }

        //we have enough info to display a report. Lets go.
        foreach ($tokenobject->subs as $sub) {
            $sub->expiredate = date('d/m/Y', $sub->expiredate);
            $message .= get_string('displaysubs', constants::M_COMPONENT, $sub).'<br>';
        }
        //Is app authorised
        if (in_array(constants::M_COMPONENT, $tokenobject->apps)) {
            $message .= get_string('appauthorised', constants::M_COMPONENT).'<br>';
        }else{
            $message .= get_string('appnotauthorised', constants::M_COMPONENT).'<br>';
        }

        return $refresh.$message;

    }

    /**
     * Retrieves the API token for authenticating with Poodll services.
     *
     * This method checks the cache for a valid token before making an API request.
     * If no valid token exists, a new one is requested from the Poodll cloud service.
     *
     * @param string $apiuser The API username.
     * @param string $apisecret The API secret key.
     * @param bool $force Whether to force fetching a new token.
     * @return string The API token or an empty string if the request fails.
     */
    public static function fetch_token($apiuser, $apisecret, $force = false) {

        $cache = \cache::make_from_params(\cache_store::MODE_APPLICATION, constants::M_COMPONENT, 'token');
        $tokenobject = $cache->get('recentpoodlltoken');
        $tokenuser = $cache->get('recentpoodlluser');
        $apiuser = trim($apiuser);
        $apisecret = trim($apisecret);

        //if we got a token and its less than expiry time
        // use the cached one
        if ($tokenobject && $tokenuser && $tokenuser==$apiuser && !$force) {
            if ($tokenobject->validuntil == 0 || $tokenobject->validuntil > time()) {
                return $tokenobject->token;
            }
        }

        // Send the request & save response to $resp
        $token_url ='https://cloud.poodll.com/local/cpapi/poodlltoken.php';
        $postdata = array(
            'username' => $apiuser,
            'password' => $apisecret,
            'service' => 'cloud_poodll'
        );
        $token_response = self::curl_fetch($token_url, $postdata);
        if ($token_response) {
            $resp_object = json_decode($token_response);
            if ($resp_object && property_exists($resp_object, 'token')) {
                $token = $resp_object->token;
                //store the expiry timestamp and adjust it for diffs between our server times
                if ($resp_object->validuntil) {
                    $validuntil = $resp_object->validuntil - ($resp_object->poodlltime - time());
                    //we refresh one hour out, to prevent any overlap
                    $validuntil = $validuntil - (1 * HOURSECS);
                }else{
                    $validuntil = 0;
                }

                //cache the token
                $tokenobject = new \stdClass();
                $tokenobject->token = $token;
                $tokenobject->validuntil = $validuntil;
                $tokenobject->subs=false;
                $tokenobject->apps=false;
                $tokenobject->sites=false;
                if (property_exists($resp_object, 'subs')) {
                    $tokenobject->subs = $resp_object->subs;
                }
                if (property_exists($resp_object, 'apps')) {
                    $tokenobject->apps = $resp_object->apps;
                }
                if (property_exists($resp_object, 'sites')) {
                    $tokenobject->sites = $resp_object->sites;
                }
                $cache->set('recentpoodlltoken', $tokenobject);
                $cache->set('recentpoodlluser', $apiuser);

            }else{
                $token = '';
                if ($resp_object && property_exists($resp_object, 'error')) {
                    //ERROR = $resp_object->error
                }
            }
        }else{
            $token='';
        }
        return $token;
    }

    /**
     * Fetches a transcript for the given audio file.
     * Note: Transcripts become ready in their own time. Fetch them here.
     *
     * @param string $audiourl The URL of the audio file.
     * @return string|false The transcript text, or false if access is denied.
     */
    public static function fetch_transcript($audiourl) {
        $url = $audiourl.'.txt';
        $transcript = self::curl_fetch($url);
        if (strpos($transcript, '<Error><Code>AccessDenied</Code>') > 0) {
            return false;
        }
        return $transcript;
    }
    /**
     * Fetches VTT (WebVTT) caption data for the given audio file.
     * Note: VTT data becomes ready in its own time. Fetch it here.
     *
     * @param string $audiourl The URL of the audio file.
     * @return string|false The VTT content, or false if access is denied.
     */
    public static function fetch_vtt($audiourl) {
        $url = $audiourl.'.vtt';
        $vtt = self::curl_fetch($url);
        if (strpos($vtt, '<Error><Code>AccessDenied</Code>') > 0) {
            return false;
        }
        return $vtt;
    }

    /**
     * Registers an ad-hoc task to fetch a transcript for an audio file.
     *
     * This method schedules a background task to process transcript retrieval.
     *
     * @param string $audiourl The URL of the audio file.
     * @param object $qa The question attempt object.
     * @param object $oldstep The previous attempt step.
     * @return bool Always returns true.
     */
    public static function register_fetch_transcript_task($audiourl, $qa, $oldstep) {
        $fetch_task = new \qtype_speakautograde\task\fetch_transcript_adhoc();
        $fetch_task->set_component(constants::M_COMPONENT);

        $customdata = new \stdClass();
        $customdata->audiourl = $audiourl;
        $customdata->qa = $qa;
        $customdata->oldstep = $oldstep;
        $customdata->taskcreationtime = time();

        //$fetch_task->set_custom_data($customdata);
        $fetch_task->set_custom_data_as_string(serialize($customdata));

        // queue it
        \core\task\manager::queue_adhoc_task($fetch_task);
        return true;
    }
}