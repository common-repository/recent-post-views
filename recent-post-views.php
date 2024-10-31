<?php
/*
Plugin Name: Recent Post Views
Plugin URI: http://cbuckconsulting.com/recentPostViews/
Description: Displays the most recent posts from the last (n) days in a widget, the number of post views, selectable by post category, and images for whether they are "hot" or not, based on the post with the max number of views. Shortcode included as well.
Version: 1.5
Author: ChrisBuck
Author URI: http://cbuckconsulting.com
License: GPL2
*/

/*  Copyright 2012  Chris Buck  (email : Chris@CBuckConsulting.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* 
Please rate 5 stars if you love this plugin, at http://wordpress.org/extend/plugins/recent-post-views.

NOTICE: Shortcode now available. Here are the instructions:
	- The basic format is [recentpostviews], which will by default show a list of the top 7 posts, from all categories, over the last 2 days.
	- Options are "cat", "posts" and "days".
		-- "cat" is the category ID.
		-- "posts" is the number of posts you want to display.
		-- "days" is the number of days back you want the shortcode to query, as in "over the last (n) days."
	- Example: [recentpostviews cat="5" posts="10" days="3"]
		-- This would display the 10 most recent posts from category 5 over the last 3 days.

*/

/*
WIDGET CODE
*/

class recentPostViews extends WP_Widget
{
  function recentPostViews()
  { //Constructor
    $widget_ops = array('classname' => 'recentPostViews', 'description' => 'Allows you to display recent posts, with the number of views, in the sidebar.');
    $this->WP_Widget('recentPostViews', 'Recent Post Views', $widget_ops);
  }
 
