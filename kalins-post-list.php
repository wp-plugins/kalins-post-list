<?php
/*
Plugin Name: Kalin's Post List
Version: 1.0
Plugin URI: http://kalinbooks.com/post-list-wordpress-plugin/
Description: Creates a shortcode or PHP snippet for placing highly customizable lists of posts into your post content or your theme.
Author: Kalin Ringkvist
Author URI: http://kalinbooks.com/

------Kalin's Post List WordPress Plugin------------------

Kalin's Post List by Kalin Ringkvist (email: kalin@kalinflash.com)


License:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

define("KALINSPOST_ADMIN_OPTIONS_NAME", "kalinsPost_admin_options");

function kalinsPost_admin_page() {//load php that builds our admin page
	require_once( WP_PLUGIN_DIR . '/kalins-post-list/kalinsPost_admin_page.php');
}

function kalinsPost_admin_init(){
	add_action('contextual_help', 'kalinsPost_contextual_help', 10, 2);
	
	register_deactivation_hook( __FILE__, 'kalinsPost_cleanup' );
	
	/*
	add_action('wp_ajax_kalinsPost_reset_orig', 'kalinsPost_reset_orig');
	add_action('wp_ajax_kalinsPost_reset_my', 'kalinsPost_reset_my');
	add_action('wp_ajax_kalinsPost_save', 'kalinsPost_save');//kalinsPost_savePreset
	*/
	
	add_action('wp_ajax_kalinsPost_save_preset', 'kalinsPost_save_preset');
	add_action('wp_ajax_kalinsPost_delete_preset', 'kalinsPost_delete_preset');//kalinsPost_restore_preset
	add_action('wp_ajax_kalinsPost_restore_preset', 'kalinsPost_restore_preset');
	
	
}

//205

function kalinsPost_save_preset(){	
	check_ajax_referer( "kalinsPost_save_preset" );
	
	$kalinsPostAdminOptions = kalinsPost_get_admin_options();
	
	$outputVar = new stdClass();
	
	$valArr = json_decode($kalinsPostAdminOptions['preset_arr']);
	$preset_name = stripslashes($_POST['preset_name']);
	
	$valObj = array();//$valArr[$preset_name];
	
	$valObj["categories"] = $_POST['categories'];//replace these lines with dynamic loop like in restore_preset()?
	$valObj["tags"] = $_POST['tags'];
	$valObj["post_type"] = $_POST['post_type'];
	$valObj["orderby"] = $_POST['orderby'];
	$valObj["order"] = $_POST['order'];
	$valObj['numberposts'] = $_POST['numberposts'];
	$valObj['before'] = stripslashes($_POST['before']);
	$valObj['content'] = stripslashes($_POST['content']);
	$valObj['after'] = stripslashes($_POST['after']);
	$valObj['excludeCurrent'] = $_POST['excludeCurrent'];
	
	$valObj['includeCats'] = $_POST['includeCats'];
	$valObj['includeTags'] = $_POST['includeTags'];
	
	$valArr->$preset_name = $valObj;
	
	$kalinsPostAdminOptions['preset_arr'] = json_encode($valArr);
	
	$kalinsPostAdminOptions['doCleanup'] = $_POST['doCleanup'];
	
	update_option(KALINSPOST_ADMIN_OPTIONS_NAME, $kalinsPostAdminOptions);//save options to database
	
	$outputVar->status = "success";
	$outputVar->preset_arr = stripslashes($kalinsPostAdminOptions['preset_arr']);
	
	echo $kalinsPostAdminOptions['preset_arr'];
}

function kalinsPost_delete_preset(){
	check_ajax_referer( "kalinsPost_delete_preset" );
	
	$kalinsPostAdminOptions = kalinsPost_get_admin_options();
	
	$outputVar = new stdClass();
	
	$valArr = json_decode($kalinsPostAdminOptions['preset_arr']);
	$preset_name = stripslashes($_POST['preset_name']);
	
	unset($valArr->$preset_name);
	
	$kalinsPostAdminOptions['preset_arr'] = json_encode($valArr);
	
	update_option(KALINSPOST_ADMIN_OPTIONS_NAME, $kalinsPostAdminOptions);//save options to database
	
	echo $kalinsPostAdminOptions['preset_arr'];
}

function kalinsPost_restore_preset(){
	check_ajax_referer( "kalinsPost_restore_preset" );
	
	$kalinsPostAdminOptions = kalinsPost_get_admin_options();
	$defaultAdminOptions = kalinsPost_getAdminSettings();
	
	$outputVar = new stdClass();
	
	$userValArr = json_decode($kalinsPostAdminOptions['preset_arr']);
	$defValArr = json_decode($defaultAdminOptions['preset_arr']);
	
	//echo $defaultAdminOptions['preset_arr'];
	
	//echo $defaultAdminOptions['preset_arr'];
	
	//return;
	
	//echo $defValArr -> fullContent;
	
	foreach ($defValArr as $key => $value){
		$userValArr->$key = $value;
	}
	
	/*
	for($i in $defValArr){
		$userValArr->$i = $defValArr->$i;
	}
	*/
	
	$kalinsPostAdminOptions['preset_arr'] = json_encode($userValArr);
	
	update_option(KALINSPOST_ADMIN_OPTIONS_NAME, $kalinsPostAdminOptions);//save options to database
	
	echo $kalinsPostAdminOptions['preset_arr'];
	
}


