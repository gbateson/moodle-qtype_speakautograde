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
 * Strings for component 'qtype_speakautograde', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage speakautograde
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Speak (auto-grade)';
$string['pluginname_help'] = 'In response to a question that may include an image, the respondent speaks an answer of one or more paragraphs. Initially, a grade is awarded automatically based on the number of chars, words, sentences or paragarphs, and the presence of certain target phrases. The automatic grade may be overridden later by the teacher.';
$string['pluginname_link'] = 'question/type/speakautograde';
$string['pluginnameadding'] = 'Adding an Speak (auto-grade) question';
$string['pluginnameediting'] = 'Editing an Speak (auto-grade) question';
$string['pluginnamesummary'] = 'Allows a short speech segment, consisting of several sentences or paragraphs, to be submitted as a question response. The speak is graded automatically. The grade may be overridden later.';

$string['privacy:metadata'] = 'The Speak (auto-grade) question type plugin does not store any personal data.';

// CloudPoodll settings and options
$string['apisecret_details'] = 'The Poodll API secret. See <a href= "https://support.poodll.com/support/solutions/articles/19000083076-cloud-poodll-api-secret">here</a> for more details';
$string['apisecret'] = 'Poodll API Secret ';
$string['apiuser_details'] = 'The Poodll account username that authorises Poodll on this site.';
$string['apiuser'] = 'Poodll API User ';
$string['appauthorised']= 'Speak(auto grade) is authorised for this site.';
$string['appnotauthorised']= 'Speak(auto grade) is NOT authorised for this site.';
$string['audioskin_help'] = 'Select a design skin for the audio player';
$string['audioskin'] = 'Audio recorder skin';
$string['awsregion'] = 'AWS Region';
$string['credentialsinvalid'] = 'The API user and secret entered could not be used to get access. Please check them.';
$string['currentsubmission'] = 'Current Submission:';
$string['defaultrecorder_details'] = '';
$string['defaultrecorder'] = 'Recorder type';
$string['displaysubs'] = '{$a->subscriptionname} : expires {$a->expiredate}';
$string['dublin'] = 'Dublin, Ireland';
$string['en-au'] = 'English (Aus.)';
$string['en-uk'] = 'English (UK)';
$string['en-us'] = 'English (US)';
$string['es-us'] = 'Spanish (US)';
$string['expiredays_help'] = 'The number of days that the audio/video file will be kept on the server.';
$string['expiredays'] = 'Days to keep file';
$string['fallback_details'] = 'If the browser does not support HTML5 recording for the selected mediatype, fallback to an upload screen or a warning.';
$string['fallback'] = 'non-HTML5 Fallback';
$string['fallbackiosupload'] = 'iOS: upload, else warning';
$string['fallbackupload'] = 'Upload';
$string['fallbackwarning'] = 'Warning';
$string['forever'] = 'Never expire';
$string['formataudio']="Audio recording";
$string['formatupload']="Upload media file";
$string['formatvideo']="Video recording";
$string['fr-ca'] = 'French (Can.)';
$string['frankfurt'] = 'Frankfurt, Germany (slow)';
$string['language_help'] = 'Select the language that will be used in the responses.';
$string['language'] = 'Speaker language';
$string['london'] = 'London, U.K (slow)';
$string['noapisecret'] = 'No API secret entered. Plugin will not work correctly.';
$string['noapiuser'] = 'No API user entered. Plugin will not work correctly.';
$string['notimelimit'] = 'No time limit';
$string['notokenincache']= 'Refresh to see license information. Contact support if there is a problem.';
$string['onemin'] = '1 minute';
$string['oneminxsecs'] = '1 minutes {$a} seconds';
$string['ottawa'] = 'Ottawa, Canada (slow)';
$string['recorder'] = 'Recorder type';
$string['recorderaudio'] = 'Audio recorder';
$string['recordertype'] = 'Cloud Poodll recording type';
$string['recordervideo'] = 'Video recorder';
$string['refreshtoken']= 'Refresh license information';
$string['region'] = 'AWS Region';
$string['responseformat_help'] = 'Specify if the response will be audio only, or audio and video.';
$string['responseformat'] = 'Response format';
$string['responseisnotoriginal'] = 'Please make your text more original.';
$string['saopaulo'] = 'Sao Paulo, Brazil (slow)';
$string['skin123'] = 'One Two Three';
$string['skinbmr'] = 'Burnt Rose';
$string['skinfresh'] = 'Fresh (audio only)';
$string['skinonce'] = 'Once';
$string['skinplain'] = 'Plain';
$string['skinupload'] = 'Upload';
$string['sydney'] = 'Sydney, Australia';
$string['timelimit_help'] = 'The maximum time allowed for recording a response.';
$string['timelimit'] = 'Recording time limit';
$string['tokyo'] = 'Tokyo, Japan';
$string['transcode_details']= 'Transcode audio to MP3 and video to MP4.';
$string['transcode_help'] = 'If this option is selected, then audio data will be converted to MP3 format, and video data will be converted to MP4 format.';
$string['transcode']= 'Transcode';
$string['transcriber_amazontranscribe'] = 'Amazon Transcribe';
$string['transcriber_chrome'] = 'Chrome Speech API';
$string['transcriber_details'] = 'The transcription engine to use';
$string['transcriber_help'] = 'Select the transcription engine that will be used to convert audio to text.';
$string['transcriber'] = 'Transcriber';
$string['transcriptnotready'] = 'Transcript not ready yet';
$string['transcripttitle'] = 'Transcript';
$string['useast1'] = 'US East';
$string['videoskin_help'] = 'Select a design skin for the video player';
$string['videoskin'] = 'Video recorder skin';
$string['xmins'] = '{$a} minutes';
$string['xminsecs'] = '{$a->minutes} minutes {$a->seconds} seconds';
$string['xsecs'] = '{$a} seconds';
