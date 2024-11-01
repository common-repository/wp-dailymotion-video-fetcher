=== Plugin Name ===
Contributors: wpdigger, wpdigger
Tags: dailymotion, widget, video
Requires at least: 3.5
Tested up to: 4.4
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add widget to display your latest dailymotion videos

== Description ==

Add widget to display any dailymotion user videos. Just set Dailymotion's username and choose the number of videos you'd like to display and set other layout options.

== Frequently Asked Questions ==

= Can it be customisable ? =

Yes there are somes filters that can help you get the output that you want :

the **wst_dailymotion_fields** filter, that let you filter the video fields to get from dailymotion.
Possible fields can be found here : https://developer.dailymotion.com/documentation#video-fields

Example :
`
function wst_dailymotion_fields( $fields ){

    array_push($fields, 'duration_formatted');

    return $fields;
}
add_filter( 'wst_dailymotion_fields', 'wst_dailymotion_fields' );
`

== Screenshots ==

1. Customizer view of the widget
2. How the widget displays with the theme TwentySixteen
3. How the widget displays with customization with the theme TwentySixteen
4. How the video popup show

== Changelog ==

= 1 =
* First version of the plugin