function kalinsPost_configure_pages() {
	
	$mypage = add_submenu_page('options-general.php', "Kalin's Post List", "Kalin's Post List", 'manage_options', __FILE__, 'kalinsPost_admin_page');
	
	//$mytool = add_submenu_page('tools.php', 'Kalins PDF Creation Station', 'PDF Creation Station', 'manage_options', __FILE__, 'kalinsPost_tool_page');
	
	add_action( "admin_print_scripts-$mypage", 'kalinsPost_admin_head' );
	//add_action('admin_print_styles-' . $mypage, 'kalinsPost_admin_styles');
	
	//add_action( "admin_print_scripts-$mytool", 'kalinsPost_admin_head' );
	//add_action('admin_print_styles-' . $mytool, 'kalinsPost_admin_styles');
}

function kalinsPost_admin_head() {
	//echo "My plugin admin head";
	wp_enqueue_script("jquery");
	//wp_enqueue_script("jquery-ui-sortable");
	//wp_enqueue_script("jquery-ui-dialog");
}

/*function kalinsPost_admin_styles(){//not sure why this didn't work if called from pdf_admin_head
	wp_enqueue_style('kalinPDFStyle');
}*/

function kalinsPost_inner_custom_box($post) {//creates the box that goes on the post/page edit page
	require_once( WP_PLUGIN_DIR . '/kalins-edit-links/kalinsPost_custom_box.php');
}


function kalinsPost_contextual_help($text, $screen) {
	if (strcmp($screen, 'settings_page_kalins-post-list/kalins-post-list') == 0 ) {//if we're on settings page, add setting help and return
		require_once( WP_PLUGIN_DIR . '/kalins-post-list/kalins_post_admin_help.php');
		return;
	}
}

function kalinsPost_get_admin_options() {
	$kalinsPostAdminOptions = kalinsPost_getAdminSettings();
	
	$devOptions = get_option(KALINSPOST_ADMIN_OPTIONS_NAME);

	if (!empty($devOptions)) {
		foreach ($devOptions as $key => $option){
			$kalinsPostAdminOptions[$key] = $option;
		}
	}

	update_option(KALINSPOST_ADMIN_OPTIONS_NAME, $kalinsPostAdminOptions);

	return $kalinsPostAdminOptions;
}

function kalinsPost_getAdminSettings(){//simply returns all our default option values
	
	$kalinsPostAdminOptions = array();
	
	
	$kalinsPostAdminOptions['preset_arr'] = '{"pageContentDivided_5":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"5","before":"<p><hr\/>","content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_content]<hr\/>","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"postExcerptDivided_5":{"categories":"","tags":"","post_type":"post","orderby":"post_date","order":"DESC","numberposts":"5","before":"<p><hr\/>","content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_excerpt]<hr\/>","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"simpleAttachmentList_10":{"categories":"","tags":"","post_type":"attachment","orderby":"post_date","order":"DESC","numberposts":"10","before":"<ul>","content":"<li><a href=\"[post_permalink]\">[post_title]<\/a><\/li>","after":"<\/ul>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"images_5":{"categories":"","tags":"","post_type":"attachment","orderby":"post_date","order":"DESC","numberposts":"5","before":"<hr \/>","content":"<p><a href=\"[post_permalink]\"><img src=\"[guid]\" \/><\/a><\/p>","after":"<hr \/>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"pageDropdown_100":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"100","before":"<p><select id=\"postList_dropdown\" style=\"width:200px; margin-right:20px\">","content":"<option value=\"[post_permalink]\">[post_title]<\/option>","after":"<\/ select> <input type=\"button\" id=\"postList_goBtn\" value=\"GO!\" onClick=\"javascript:window.location=document.getElementById(\'postList_dropdown\').value\" \/><\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"simplePostList_5":{"categories":"","tags":"","post_type":"post","orderby":"date","order":"DESC","numberposts":"5","before":"<p>","content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"footerPageList_10":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"10","before":"<p align=\"center\">","content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end] | ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"everythingNumbered_200":{"categories":"","tags":"","post_type":"any","orderby":"date","order":"ASC","numberposts":"200","before":"<p>All my pages and posts (roll over for titles):<br\/>","content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[item_number]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"false","includeCats":"false","includeTags":"false"},"everythingID_200":{"categories":"","tags":"","post_type":"any","orderby":"date","order":"ASC","numberposts":"200","before":"<p>All my pages and posts (roll over for titles):<br\/>","content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[ID]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"false","includeCats":"false","includeTags":"false"},"relatedPosts_5":{"categories":"","tags":"","post_type":"post","orderby":"rand","order":"DESC","numberposts":"5","before":"<p>Related posts: ","content":"<a href=\"[post_permalink]\" title=\"[post_excerpt]\">[post_title]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"true"}}';
	$kalinsPostAdminOptions['default_preset'] = '';
	$kalinsPostAdminOptions['doCleanup'] = 'true';
	//$kalinsPostAdminOptions['doCleanup'] = "true";
	
	return $kalinsPostAdminOptions;
}