  function form($instance)
  { //Widget form in backend
    $instance = wp_parse_args((array) $instance, array( 'title' => '', 'cb_rpv_timeframe' => '2', 'cb_rpv_numposts' => '7', 'cb_rpv_category' => 'ALL', 'cb_rpv_hotlevel' => '.75', 'cb_rpv_hotcolor' => 'd5000'));
    $title = esc_attr($instance['title']);
	$cb_rpv_timeframe = intval($instance['cb_rpv_timeframe']);
	$cb_rpv_numposts = intval($instance['cb_rpv_numposts']);
	$cb_rpv_category = $instance['cb_rpv_category'];
	$cb_rpv_hotlevel = $instance['cb_rpv_hotlevel'];
	$cb_rpv_hotcolor = $instance['cb_rpv_hotcolor'];

?><!--Title form element-->
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
  
  <!--Number of days element-->
  <p><label for="<?php echo $this->get_field_id('cb_rpv_timeframe'); ?>">Number of days for "recent" posts: <input class="widefat" id="<?php echo $this->get_field_id('cb_rpv_timeframe'); ?>" name="<?php echo $this->get_field_name('cb_rpv_timeframe'); ?>" type="text" value="<?php echo attribute_escape($cb_rpv_timeframe); ?>" /></label></p>
  <p style="font-size:10px;"><em>Only display posts from the last (x) days. Expressed as an integer.</em></p>
 
<!--Number of posts element-->
  <p>
			<label for="<?php echo $this->get_field_id('cb_rpv_numposts'); ?>"><?php _e('Number of posts to show:', 'recentPostViews'); ?>
				<select name="<?php echo $this->get_field_name('cb_rpv_numposts'); ?>" id="<?php echo $this->get_field_id('cb_rpv_numposts'); ?>" class="widefat">
					<option value="1"<?php selected(1, $cb_rpv_numposts); ?>><?php _e(1, 'recentPostViews'); ?></option>
					<option value="2"<?php selected(2, $cb_rpv_numposts); ?>><?php _e(2, 'recentPostViews'); ?></option>
					<option value="3"<?php selected(3, $cb_rpv_numposts); ?>><?php _e(3, 'recentPostViews'); ?></option>
					<option value="4"<?php selected(4, $cb_rpv_numposts); ?>><?php _e(4, 'recentPostViews'); ?></option>
					<option value="5"<?php selected(5, $cb_rpv_numposts); ?>><?php _e(5, 'recentPostViews'); ?></option>
					<option value="6"<?php selected(6, $cb_rpv_numposts); ?>><?php _e(6, 'recentPostViews'); ?></option>
					<option value="7"<?php selected(7, $cb_rpv_numposts); ?>><?php _e(7, 'recentPostViews'); ?></option>
					<option value="8"<?php selected(8, $cb_rpv_numposts); ?>><?php _e(8, 'recentPostViews'); ?></option>
					<option value="9"<?php selected(9, $cb_rpv_numposts); ?>><?php _e(9, 'recentPostViews'); ?></option>
					<option value="10"<?php selected(10, $cb_rpv_numposts); ?>><?php _e(10, 'recentPostViews'); ?></option>
					<option value="15"<?php selected(15, $cb_rpv_numposts); ?>><?php _e(15, 'recentPostViews'); ?></option>
					<option value="20"<?php selected(20, $cb_rpv_numposts); ?>><?php _e(20, 'recentPostViews'); ?></option>
					<option value="25"<?php selected(25, $cb_rpv_numposts); ?>><?php _e(25, 'recentPostViews'); ?></option>
				</select>
			</label>
		</p>
  
  <!--Category element-->
  <?php //First thing is to get all the category names and ID's for the select list.
  $catargs=array(
  'orderby' => 'name',
  'order' => 'ASC'
  );
  $cb_rpv_categories=get_categories($catargs); ?>
  <p>
			<label for="<?php echo $this->get_field_id('cb_rpv_category'); ?>"><?php _e('Select a specific category:', 'recentPostViews'); ?>
				<select name="<?php echo $this->get_field_name('cb_rpv_category'); ?>" id="<?php echo $this->get_field_id('cb_rpv_category'); ?>" class="widefat">
					<option value="ALL"<?php selected('ALL', $cb_rpv_category); ?>><?php _e('ALL', 'recentPostViews'); ?></option>
					<?php
					foreach ($cb_rpv_categories as $cat) {
						echo '<option value="'.$cat->cat_ID.'"';
						selected ($cat->cat_ID, $cb_rpv_category);
						echo '>';
						_e($cat->name, 'recentPostViews');
						echo '</option>';
					}?>
				</select>
			</label>
		</p>
<!--"Hotlevel" element-->
	<p><label for="<?php echo $this->get_field_id('cb_rpv_hotlevel'); ?>">Percent threshold for "hot" status:<input class="widefat" id="<?php echo $this->get_field_id('cb_rpv_hotlevel'); ?>" name="<?php echo $this->get_field_name('cb_rpv_hotlevel'); ?>" type="text" value="<?php echo attribute_escape($cb_rpv_hotlevel); ?>" /></label></p>
	<p style="font-size:10px;"><em>Must be expressed as a decimal, e.g., .75.</br>
	This is the percent threshold, of the most views for a post, to be considered "hot." If 0 is the level, all posts are hot. At 1.0, only the most viewed is hot.</em></p>

<!--Hotcolor element-->
  <p><label for="<?php echo $this->get_field_id('cb_rpv_hotcolor'); ?>">(Hex) color for "views"?: <input class="widefat" id="<?php echo $this->get_field_id('cb_rpv_hotcolor'); ?>" name="<?php echo $this->get_field_name('cb_rpv_hotcolor'); ?>" type="text" value="<?php echo attribute_escape($cb_rpv_hotcolor); ?>" /></label></p>
  <p style="font-size:10px;"><em>Expressed as a hex color code, no '#', e.g., d5000.</em></p>

<?php
//End widget form
  }
 
  function update($new_instance, $old_instance)
  { //Saves the widget
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['cb_rpv_timeframe'] = $new_instance['cb_rpv_timeframe'];
	$instance['cb_rpv_numposts'] = $new_instance['cb_rpv_numposts'];
	$instance['cb_rpv_category'] = $new_instance['cb_rpv_category'];
	$instance['cb_rpv_hotlevel'] = $new_instance['cb_rpv_hotlevel'];
	$instance['cb_rpv_hotcolor'] = $new_instance['cb_rpv_hotcolor'];
    return $instance;
  }
 
