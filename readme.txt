=== Kalin's Post List ===
Contributors: kalinbooks
Tags: post, shortcode, pages, table of contents, related posts, links, automatic
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: trunk

Creates a shortcode or PHP snippet for inserting dynamic, highly customizable lists of posts or pages such as related posts or table of contents into your post content or theme.

== Description ==

<p>
Creates a shortcode or PHP snippet for inserting dynamic, highly customizable lists of posts or pages such as related posts or table of contents into your post content or theme.          
</p>

<p>
Plugin by Kalin Ringkvist at http://kalinbooks.com/
</p>
<p>
Plugin URL: http://kalinbooks.com/post-list-wordpress-plugin/
Post a message if you find any bugs, issues or have a feature request and I will do my best to accommodate.
</p>

== Installation ==

1. Unzip `kalins-post-list.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Post List settings menu to get shortcodes for the default preset configurations or create and save your own configurations.

Note: May require PHP 5.2 and Wordpress 3.0 (hasn't been tested on older versions)

== Screenshots ==

1. The settings menu where you create and adjust the preset configurations which you then refer to by name in the shortcode. Not shown: parent page combobox (shows up when you select page or custom post type)


== Changelog ==

= 0.7 =
* First version. Beta. 

= 1.0 =
*fixed post_excerpt internal shortcode
*added PHP snippet generation

= 1.0.1 =
*Bug fix. Plugin no longer destroys other admin help menus.

= 2.0 =
*added Custom post type support
*”None” post support (you can now use the plugin to simply insert plain HTML. Shortcodes here refer to current page)
*support for listing pages/custom posts based on parent page (including current page)
*post thumbnail/featured image shortcode
*shortcode for link to page/post PDF (requires PDF Creation Station plugin)
*format parameter for total customization of all date/time shortcodes
*length parameter to [post_excerpt] shortcode
*offset parameter for [item_number] shortcode to start count at something other than 1
*shortcodes now show nothing if no results instead of broken/empty list
*fail gracefully when incorrect preset param entered (shows error if admin, nothing if regular user)
*improved handling of HTML-conflicting characters
*strip shortcodes out before showing excerpts

= 2.0.1 =
*Emergency bug fix: plugin no longer throws error when theme does not support post thumbnails.


== Upgrade Notice ==

= 0.7 =
First version. Beta.

= 1.0 =
post_excerpt shortcode should work properly now and anyone familiar with themes or PHP can now insert a simple auto-generated PHP snippet into their theme

= 1.0.1 =
Sorry about all those help menus my plugin was killing before this

= 2.0 =
Lots of new features.


== About ==

If you find this plugin useful please pay it forward to the community, or visit http://kalinbooks.com/ and check out some of my science fiction or political writings.

