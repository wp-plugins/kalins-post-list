<p>  
	<b>Kalin's Post List</b> allows you to create dynamic, highly customizable lists of posts and pages that can be inserted into pages and posts through a shortcode.</p>
<p>To use one of the pre-defined Post List configurations, simply look at the table halfway down the page and copy one of the shortcodes into any page or post. When you view the post, the shortcode should be replaced with a list of posts that corresponds to the preset. You can then come back to this settings page and customize the preset, or save it as a new configuration under a different name. Most importantly will probably be setting the proper categories and/or tags from which to pull the post list.</p>
<ul>
  <li><strong>Top Row Options</strong><br />
    <ol>
    <li><strong>Post Type:</strong> Choose to show pages, posts, attachments or everything.</li>
    <li><strong>Show Count: </strong>The number of items that will be shown. Be sure to only insert integer numbers here. Use -1 (negative one) for no limit.</li>
    <li><strong>Order by:</strong> Choose from 16 different ways to order the posts,  then select ascending or descending. These will often affect which posts are actually shown, as well as their order.</li>
    </ol>
  </li>
  <br/>
  <li><strong>Categories and Tags</strong><br />
  <ol>
    <li><strong>Include current post categories/tags:</strong> Checking this for either categories or tags will extract the terms from the current page/post where you have applied the shortcode, and use them to determine which posts to pull into the list. This is how you can show &quot;related posts&quot;.</li>
    <li><strong>Category/Tags: </strong>Your own categories and tags should show up in these two lists, allowing you to select any combination. If you selected &quot;include current&quot;, these will be added to the list.</li>
    </ol>
  </li><br/>
  <li><strong>Content</strong><br />
  <ol>
    <li><strong>Before list HTML:</strong> Insert any HTML to be displayed immediately before the actual post list. (Shortcodes don't work here since there's no post for them to refer to.)</li>
    <li><strong>List item content: </strong>This is the important one. This HTML represents the individual items within the list and will be repeated multiple times based on the &quot;show count&quot; and the number of results returned. You must use the shortcodes listed further down on the page to insert information about each post. Most common, of course would be [post_title] and [post_permalink] shortcodes, but you'll see that there are many others, allowing you to customize the information displayed in the list. For examples on how these are used, click &quot;load&quot; on any preset in the preset table below.</li>
    <li><strong>After list HTML: </strong>HTML content to insert after the list. (Shortcodes don't work here either.)</li>
    <li><strong>Exclude current post from results:</strong> Check this to exclude the page/post you are actually on from the list, since there's usually no reason to link a page to itself.</li>
    </ol>
  </li>
  <br/>
  <li><strong>Presets:</strong><br />
  <ol>
  <li><strong>Save to Preset:</strong> You can save a preset to any name you  like, though avoid special characters. Once you save, the preset should immediately appear in the list below. You can save over the default presets, though you may be better off saving them as unique names to avoid confusion later. 
  </li>
    <li>
  <strong>Preset List Table:</strong> The preset list table should show all of the presets you've entered as well as any remaining defaults that shipped with the plugin. You should see load and delete buttons for each. Clicking load should populate all the above fields to allow you to make adjustments and re-save. Delete will delete that preset. The right-most cell contains the shortcode itself for easy copy/pasting into your post. 
  </li>
  <li>
  <strong>Restore Preset Defaults:</strong> To restore your original presets, click "Restore Preset Defaults" under the preset list. This will restore your original presets, overwriting any changes you have made to them. This will not, however, delete any new presets you've created using unique names.
  </li>
  <li>
  <strong>PHP code:</strong> For folks with a working knowledge of PHP or themes. When you click load on any preset, it will generate a simple PHP snippet that you can insert anywhere into your theme. Don't forget the opening and closing PHP tags.
  </li>
  </ol>
  </li>
  <br/>
  <li><strong>Shortcodes:</strong><br />
  This is a reference list of the shortcodes that can be used within the "List item content" inut field. They will refer to each respective post in the generated list. They will not function anywhere  except the &quot;List item content&quot; field. 
  </li><br/>
  <li><strong>Plugin deactivation:</strong><br />
  At the bottom of the page, you can select whether or not to save all your values when deactivating this plugin. If you have lots of custom presets that you don't want to lose, you may want to turn this off. Otherwise the default is to clean up everything upon plugin deactivation.
  </li>
</ul>
<p>
  </p>
</p>