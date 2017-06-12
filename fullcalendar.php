<?php 
/* 
Plugin Name: fullcalendar
Plugin URI: https://ircf.fr
Description: Display and customize one or many Google calendars. This is just a Wordpress wrapper for the fullcalendar Open Source project
Version: 2.0.0
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
  "head" => '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment-with-locales.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.2/fullcalendar.min.css" />
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.2/fullcalendar.print.css" media="print"/>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.2/fullcalendar.min.js"></script>',
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
    add_options_page('fullcalendar', 'fullcalendar', 20, 'fullcalendar', 'fullcalendar_options');	 
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
  if(isset($_POST['Submit'])){
    if ($_POST["Submit"]=="Modifier"){
      foreach($_POST as $key=>$value){
        if (substr($key,0,13)=="fullcalendar_"){
          $options[str_replace("fullcalendar_","",$key)] = stripslashes($value);
        }
      }
      update_option('fullcalendar_options', $options);
      echo '<div class="updated"><p><strong>'.__('Options enregistrées', 'fullcalendar_textdomain' ).'</strong></p></div>';
    }elseif ($_POST["Submit"]=="Réinitialiser"){
      update_option('fullcalendar_options', $fullcalendar_default_options);
      echo '<div class="updated"><p><strong>'.__('Options réinitialisées', 'fullcalendar_textdomain' ).'</strong></p></div>';
    }
  }
  ?>
  <div class="wrap">   
    <form method="post" name="options" target="_self">
      <h2>Configure fullcalendar</h2>
      <h3>Head (Javascript template)</h3>
      <textarea name="fullcalendar_head" cols="100" rows="10"><?php echo $options['head']?></textarea>
      <h3>Body (HTML template)</h3>
      <p>Short code parameters [fullcalendar param1=valeur1 param2=valeur2] can be used with : %param1%, %param2% . These expressions will be replaced by their respective values in the HTML code.</p>
      <textarea name="fullcalendar_body" cols="100" rows="30"><?php echo $options['body']?></textarea>
      <p class="submit"><input type="submit" name="Submit" value="Modifier" class="button-primary" /></p>
      <p class="submit"><input type="submit" name="Submit" value="Réinitialiser" class="button-primary" /></p>
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

function fullcalendar_head(){
  $options = fullcalendar_get_options();
  echo $options['head'];
}

function fullcalendar_scan () { 
  global $posts; 
  if ( !is_array ( $posts ) ) 
    return;
  foreach ( $posts as $post ) { 
   if ( false !== strpos ( $post->post_content, '[fullcalendar' ) ) { 
     add_action ( 'wp_head', 'fullcalendar_head' ); 
     break; 
   } 
  } 
} 
add_action ( 'template_redirect' , 'fullcalendar_scan' );