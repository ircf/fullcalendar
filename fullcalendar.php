<?php 
/* 
Plugin Name: fullcalendar
Plugin URI: https://ircf.fr
Description: Display and customize one or many Google calendars. This is just a Wordpress wrapper for the fullcalendar Open Source project
Version: 3.4.0
Author: IRCF
Author URI: https://ircf.fr/
*/

/*  Copyright 2017  IRCF

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; 
*/

// Default plugin options, may be modified in the Settings menu
$fullcalendar_default_options = array(
  "head" => '',
  "body" => '<div id="loading" style="display:none">Chargement en cours...</div>
    <div id="calendar"></div>
    <script type="text/javascript">
    $(document).ready(function() {
      $("#calendar").fullCalendar({
        googleCalendarApiKey: "YOUR API KEY",
        eventSources: [
          {
            googleCalendarId: "abcd1234@group.calendar.google.com",
            className: "nice-event"
          }
        ]
      });
    });
    </script>'
);

// Admin menu

add_action('admin_menu', 'fullcalendar_menu');

function fullcalendar_menu() {
    add_options_page('FullCalendar', 'FullCalendar', 20, 'fullcalendar', 'fullcalendar_options');	 
}

function fullcalendar_get_options($field=null){
  global $fullcalendar_default_options;
  $options = get_option('fullcalendar_options');
  if (!is_array($options)) $options = array();
  $options = array_merge($fullcalendar_default_options,$options);
  if (isset($field)){
    return $options[$field];
  }else{
    return $options;
  }
}

function fullcalendar_options() {
  global $fullcalendar_default_options;
  $options = fullcalendar_get_options();
  if(isset($_POST['Submit']) && check_admin_referer('fullcalendar_options')){
    if ($_POST["Submit"] == __('Update', 'fullcalendar_textdomain' )){
      foreach($_POST as $key => $value){
        if (substr($key,0,13) == "fullcalendar_"){
          $options[str_replace("fullcalendar_", "", sanitize_key($key))] = stripslashes($value); // value can't be sanitized as it's HTML code
        }
      }
      update_option('fullcalendar_options', $options);
      echo '<div class="updated"><p><strong>'.__('Options saved', 'fullcalendar_textdomain' ).'</strong></p></div>';
    }elseif ($_POST["Submit"] == __('Reset', 'fullcalendar_textdomain' )){
      update_option('fullcalendar_options', $fullcalendar_default_options);
      echo '<div class="updated"><p><strong>'.__('Options resetted', 'fullcalendar_textdomain' ).'</strong></p></div>';
    }
  }
  ?>
  <div class="wrap">   
    <form method="post" name="options" target="_self">
      <?php wp_nonce_field( 'fullcalendar_options' ); ?>
      <h2><?=__('Configure FullCalendar', 'fullcalendar_textdomain' )?></h2>
      <p><?=__('For the FullCalendar API documentation, please visit <a href="https://fullcalendar.io" target="_blank">fullcalendar.io</a>', 'fullcalendar_textdomain' )?></p>
      <h3><?=__('Head template', 'fullcalendar_textdomain' )?></h3>
      <p><?=__('<strong>WARNING : </strong> Setting the head template will remove the default FullCalendar JS+CSS loading.', 'fullcalendar_textdomain' )?></p>
      <textarea name="fullcalendar_head" cols="100" rows="10"><?php echo $options['head']?></textarea>
      <h3><?=__('Body template', 'fullcalendar_textdomain' )?></h3>
      <p><?=__('Short code parameters [fullcalendar param1=valeur1 param2=valeur2] can be used with : %param1%, %param2% . These expressions will be replaced by their respective values in the HTML code.', 'fullcalendar_textdomain' )?></p>
      <textarea name="fullcalendar_body" cols="100" rows="30"><?php echo $options['body']?></textarea>
      <p class="submit">
        <input type="submit" name="Submit" value="<?=__('Update', 'fullcalendar_textdomain' )?>" class="button-primary" />
        <input type="submit" name="Submit" value="<?=__('Reset', 'fullcalendar_textdomain' )?>" class="button" />
      </p>
    </form>
  </div>
  <?php
}

// Replace placeholders "%whatever%" in a text
function fullcalendar_placeholders($text,$placeholders){
  foreach($placeholders as $key=>$value){
    $text = str_replace('%'.$key.'%',$value,$text);
  }
  return $text;
}

// Short code
// TODO Add TinyMCE shortcode button
function fullcalendar_body($atts) {
  $options = fullcalendar_get_options();
  $output = fullcalendar_placeholders($options['body'],$atts);
  return $output;
}
add_shortcode('fullcalendar', 'fullcalendar_body');

// Enqueue scripts and styles
function fullcalendar_enqueue_scripts() {
  wp_enqueue_script('moment', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js', array('jquery'));
  wp_enqueue_script('fullcalendar', '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js', array('jquery','moment'));
  wp_enqueue_script('fullcalendar_gcal', '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/gcal.js', array('fullcalendar'));
  wp_enqueue_style('fullcalendar', '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.css');
  wp_enqueue_style('fullcalendar_print', '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.print.min.css', array(), null, 'print');
}

// Custom head template
function fullcalendar_head(){
  $options = fullcalendar_get_options();
  echo $options['head'];
}
if (fullcalendar_get_options('head') != ''){
  add_action('wp_head', 'fullcalendar_head');
}else{
  add_action('wp_enqueue_scripts', 'fullcalendar_enqueue_scripts');
}