function kalinsPost_cleanup() {//deactivation hook. Clear all traces of PDF Creation Station
	
	$adminOptions = kalinsPost_get_admin_options();
	if($adminOptions['doCleanup'] == 'true'){//if user set cleanup to true, remove all options and post meta data
		delete_option(KALINSPOST_ADMIN_OPTIONS_NAME);//remove all options for admin
	}
}

function kalinsPost_init(){
	//setup internationalization here
	//this doesn't actually run and perhaps there's another better place to do internationalization
}

function kalinsPostinternalShortcodeReplace($str, $page, $count){
	$SCList =  array("[ID]", "[post_date]", "[post_date_gmt]", "[post_title]", "[post_name]", "[post_modified]", "[post_modified_gmt]", "[guid]", "[comment_count]", "[post_content]");
	
	$l = count($SCList);
	for($i = 0; $i<$l; $i++){//loop through all possible shortcodes
		$scName = substr($SCList[$i], 1, count($SCList[$i]) - 2);
		$str = str_replace($SCList[$i], $page->$scName, $str);
	}
	
	$str = str_replace("[post_author]", get_userdata($page->post_author)->user_login, $str);//post_author requires an extra function call to convert the userID into a name so we can't do it in the loop above
	$str = str_replace("[post_permalink]", get_permalink( $page->ID ), $str);
	$str = str_replace("[item_number]", $count, $str);
	
	
	//not sure why wp_trim_excerpt(); doesn't work anymore
	if(strpos($str, "[post_excerpt]")){
		if($page->post_excerpt == ""){//if there's no excerpt applied to the post, extract one
			$pageContent = strip_tags($page->post_content);
			if(strlen($pageContent) <= 250) {
				$str = str_replace("[post_excerpt]", $pageContent, $str);
			}else{
				
				$str = str_replace("[post_excerpt]", substr($pageContent, 0, 250) ."...", $str);
			}
		}else{
			$str = str_replace("[post_excerpt]", $page->post_excerpt, $str);
		}
	}
	
	
	return $str;
}

function kalinsPost_shortcode($atts){
	return kalinsPost_execute($atts['preset']);
}

function kalinsPost_show($preset){
	echo kalinsPost_execute($preset);
}

function kalinsPost_execute($preset) {
	
	$adminOptions = kalinsPost_get_admin_options();
	$presetObj = json_decode($adminOptions['preset_arr']);
	$newVals = $presetObj->$preset;
	$excludeList = "";
	
	global $post;
	
	if($newVals->excludeCurrent == "true"){
		$excludeList = $post->ID;
	}else{	
	}
	
	$catString = $newVals->categories;
	if($newVals->includeCats == "true"){
		$post_categories = wp_get_post_categories($post->ID);
		foreach($post_categories as $c){
			$catString = $catString .$c .",";
		}
	}
	
	$tagString = $newVals->tags;
	if($newVals->includeTags == "true"){
		$post_tags = wp_get_post_tags( $post->ID);
		foreach($post_tags as $c){
			$tagString = $tagString .$c->slug .",";
		}
	}
	
	$posts = get_posts('numberposts=' .$newVals->numberposts .'&category=' .$catString .'&post_type=' .$newVals->post_type .'&tag=' .$tagString .'&orderby=' .$newVals->orderby .'&order=' .$newVals->order .'&exclude=' .$excludeList);
	
	$output = stripslashes($newVals->before);
	
	$count = 1;
	
	foreach ($posts as $page) {
		$output = $output .kalinsPostinternalShortcodeReplace($newVals->content, $page, $count);
		
		$count = $count + 1;
	}
	
	$finalPos = strrpos ($output , "[final_end]");
	if($finalPos > 0){//if ending exists (the last item where we don't want to add any more commas or ending brackets or whatever)
		$output = substr($output, 0, $finalPos);//cut everything off at the final position of {final_end}
		$output = str_replace("[final_end]", "", $output);//replace all the other instances of {final_end}, since we only care about the last one
	}
	
	$output = $output .$newVals->after;
	return $output;
}

add_shortcode('post_list', 'kalinsPost_shortcode');





//wp actions to get everything started
add_action('admin_init', 'kalinsPost_admin_init');
add_action('admin_menu', 'kalinsPost_configure_pages');
//add_action( 'init', 'kalinsPost_init' );//just keep this for whenever we do internationalization - if the function is actually needed, that is.


//content filter is called whenever a blog page is displayed. Comment this out if you aren't using links applied directly to individual posts, or if the link is set in your theme
//add_filter("the_content", "kalinsPost_content_filter" );

?>