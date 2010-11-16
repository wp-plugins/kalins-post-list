<?php

	if ( !function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
		exit;
	}
	
	$catList = get_categories('hide_empty=0');
	$tagList = get_tags('hide_empty=0');
	
	$save_preset_nonce = wp_create_nonce( 'kalinsPost_save_preset' );
	$delete_preset_nonce = wp_create_nonce('kalinsPost_delete_preset');
	$restore_preset_nonce = wp_create_nonce('kalinsPost_restore_preset');
	
	$adminOptions = kalinsPost_get_admin_options();
	
	//echo $adminOptions["preset_arr"];
?>


<script language="javascript" type='text/javascript'>

	jQuery(document).ready(function($){
		
		var savePresetNonce = '<?php echo $save_preset_nonce; ?>';
		var deletePresetNonce = '<?php echo $delete_preset_nonce; ?>';
		var restorePresetNonce = '<?php echo $restore_preset_nonce; ?>';
		
		var catList = <?php echo json_encode($catList); ?>;
		var tagList = <?php echo json_encode($tagList); ?>;
	
		var presetArr = <?php echo $adminOptions["preset_arr"]; ?>;
		
		function getCatString(){
			var catString = '';
			var pageCount = 0;
			var l = catList.length;	
			for(var i=0; i<l; i++){
				if($('#chkCat' + catList[i]['term_id']).is(':checked')){
					catString += catList[i]['term_id'] + ",";
					pageCount++;
				}
			}
			return catString;
		}
		
		function getTagString(){
			var tagString = '';
			var pageCount = 0;
			var l = tagList.length;
			for(var i=0; i<l; i++){
				if($('#chkTag' + tagList[i]['slug']).is(':checked')){
					tagString += tagList[i]['slug'] + ",";
					pageCount++;
				}
			}
			return tagString;
		}
		
		function setCatValues(str){
					
			var l = catList.length;
			for(var i=0; i<l; i++){
				$('#chkCat' + catList[i]['term_id']).attr('checked', false);
			}

			var arrCats = str.split(",");
			var l = arrCats.length;
			for(var i=0; i<l; i++){
				$('#chkCat' + arrCats[i]).attr('checked', true);
			}
		}
		
		function setTagValues(str){
			var l = tagList.length;		   
			for(var i=0; i<l; i++){
				$('#chkTag' + tagList[i]['slug']).attr('checked', false);
			}
			
			var arrCats = str.split(",");
			var l = arrCats.length;
			for(var i=0; i<l; i++){
				$('#chkTag' + arrCats[i]).attr('checked', true);
			}
		}
		
		function deletePreset(id){
			//alert("deleting: " + id);
			
			var data = { action: 'kalinsPost_delete_preset',
				_ajax_nonce : deletePresetNonce
			}
			
			data.preset_name = id;
			
			$('#createStatus').html("Deleting preset...");
	
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				//alert(response);
												
				var startPosition = response.indexOf("{");
				var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
				
				var newFileData = JSON.parse(responseObjString);
				
				/*if(newFileData.status == "success"){
					$('#createStatus').html("Preset deleted successfully.");
				}else{
					$('#createStatus').html(response);
				}*/
				
				presetArr = newFileData;//.preset_arr;
				
				buildPresetTable();
				
				$('#createStatus').html("Preset deleted successfully.");
				
			});
		}
		
		function loadPreset(id){
			
			var newValues = presetArr[id];
			
			setCatValues(newValues["categories"]);
			setTagValues(newValues["tags"]);
			
			$('#txtNumberposts').val(newValues["numberposts"]);
			$('#txtBeforeList').val(newValues["before"]);
			$('#txtContent').val(newValues["content"]);
			$('#txtAfterList').val(newValues["after"]);
				
			$('#cboPost_type option[value=' + newValues["post_type"] + ']').attr('selected','selected'); 
			$('#cboOrderby option[value=' + newValues["orderby"] + ']').attr('selected','selected');
			$('#cboOrder option[value=' + newValues["order"] + ']').attr('selected','selected');
			
			if(newValues["excludeCurrent"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
				$('#chkExcludeCurrent').attr('checked', true);
			}else{
				$('#chkExcludeCurrent').attr('checked', false);
			}
			
			if(newValues["includeCats"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
				$('#chkIncludeCats').attr('checked', true);
			}else{
				$('#chkIncludeCats').attr('checked', false);
			}
			
			if(newValues["includeTags"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
				$('#chkIncludeTags').attr('checked', true);
			}else{
				$('#chkIncludeTags').attr('checked', false);
			}
			
			$('#txtPresetName').val(id);
		}
		
		function buildPresetTable(){//build the file table - we build it all in javascript so we can simply rebuild it whenever an entry is added through ajax

			function tc(str){
				return "<td style='border:solid 1px' align='center'>" + str + "</td>";
			}
			
			var tableHTML = "<table style='border:solid 1px' width='725' border='1' cellspacing='1' cellpadding='3'><tr><th scope='col'>#</th><th scope='col'>Preset Name</th><th scope='col'>Load</th><th scope='col'>Delete</th><th scope='col'>Shortcode</th></tr>";

			var count = 0;
			for(i in presetArr){
				var shortcode = '[post_list preset="' + i + '"]';
				tableHTML += "<tr>" + tc(count) + tc(i) + tc("<button name='btnLoad_" + count + "' id='btnLoad_" + count + "'>Load</button>") + tc("<button name='btnDelete_" + count + "' id='btnDelete_" + count + "'>Delete</button>") + tc(shortcode) + "</tr>";
				count++;
			}
			
			tableHTML += "</table>";
			
			$('#presetListDiv').html(tableHTML);
			
			count = 0;
			for(j in presetArr){
				
				$('#btnDelete_' + count).attr('presetname', j);
				
				$('#btnDelete_' + count).click(function(){
					if(confirm("Are you sure you want to delete " + $(this).attr('presetname') + "?")){							
						deletePreset($(this).attr('presetname'));
					}
				});
				
				$('#btnLoad_' + count).attr('presetname', j);
				
				$('#btnLoad_' + count).click(function(){				
					loadPreset($(this).attr('presetname'));
				});
				
				count++;
			}	
		}
		
		$('#btnSavePreset').click(function(){
			//alert(data.post_type);
			var data = { action: 'kalinsPost_save_preset',
				_ajax_nonce : savePresetNonce
			}
			
			data.preset_name = $("#txtPresetName").val();
			
			
							   
			if(presetArr[data.preset_name]){	//presetArr[data.preset_name]						   
				if(!confirm("Are you sure you want to overwrite the preset" + data.preset_name)){
					$('#createStatus').html("<br/>");
					return;
				}
			}
			
			
			data.categories = getCatString();
			data.tags = getTagString();
			data.post_type = $("#cboPost_type").val();
			
			data.numberposts = $("#txtNumberposts").val();
			data.before = $("#txtBeforeList").val();
			data.content = $("#txtContent").val();
			data.after = $("#txtAfterList").val();
			
			data.orderby = $("#cboOrderby").val();
			data.order = $("#cboOrder").val();
			
			data.excludeCurrent = $("#chkExcludeCurrent").is(':checked');
			
			data.includeCats = $("#chkIncludeCats").is(':checked');
			data.includeTags = $("#chkIncludeTags").is(':checked');
			
			data.doCleanup = $("#chkDoCleanup").is(':checked');
			
			
			
			
			
			$('#createStatus').html("Saving to preset...");
	
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
												
				//$('#createStatus').html(response);
												
				var startPosition = response.indexOf("{");
				var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
				
				//alert(responseObjString);
				var newFileData = JSON.parse(responseObjString);
				
				presetArr = newFileData;//.preset_arr;
				buildPresetTable();
				$('#createStatus').html("Preset successfully added.");
			});
		});
		
		//btnRestorePreset
		
		$('#btnRestorePreset').click(function(){
			//alert(data.post_type);
			var data = { action: 'kalinsPost_restore_preset',
				_ajax_nonce : restorePresetNonce
			}
			
			if(confirm("Are you sure you want to restore all default presets? This will remove any changes you've made to the default presets, but will not delete your custom presets.")){
				
				$('#createStatus').html("Restoring presets...");
		
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
													
					//$('#createStatus').html(response);
													
					var startPosition = response.indexOf("{");
					var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
					
					//alert(responseObjString);
					var newFileData = JSON.parse(responseObjString);
					
					presetArr = newFileData;//.preset_arr;
					buildPresetTable();
					//$('#createStatus').html("Preset successfully added.");
					
					$('#createStatus').html("Presets successfully reset.");
					
				});
			}
		});
		
		//alert(catString);
		
		//setCatValues(catString);
		//setTagValues(tagString);
		buildPresetTable();
		//alert("fourth");
		
		$('#outputSpan').hide();
		
	});
	
