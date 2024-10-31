=== Plugin Name ===
Contributors: ChrisBuck
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SPAZAZLTNNRXG
Tags: recent posts, posts, post, recent, views, post views, counter, widget, sidebar
Requires at least: 2.8.0
Tested up to: 3.3.3
Stable tag: 1.5

Displays the most recent posts in a widget, how many times they have been viewed over last (n) days, and images for popularity.

== Description ==

This plugin requires the WP-PostViews plugin to be installed and setup to work correctly. (http://wordpress.org/extend/plugins/wp-postviews/)

This plugin displays a widget in the sidebar with the most recently viewed posts over the last 2 days, the number of times they were viewed, a link to each, and images corresponding to whether they were "hot" posts or not (calculated as a percentage of the most viewed post).

== Installation ==

1. Upload `recent-post-views.php` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Put the post in the sidebar via the 'Widgets' menu, save a title for the widget.
1. Use the shortcode [recentpostviews] in your pages and posts. See FAQ's for more info. Accepts "cat", "posts", and "days" as inputs.

== Frequently Asked Questions ==

= How do I use the shortcode? =
The basic format is [recentpostviews], which will by default show a list of the top 7 posts, from all categories, over the last 2 days.
Options are "cat", "posts" and "days".
* "cat" is the category ID.
* "posts" is the number of posts you want to display.
* "days" is the number of days back you want the shortcode to query, as in "over the last (n) days."

Example: [recentpostviews cat="5" posts="10" days="3"]
*This would display the 10 most recent posts from category 5 over the last 3 days.

If you're not getting any results for the shortcode, try changing the number of days.

By default, the "hotlevel" for posts is 75% or .75 in the shortcode. E.g., a post will not show up as "hot" unless it is greater than or equal to the post with the most number of views over the same time frame.

= How can I change options, like what is considered a "recent" post? =
Just add the widget and select your options.

= I have multiple widgets, and some of them have posts that are viewed the same number of times, but one is not a "hot" post. What gives? =
Whether a post is hot or not is dependent on the max number of views for any post over the timeframe you have selected. E.g., if you have a widget based on the last 3 days and one based on the last 7, the level for a "hot" post will be different. I'm planning on changing this in a future update, such that the max number of posts for a particular category will be calculated.

= The number of views for my posts are not updating, why? =
I actually had this issue myself, and discovered that it's because the postmeta table was not updating the number of views. As a temporary solution, I installed the WP-PostViews plugin (at http://wordpress.org/extend/plugins/wp-postviews/) and enabled the option in the settings menu that said to register views from everyone, not just guests. Thanks to GamerZ for a great plugin, and for helping to fix this issue. If you're having this problem, I suggest that recent-post-views should be used in conjunction with WP-PostViews.

== Screenshots ==
1. As you see from the screenshot, you get a nice sidebar widget with posts titles, links to the post, and a count of the recent number of views.

== Changelog ==

= 1.5 =
* Added settings sub-menu with styling options.
* Fixed list and table code for widgets and shortcode.

= 1.4 =
* Added shortcode functionality.
* Minor changes to the table tags in the widget.

= 1.3 =
* Prefixed all the variable names and made the paths to image files relative.
* This should be a more stable release, one that can co-exist peacefully with other plugins.

= 1.2 =
* Minor bug fix

= 1.1 =
* Added option to set threshold percent level for "hot" posts.
* Added option to change html color code for number of views.

= 1.0 =
* Adds option to change number of posts and to select specific categories.

= 0.5 =
* Adds option to widget to change number of days for recent posts.

= 0.1 =
* First release, displays most recent posts views over last 2 days.

== Upgrade Notice ==

= 1.5 =
* Adds styling options in settings and fixes table and list code.

= 1.4 =
* Adds new shortcode functionality.

= 1.3 =
* Prefixes variable names and creates relative paths to image files, for more stable plugin.

= 1.2 =
* Minor bug fix

= 1.1 =
* Adds options for changing "hot" posts and some styling of the widget.

= 1.0 =
* Adds option to change number of posts and to select specific categories.

= 0.5 =
Change how many days old until posts are no longer considered "recent."