  function widget($args, $instance)
  { //Prints the widget
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
	$cb_rpv_timeframe = empty($instance['cb_rpv_timeframe']) ? '' : apply_filters('widget_cb_rpv_timeframe', $instance['cb_rpv_timeframe']);
	$cb_rpv_numposts = empty($instance['cb_rpv_numposts']) ? '' : apply_filters('widget_cb_rpv_numposts', $instance['cb_rpv_numposts']);
	$cb_rpv_category = empty($instance['cb_rpv_category']) ? '' : apply_filters('widget_cb_rpv_category', $instance['cb_rpv_category']);
	$cb_rpv_hotlevel = empty($instance['cb_rpv_hotlevel']) ? '' : apply_filters('widget_cb_rpv_hotlevel', $instance['cb_rpv_hotlevel']);
	$cb_rpv_hotcolor = empty($instance['cb_rpv_hotcolor']) ? '' : apply_filters('widget_cb_rpv_hotcolor', $instance['cb_rpv_hotcolor']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
/*
*
Widget Stuff Goes Here...
*
*/

//Preliminary housekeeping
	global $wpdb;
	$prefix = $wpdb->prefix;
	$cb_rpv_homeurl = home_url();
	$cb_rpv_hottestimg = plugins_url('recent-post-views/images/Hottest.png', _FILE_);
	$cb_rpv_hotimg = plugins_url('recent-post-views/images/Hot.png', _FILE_);
	$cb_rpv_ndays = $cb_rpv_timeframe;
	$cb_rpv_fontcolor = '#'.$cb_rpv_hotcolor;


//Get options from the settings page
$options = get_option('cb_rpv_options');
$select_bold = $options['select_bold'];
$select_italics = $options['select_italics'];
$select_list_style = $options['select_list_style'];

//Set variables for output based on options
$cb_rpv_open_bold = '';
$cb_rpv_close_bold = '';
if ($select_bold == 'bold') {
	$cb_rpv_open_bold .= '<strong>';
	$cb_rpv_close_bold .= '</strong>';
}

$cb_rpv_open_italics = '';
$cb_rpv_close_italics = '';
if ($select_italics == 'em') {
	$cb_rpv_open_italics = '<em>';
	$cb_rpv_close_italics = '</em>';
}

$cb_rpv_open_list = '';
$cb_rpv_close_list = '';
if ($select_list_style == 'ul') {
	$cb_rpv_open_list .= '<ul>';
	$cb_rpv_close_list .= '</ul>';
}
else {
	$cb_rpv_open_list .= '<ol>';
	$cb_rpv_close_list .= '</ol>';
}


//Calculate the max views
$cb_rpv_maxviews = $wpdb->get_row("SELECT SUM(".$prefix."postmeta.meta_value) as 'views'
FROM ".$prefix."postmeta
LEFT JOIN ".$prefix."posts ON ".$prefix."postmeta.post_id = ".$prefix."posts.ID
WHERE ".$prefix."postmeta.meta_key = 'views' AND ".$prefix."posts.post_type = 'post' AND ".$prefix."posts.post_date > DATE_SUB(CURDATE(), INTERVAL ".$cb_rpv_ndays." DAY)
GROUP BY ".$prefix."posts.post_title
ORDER BY views DESC
LIMIT 1",ARRAY_A);

//First check if the user selected a category for the widget or not. Then switch out a query based on that info.
if ($cb_rpv_category == 'ALL') {
$cb_rpv_postviews = $wpdb->get_results("SELECT ".$prefix."posts.post_date, ".$prefix."posts.ID, ".$prefix."posts.post_title, ".$prefix."posts.post_name, SUM(".$prefix."postmeta.meta_value) as 'views'
	FROM ".$prefix."postmeta
	LEFT JOIN ".$prefix."posts ON ".$prefix."postmeta.post_id = ".$prefix."posts.ID
	WHERE ".$prefix."postmeta.meta_key = 'views' AND ".$prefix."posts.post_type = 'post' AND ".$prefix."posts.post_date > DATE_SUB(CURDATE(), INTERVAL ".$cb_rpv_ndays." DAY)
	GROUP BY ".$prefix."posts.post_title
	ORDER BY views DESC
	LIMIT ".$cb_rpv_numposts,OBJECT);
}
else {
$cb_rpv_postviews = $wpdb->get_results("SELECT ".$prefix."posts.post_date, ".$prefix."posts.ID, ".$prefix."posts.post_title, ".$prefix."posts.post_name, SUM(".$prefix."postmeta.meta_value) as 'views', ".$prefix."term_relationships.term_taxonomy_id, ".$prefix."terms.name
	FROM ".$prefix."postmeta
	LEFT JOIN ".$prefix."posts ON ".$prefix."postmeta.post_id = ".$prefix."posts.ID
        LEFT JOIN ".$prefix."term_relationships ON ".$prefix."posts.ID = ".$prefix."term_relationships.Object_ID
        LEFT JOIN ".$prefix."terms ON ".$prefix."terms.term_id = ".$prefix."term_relationships.term_taxonomy_id
	WHERE ".$prefix."postmeta.meta_key = 'views' AND ".$prefix."posts.post_type = 'post' AND ".$prefix."posts.post_date > DATE_SUB(CURDATE(), INTERVAL ".$cb_rpv_ndays." DAY) AND ".$prefix."terms.term_id = ".$cb_rpv_category."
	GROUP BY ".$prefix."posts.post_title
	ORDER BY views DESC
	LIMIT ".$cb_rpv_numposts,OBJECT);
}

//Open the table and the list
echo $cb_rpv_open_list;

foreach ($cb_rpv_postviews as $widgetvalue) {

//if the post has the most views, include the "hottest" image.
if ($widgetvalue->views == $cb_rpv_maxviews['views'])
	echo '<table border="0"><tr><td><li></td><td><img src="'.$cb_rpv_hottestimg.'" width="25" height="9" align="top"></td><td>'.$cb_rpv_open_bold.$cb_rpv_open_italics.'<a href="'.$cb_rpv_homeurl.'/?p='.$widgetvalue->ID.'">'.$widgetvalue->post_title.'</a>'.$cb_rpv_close_italics.$cb_rpv_close_bold.' - <font color="'.$cb_rpv_fontcolor.'">'.$widgetvalue->views.' views</font></td></tr></li></table>';

//if the post has more than the x% of the most views, include the "hot" image.
else if ($widgetvalue->views >= $cb_rpv_hotlevel * $cb_rpv_maxviews['views'])
	echo '<table border="0"><tr><td><li></td><td><img src="'.$cb_rpv_hotimg.'" width="25" height="9" align="top"></td><td>'.$cb_rpv_open_bold.$cb_rpv_open_italics.'<a href="'.$cb_rpv_homeurl.'/?p='.$widgetvalue->ID.'">'.$widgetvalue->post_title.'</a>'.$cb_rpv_close_italics.$cb_rpv_close_bold.' - <font color="'.$cb_rpv_fontcolor.'">'.$widgetvalue->views.' views</font></td></tr></li></table>';

//otherwise, just list the post with a link.
else
	echo '<table border="0"><tr><td><li></td><td>'.$cb_rpv_open_bold.$cb_rpv_open_italics.'<a href="'.$cb_rpv_homeurl.'/?p='.$widgetvalue->ID.'">'.$widgetvalue->post_title.'</a>'.$cb_rpv_close_italics.$cb_rpv_close_bold.' - '.$widgetvalue->views.' views</td></tr></li></table>';

}
echo $cb_rpv_close_list;
	
    echo $after_widget;
  }
    
}
add_action( 'widgets_init', create_function('', 'return register_widget("recentPostViews");') );
?>
<?php
/*
*
*
SHORTCODE
*
*
*/

/*
* This is the function that the shortcode executes
* This shows the most recently viewed posts over the last (n) days, within a user-selected category.
* @param	$atts	cat		the 'cat' attribute is the category ID.
* @param	$atts	posts	the 'posts' attribute is the LIMIT query, or the number of posts to show.
* @param	$atts	days	the 'days' attribute is the number of days of posts to query.
*/

add_shortcode('recentpostviews', 'cb_rpv_scfunc');

function cb_rpv_scfunc ($atts) {

	global $wpdb;
	$prefix = $wpdb->prefix;
	$cb_rpv_homeurl = home_url();
	$cb_rpv_hottestimg = plugins_url('recent-post-views/images/Hottest.png', _FILE_);
	$cb_rpv_hotimg = plugins_url('recent-post-views/images/Hot.png', _FILE_);
	$cb_rpv_hotlevel = .75;
	
	//Set default variables based on user inputs, will be used in queries.
	if ($atts['days'] > 0) {
		$days = $atts['days'];
		}
		else {
		$days = 2;
		};
	
	if ($atts['posts'] > 0) {
		$posts = $atts ['posts'];
		}
		else {
		$posts = 7;
		}
		
	//Get options from the settings page
	$options = get_option('cb_rpv_options');
	$select_bold = $options['select_bold'];
	$select_italics = $options['select_italics'];
	$select_list_style = $options['select_list_style'];

	//Set variables for output based on options
	$cb_rpv_open_bold = '';
	$cb_rpv_close_bold = '';
	if ($select_bold == 'bold') {
		$cb_rpv_open_bold .= '<strong>';
		$cb_rpv_close_bold .= '</strong>';
	}

	$cb_rpv_open_italics = '';
	$cb_rpv_close_italics = '';
	if ($select_italics == 'em') {
		$cb_rpv_open_italics = '<em>';
		$cb_rpv_close_italics = '</em>';
	}

	$cb_rpv_open_list = '';
	$cb_rpv_close_list = '';
	if ($select_list_style == 'ul') {
		$cb_rpv_open_list .= '<ul>';
		$cb_rpv_close_list .= '</ul>';
	}
	else {
		$cb_rpv_open_list .= '<ol>';
		$cb_rpv_close_list .= '</ol>';
	}
		
	//calculate the max views for a post over the last n days.
	$cb_rpv_maxviews = $wpdb->get_row("SELECT SUM(".$prefix."postmeta.meta_value) as 'views'
		FROM ".$prefix."postmeta
		LEFT JOIN ".$prefix."posts ON ".$prefix."postmeta.post_id = ".$prefix."posts.ID
		WHERE ".$prefix."postmeta.meta_key = 'views' AND ".$prefix."posts.post_type = 'post' AND ".$prefix."posts.post_date > DATE_SUB(CURDATE(), INTERVAL ".$days." DAY)
		GROUP BY ".$prefix."posts.post_title
		ORDER BY views DESC
		LIMIT 1",ARRAY_A);
	
	//if the category is set (greater than zero), the query will return posts from the category.
	if ($atts['cat'] > 0) {
		//query all the posts with that category
		$cb_rpv_scpostviews = $wpdb->get_results("SELECT ".$prefix."posts.post_date, ".$prefix."posts.ID, ".$prefix."posts.post_title, ".$prefix."posts.post_name, SUM(".$prefix."postmeta.meta_value) as 'views', ".$prefix."term_relationships.term_taxonomy_id, ".$prefix."terms.name
			FROM ".$prefix."postmeta
			LEFT JOIN ".$prefix."posts ON ".$prefix."postmeta.post_id = ".$prefix."posts.ID
			LEFT JOIN ".$prefix."term_relationships ON ".$prefix."posts.ID = ".$prefix."term_relationships.Object_ID
			LEFT JOIN ".$prefix."terms ON ".$prefix."terms.term_id = ".$prefix."term_relationships.term_taxonomy_id
			WHERE ".$prefix."postmeta.meta_key = 'views' AND ".$prefix."posts.post_type = 'post' AND ".$prefix."posts.post_date > DATE_SUB(CURDATE(), INTERVAL ".$days." DAY) AND ".$prefix."terms.term_id = ".$atts['cat']."
			GROUP BY ".$prefix."posts.post_title
			ORDER BY views DESC
			LIMIT ".$posts,OBJECT);
			
		//return the output format
		
			$output = '';
			$output .= '<ul><table>';
			
			foreach ($cb_rpv_scpostviews as $scvalues) {

				if ($scvalues->views == $cb_rpv_maxviews['views']) {
					$output .= '<li><tr>';
					
					$output .= '<td><img src="'.$cb_rpv_hottestimg.'" width="25" height="9" align="top"></td><td><strong><a href="'.$cb_rpv_homeurl.'/?p='.$scvalues->ID.'">'.$scvalues->post_title.'</a></strong> - <font color="'.$cb_rpv_fontcolor.'">'.$scvalues->views.' views</font></td></tr>';

					$output .= '</li>';
				}
				
				else if ($scvalues->views >= $cb_rpv_hotlevel * $cb_rpv_maxviews['views']) {
					$output .= '<li><tr>';
					
					$output .= '<td><img src="'.$cb_rpv_hotimg.'" width="25" height="9" align="top"></td><td><strong><a href="'.$cb_rpv_homeurl.'/?p='.$scvalues->ID.'">'.$scvalues->post_title.'</a></strong> - <font color="'.$cb_rpv_fontcolor.'">'.$scvalues->views.' views</font></td></tr>';

					$output .= '</li>';
				}
				
				else {
					$output .= '<li><tr>';

					$output .= '<td></td><td><strong><a href="'.$cb_rpv_homeurl.'/?p='.$scvalues->ID.'">'.$scvalues->post_title.'</a></strong> - '.$scvalues->views.' views</td></tr>';

					$output .= '</li>';
				}
			}
			$output .= '</table></ul>';
			
			return $output;
	}
	//otherwise, the category is not set, and the shortcode should return posts from all categories, which is a different query. I know I should condense this... later.	
	else {
		//query all the posts with all categories.
		$cb_rpv_scpostviews = $wpdb->get_results("SELECT ".$prefix."posts.post_date, ".$prefix."posts.ID, ".$prefix."posts.post_title, ".$prefix."posts.post_name, SUM(".$prefix."postmeta.meta_value) as 'views'
			FROM ".$prefix."postmeta
			LEFT JOIN ".$prefix."posts ON ".$prefix."postmeta.post_id = ".$prefix."posts.ID
			WHERE ".$prefix."postmeta.meta_key = 'views' 
				AND ".$prefix."posts.post_type = 'post' 
				AND ".$prefix."posts.post_date > DATE_SUB(CURDATE(), INTERVAL ".$days." DAY)
			GROUP BY ".$prefix."posts.post_title
			ORDER BY views DESC
			LIMIT ".$posts,OBJECT);

		//return the formatted output
			$output = '';
			$output .= $cb_rpv_open_list;
			
			foreach ($cb_rpv_scpostviews as $scvalues) {

				if ($scvalues->views == $cb_rpv_maxviews['views']) {
					$output .= '<table border="0"><tr><td><li></td>';
					
					$output .= '<td><img src="'.$cb_rpv_hottestimg.'" width="25" height="9" align="top"></td><td>'.$cb_rpv_open_bold.$cb_rpv_open_italics.'<a href="'.$cb_rpv_homeurl.'/?p='.$scvalues->ID.'">'.$scvalues->post_title.'</a>'.$cb_rpv_close_italics.$cb_rpv_close_bold.' - <font color="'.$cb_rpv_fontcolor.'">'.$scvalues->views.' views</font></td></tr>';

					$output .= '</li></table>';
				}
				
				else if ($scvalues->views >= $cb_rpv_hotlevel * $cb_rpv_maxviews['views']) {
					$output .= '<table border="0"><tr><td><li></td>';
					
					$output .= '<td><img src="'.$cb_rpv_hotimg.'" width="25" height="9" align="top"></td><td>'.$cb_rpv_open_bold.$cb_rpv_open_italics.'<a href="'.$cb_rpv_homeurl.'/?p='.$scvalues->ID.'">'.$scvalues->post_title.'</a>'.$cb_rpv_close_italics.$cb_rpv_close_bold.' - <font color="'.$cb_rpv_fontcolor.'">'.$scvalues->views.' views</font></td></tr>';

					$output .= '</li></table>';
				}
				
				else {
					$output .= '<table border="0"><tr><td><li></td>';
					
					$output .= '<td>'.$cb_rpv_open_bold.$cb_rpv_open_italics.'<a href="'.$cb_rpv_homeurl.'/?p='.$scvalues->ID.'">'.$scvalues->post_title.'</a>'.$cb_rpv_close_italics.$cb_rpv_close_bold.' - <font color="'.$cb_rpv_fontcolor.'">'.$scvalues->views.' views</font></td></tr>';

					$output .= '</li></table>';
				}
			}
			$output .= $cb_rpv_close_list;
			
			return $output;
	}
	
	};
?>
<?php
/*
*
Options Page in Settings
*
*/

//Add the admin options page
add_action('admin_menu', 'cb_rpv_plugin_add_page');
function cb_rpv_plugin_add_page() {
	add_options_page( 'Recent Post Views', 'Recent Post Views', 'manage_options', 'recent-post-views', 'cb_rpv_plugin_options_page' );
};

//Draw the options page
function cb_rpv_plugin_options_page () {
?>
	<div class="wrap">
	<?php screen_icon('plugins'); ?>
	<h2>Recent Post Views</h2>
	<form action="options.php" method="post">
	
	<?php
	settings_fields('cb_rpv_options');
	do_settings_sections('recent-post-views');
	?>
	
	<input name="Submit" type="submit" value="Save Changes" />
	</form></div>
	<?php
};

//Register new settings
add_action('admin_init', 'cb_rpv_admin_init');
function cb_rpv_admin_init() {
register_setting('cb_rpv_options', 'cb_rpv_options' /*'cv_rpv_validate_options'*/);

	//Define sections and settings
	add_settings_section(
		'cb_rpv_main',
		'Recent Post Views Settings',
		'cb_rpv_section_text',
		'recent-post-views'
		);
		
	add_settings_field(
		'cb_rpv_select_style', //HTML ID tag
		'Basic style:', //Label
		'cb_rpv_setting_input', //Callback function
		'recent-post-views', //Settings page on which to show the input
		'cb_rpv_main' //Section of the settings page in which to show input.
	);
}

//Define the callback functions (from above)
	//Explains the section
function cb_rpv_section_text () {
	echo '<p>Use this page to style your widgets and shortcode.</p>';
}

	//Display and fill the form field
function cb_rpv_setting_input () {
	//get option 'select_bold' value from the database.
	$options = get_option( 'cb_rpv_options' );
	
	$select_bold = $options['select_bold'];
	//echo the field
	echo "<input id='select_bold' type='checkbox' name='cb_rpv_options[select_bold]' value='bold' ";
		if ($select_bold == 'bold') {echo "checked='checked'>&nbsp;<strong>Make each post bold.</strong></br>";}
		else {echo ">&nbsp;<strong>Make each post bold.</strong></br>";}
	
	$select_italics = $options['select_italics'];
	//echo the field
	echo "<input id='select_italics' type='checkbox' name='cb_rpv_options[select_italics]' value='em' ";
		if ($select_italics == 'em') {echo "checked='checked'>&nbsp;<em>Italicize each post.</em></br>";}
		else {echo ">&nbsp;<em>Italicize each post.</em></br>";}
	
	$select_list_style = $options['select_list_style'];
	//echo the field
	echo "<input id='select_list_style' type='radio' name='cb_rpv_options[select_list_style]' value='ul'";
		if ($select_list_style == 'ul') {echo "checked='checked'>&nbsp;Bulleted";}
		else {echo ">&nbsp;Bulleted";}
	echo "&nbsp;";
	echo "<input id='select_list_style' type='radio' name='cb_rpv_options[select_list_style]' value='ol'";
		if ($select_list_style == 'ol') {echo "checked='checked'>&nbsp;Numbered";}
		else {echo ">&nbsp;Numbered";}
	echo "<br></br>";
	echo "<h4>If you love this plugin, please donate, so I can make it even better.</h4>";
	echo '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SPAZAZLTNNRXG" target="_blank" />
		<input type="hidden" name="hosted_button_id" value="SPAZAZLTNNRXG">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</a>';

}

/*	
//Validation function
function cb_rpv_validate_options (){
	$valid = array();
	$valid['text_string'] = preg_replace(
	'/[^a-zA-Z]/',
	'',
	$input['text_string']
	);
	return $valid;
}
*/
?>