</script>


<style type="text/css">
	.txtHeader{
		width:610px;
		position:absolute;
		left:290px;
	}
</style>



<!--

<h2>Kalin's Post List - settings</h2>

<h3>by Kalin Ringkvist - <a href="http://kalinbooks.com/">KalinBooks.com</a></h3>

<p><a href="http://kalinbooks.com/post-list-wordpress-plugin/">Plugin Page</a></p>

-->

<br/><hr/><br/>

<p>Post type:
<select id="cboPost_type" style="width:100px;" id="cboType">
<option value="post">post</option>
<option value="page">page</option>
<option value="attachment">attachment</option>
<option value="any">all</option>
</select>

&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;

Show count: <input type="text" size='5' name='txtNumberposts' id='txtNumberposts' value='' ></input>

&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;

Order by: <select id="cboOrderby" style="width:110px;" id="cboType">
<option value="post_date">post date</option>
<option value="author">author ID</option>
<option value="category">category ID</option>
<option value="content">content</option>
<option value="date">date</option>
<option value="ID">post ID</option>
<option value="menu_order">menu_order</option>
<option value="mime_type">mime_type</option>
<option value="modified">modified date</option>
<option value="name">name</option>
<option value="parent">parent</option>
<option value="password">password</option>
<option value="rand">random</option>
<option value="status">status</option>
<option value="title">title</option>
<option value="type">type</option>
</select>

