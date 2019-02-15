=== FullCalendar  ===
Contributors: IRCF
Donate link: https://ircf.fr
Tags: fullcalendar, google, calendar, jquery, agenda, ircf
Requires at least: 3.0.0
Tested up to: 4.9.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display and customize one or many Google calendars.
A non-official WordPress plugin for the (https://fullcalendar.io/) Open Source project.

== Description ==

This plugin provides the following features :

* A **[fullcalendar] shortcode** with custom parameters to be inserted in your pages/posts
* An admin page where you can define a custom **HEAD** and **BODY template** for the [fullcalendar] shortcode
* By default **FullCalendar 3.4.0** JS+CSS is loaded locally, your can change the version and/or load from cdnjs by setting the HEAD template (**WARNING** : Setting the HEAD template will remove the default FullCalendar JS+CSS loading)
* A default BODY template to display a Google Calendar (just fill in your API KEY and your Google Calendar ID)

== Installation ==

1. Download plugin Full Calendar into /wp-content/plugins/.
2. Activate plugin.
3. Visit Google Calendar and create your calendar.
4. Configure FullCalendar in the WordPress Setup menu.
5. Insert the [fullcalendar] shortcode in your pages/posts.

== Screenshots ==

1. You can setup the head and body templates in the admin option page. Keep the head template empty to load the fullcalendar local library
2. Fill in the head template if you want to load fullcalendar from CDN, or another version or plugins.
3. The calendar is displayed in your page when you insert the [fullcalendar] shortcode. Here is a sample with multiple calendars and a few CSS.
4. Here is a sample with a single calendar and a custom CSS.

== Changelog ==  

= 1.0 =  
* First version.

= 2.2.2 =
* Updated to fullcalendar 2.2.2

= 3.4.0 =
* Updated to fullcalendar 3.4.0.