<select id="cboOrder" style="width:110px;" id="cboType">
<option value="DESC">descending</option>
<option value="ASC">ascending</option>
</select>

</p>

<div style="overflow:scroll; overflow-x:hidden; height:150px; width:239px; float:left; border:ridge; margin-right:20px; padding-left:10px;">
<h3 align="center">Categories</h3>

<input type=checkbox id="chkIncludeCats" name="chkIncludeCats" ></ input> Include current post categories<hr />

<?php
	$l = count($catList);
	for($i=0; $i<$l; $i++){//build our list of cats
		$pageID = $catList[$i]->term_id;
		echo('<input type=checkbox id="chkCat' .$pageID .'" name="chkCat' .$pageID .'" ></ input> ' .$catList[$i]->name .'<br />');
	}
?>

</div>

<div style="overflow:scroll; overflow-x:hidden; height:150px; width:239px; border:ridge; padding-left:10px; float:left; margin-right:20px">
<h3 align='center'>Tags</h3>
<input type=checkbox id="chkIncludeTags" name="chkIncludeTags" ></ input> Include current post tags<hr />
<?php
	$l = count($tagList);
	for($i=0; $i<$l; $i++){//build our list of cats
		$pageID = $tagList[$i]->slug;//so retarded that categories run off IDs and tags run off slugs
		echo('<input type=checkbox id="chkTag' .$pageID .'" name="chkTag' .$pageID .'" ></ input> ' .$tagList[$i]->name .'<br />');
	}
?>
</div>

<div style="overflow:auto; height:150px; width:160px; border:ridge; padding-left:10px;">

<p>Nothing selected means show everything, including tags or categories not yet created. Checking all will include everything, but will exclude all future categories or tags.</p>

</div>
<br/><br/>

<p>Before list HTML: <textarea rows='3' cols='200' name='txtBeforeList' id='txtBeforeList' value=''  class="txtHeader"></textarea></p><br/><br/><br/>

<p>List item content: 

<textarea name='txtContent' id='txtContent' rows='3' cols="200" class="txtHeader"></textarea>

</p>

<br/><br/><br/>

<p>After list HTML: <textarea rows='3' cols='200' name='txtAfterList' id='txtAfterList'  class="txtHeader"></textarea></p>

<br/><br/><br/>

<input type=checkbox id="chkExcludeCurrent" name="chkExcludeCurrent" checked="yes"></ input> Exclude current post from results

<br/><br/>

<p>
<button id="btnSavePreset">Save to Preset</button>&nbsp;&nbsp;:&nbsp;&nbsp;<input type="text" size='30' name='txtPresetName' id='txtPresetName' value='<?php echo $adminOptions["default_preset"]; ?>' ></input>
</p>

<p><span id="createStatus">&nbsp;</span></p>

<p>
<span id="outputSpan">
<textarea name='txtOutput' id='txtOutput' rows='6' cols="86">output goes here</textarea>
</span>
</p>

<p>
<div id="presetListDiv" class="wtf">
</div>
</p>

<p><button id="btnRestorePreset">Restore Preset Defaults</button></p>

<br/><hr/><br/>
		<p>
        <b>Shortcodes:</b> Use these codes inside the list item content (will throw errors if placed in before or after HTML fields)<br />
        </p>
        <p>
        <ul>
        <li><b>[ID]</b> - the ID number of the page/post</li>
        <li><b>[post_author]</b> - author of the page/post</li>
        <li><b>[post_permalink]</b> - the page permalink</li>
        <li><b>[post_date]</b> - date page/post was created</li>
        <li><b>[post_date_gmt]</b> - date page/post was created in gmt time</li>
        <li><b>[post_title]</b> - page/post title</li>
        <li><b>[post_excerpt]</b> - page/post excerpt</li>
        <li><b>[post_name]</b> - page/post slug name</li>
        <li><b>[post_modified]</b> - date page/post was last modified</li>
        <li><b>[post_modified_gmt]</b> - date page/post was last modified in gmt time</li>
        <li><b>[guid]</b> - url of the page/post (usually post_permalink is better)</li>
        <li><b>[comment_count]</b> - number of comments posted for this post/page</li>
        <li><b>[item_number]</b> - the list index for each page/post (starts at 1)</li>
        <li><b>[final_end]</b> - on the final list item, everything after this shortcode will be excluded. This will allow you to have commas (or anything else) after each item except the last one.</li>
        </ul></p>
        <p>Note: these shortcodes only work in the List item content box on this page.</p>

<br/><hr/><br/>
    
<p>Thank you for using Kalin's Post List</p>

<?php 
$versionNum = (int) substr(phpversion(), 0, 1);//check php version and possibly warn user
if($versionNum < 5){//I have no idea what this thing will do at anything below 5.2.11 :)
    echo "<p>You are running PHP version "  .phpversion() .". This plugin was built with PHP version 5.2.11 and has NOT been tested with older versions.</p>";
}
?>
 
 <p><input type='checkbox' id='chkDoCleanup' name='chkDoCleanup' <?php if($adminOptions["doCleanup"] == "true"){echo "checked='yes' ";} ?>></input> Upon plugin deactivation clean up all database entries. (Save any preset to change this value)</p>
 
<p>Kalin's Easy Edit Links was built with WordPress version 3.0. It has NOT been tested on older versions and might fail.</p>
<p>You may also like <a href="http://kalinbooks.com/pdf-creation-station/">Kalin's PDF Creation Station WordPress Plugin</a> - <br /> Create highly customizable PDF documents from any combination of pages and posts, or add a link to generate a PDF on each individual page or post.</p>


</html>