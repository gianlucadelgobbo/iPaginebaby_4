<?php
class EncodeExplorerFiles
{
	var $location;
	var $dirs;
	var $files;
	var $sort_by;
	var $sort_as;
	var $mobile;
	var $logging;
	var $spaceUsed;
	var $lang;
	
	//
	// Determine sorting, calculate space.
	// 
	function init()
	{
		$this->sort_by = "";
		$this->sort_as = "";
		if(isset($_GET["sort_by"]) && isset($_GET["sort_as"]))
		{
			if($_GET["sort_by"] == "name" || $_GET["sort_by"] == "size" || $_GET["sort_by"] == "mod")
				if($_GET["sort_as"] == "asc" || $_GET["sort_as"] == "desc")
				{
					$this->sort_by = $_GET["sort_by"];
					$this->sort_as = $_GET["sort_as"];
				}
		}
		if(strlen($this->sort_by) <= 0 || strlen($this->sort_as) <= 0)
		{
			$this->sort_by = "name";
			$this->sort_as = "desc";
		}
		
		
		global $_TRANSLATIONS;
		if(isset($_GET['lang']) && isset($_TRANSLATIONS[$_GET['lang']]))
			$this->lang = $_GET['lang'];
		else
			$this->lang = EncodeExplorerFiles::getConfig("lang");
		
		$this->mobile = false;
		if(EncodeExplorerFiles::getConfig("mobile_enabled") == true)
		{
			if((EncodeExplorerFiles::getConfig("mobile_default") == true || isset($_GET['m'])) && !isset($_GET['s']))
				$this->mobile = true;
		}
		
		$this->logging = false;
		if(EncodeExplorerFiles::getConfig("log_file") != null && strlen(EncodeExplorerFiles::getConfig("log_file")) > 0)
			$this->logging = true;
	}

	//
	// Read the file list from the directory
	// 
	function readDir()
	{
		global $EncodeExplorerFiles;
		//
		// Reading the data of files and directories
		//
		if($open_dir = @opendir($this->location->getFullPath()))
		{
			$this->dirs = array();
			$this->files = array();
			while ($object = readdir($open_dir))
			{
				if($object != "." && $object != "..") 
				{
					if(is_dir($this->location->getDir(true, false, false, 0)."/".$object))
					{
						if(!in_array($object, EncodeExplorerFiles::getConfig('hidden_dirs')))
							$this->dirs[] = new Dir($object, $this->location);
					}
					else if(!in_array($object, EncodeExplorerFiles::getConfig('hidden_files')))
						$this->files[] = new File($object, $this->location);
				}
			}
			closedir($open_dir);
		}
		else
		{
			$EncodeExplorerFiles->setErrorString("unable_to_read_dir");;
		}
	}

	//
	// A recursive function for calculating the total used space
	// 
	function sum_dir($start_dir, $ignore_files, $levels = 1) 
	{
		if ($dir = opendir($start_dir)) 
		{
			$total = 0;
			while ((($file = readdir($dir)) !== false)) 
			{
				if (!in_array($file, $ignore_files)) 
				{
					if ((is_dir($start_dir . '/' . $file)) && ($levels - 1 >= 0)) 
					{
						$total += $this->sum_dir($start_dir . '/' . $file, $ignore_files, $levels-1);
					}
					elseif (is_file($start_dir . '/' . $file)) 
					{					
						$total += File::getFileSize($start_dir . '/' . $file) / 1024;
					}
				}
			}
			
			closedir($dir);
			return $total;
		}
	}

	function calculateSpace()
	{
		if(EncodeExplorerFiles::getConfig('calculate_space_level') <= 0)
			return;
		$ignore_files = array('..', '.');
		$start_dir = getcwd();
		$spaceUsed = $this->sum_dir($start_dir, $ignore_files, EncodeExplorerFiles::getConfig('calculate_space_level'));
		$this->spaceUsed = round($spaceUsed/1024, 3);
	}

	function sort()
	{
		if(is_array($this->files)){
			usort($this->files, "EncodeExplorerFiles::cmp_".$this->sort_by);
			if($this->sort_as == "desc")
				$this->files = array_reverse($this->files);
		}
		
		if(is_array($this->dirs)){
			usort($this->dirs, "EncodeExplorerFiles::cmp_name");
			if($this->sort_by == "name" && $this->sort_as == "desc")
				$this->dirs = array_reverse($this->dirs);
		}
	}

	function makeArrow($sort_by)
	{	
		if($this->sort_by == $sort_by && $this->sort_as == "asc")
		{
			$sort_as = "desc";
			$img = "arrow_up";
		}
		else
		{
			$sort_as = "asc";
			$img = "arrow_down";
		}

		if($sort_by == "name")
			$text = $this->getString("file_name");
		else if($sort_by == "size")
			$text = $this->getString("size");
		else if($sort_by == "mod")
			$text = $this->getString("last_changed");

		return "<a href=\"".$this->makeLink(false, false, $sort_by, $sort_as, null, $this->location->getDir(false, true, false, 0))."\">
			$text <img style=\"border:0;\" alt=\"".$sort_as."\" src=\"../icons.php?img=".$img."\" /></a>";
	}
	
	function makeLink($switchVersion, $logout, $sort_by, $sort_as, $delete, $dir)
	{
		$link = "?";
		if($switchVersion == true && EncodeExplorerFiles::getConfig("mobile_enabled") == true)
		{
			if($this->mobile == false)
				$link .= "m&amp;";
			else
				$link .= "s&amp;";
		}
		else if($this->mobile == true && EncodeExplorerFiles::getConfig("mobile_enabled") == true && EncodeExplorerFiles::getConfig("mobile_default") == false)
			$link .= "m&amp;";
		else if($this->mobile == false && EncodeExplorerFiles::getConfig("mobile_enabled") == true && EncodeExplorerFiles::getConfig("mobile_default") == true)
			$link .= "s&amp;";
			
		if($logout == true)
		{
			$link .= "logout";
			return $link;
		}
			
		if(isset($this->lang) && $this->lang != EncodeExplorerFiles::getConfig("lang"))
			$link .= "lang=".$this->lang."&amp;";
			
		if($sort_by != null && strlen($sort_by) > 0)
			$link .= "sort_by=".$sort_by."&amp;";
			
		if($sort_as != null && strlen($sort_as) > 0)
			$link .= "sort_as=".$sort_as."&amp;";
		
		$link .= "dir=".$dir;
		if($delete != null)
			$link .= "&amp;del=".$delete;
		return $link;
	}

	function makeIcon($l)
	{
		$l = strtolower($l);
		return "../icons.php?img=".$l;
	}

	function formatModTime($time)
	{
		$timeformat = "d.m.y H:i:s";
		if(EncodeExplorerFiles::getConfig("time_format") != null && strlen(EncodeExplorerFiles::getConfig("time_format")) > 0)
			$timeformat = EncodeExplorerFiles::getConfig("time_format");
		return date($timeformat, $time);
	}

	function formatSize($size) 
	{
		$sizes = Array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		$y = $sizes[0];
		for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++) 
		{
			$size = $size / 1024;
			$y  = $sizes[$i];
		}
		return round($size, 2)." ".$y;
	}

	//
	// Debugging output
	// 
	function debug()
	{
		print("Explorer location: ".$this->location->getDir(true, false, false, 0)."\n");
		for($i = 0; $i < count($this->dirs); $i++)
			$this->dirs[$i]->output();
		for($i = 0; $i < count($this->files); $i++)
			$this->files[$i]->output();
	}
	
	//
	// Comparison functions for sorting.
	//
	
	public static function cmp_name($b, $a)
	{
		return strcasecmp($a->name, $b->name);
	}
	
	public static function cmp_size($a, $b)
	{
		return ($a->size - $b->size);
	}
	
	public static function cmp_mod($b, $a)
	{
		return ($a->modTime - $b->modTime);
	}
	
	//
	// The function for getting a translated string.
	// Falls back to english if the correct language is missing something.
	//
	public static function getLangString($stringName, $lang)
	{
		global $_TRANSLATIONS;
		if(isset($_TRANSLATIONS[$lang]) && is_array($_TRANSLATIONS[$lang]) 
			&& isset($_TRANSLATIONS[$lang][$stringName]))
			return $_TRANSLATIONS[$lang][$stringName];
		else if(isset($_TRANSLATIONS["en"]))// && is_array($_TRANSLATIONS["en"]) 
			//&& isset($_TRANSLATIONS["en"][$stringName]))
			return $_TRANSLATIONS["en"][$stringName];
		else
			return "Translation error";
	}
	
	function getString($stringName)
	{
		return EncodeExplorerFiles::getLangString($stringName, $this->lang);
	}
	
	//
	// The function for getting configuration values
	//
	public static function getConfig($name)
	{
		global $_CONFIG;
		if(isset($_CONFIG) && isset($_CONFIG[$name]))
			return $_CONFIG[$name];
		return null;
	}
	
	public static function setError($message)
	{
		global $_ERROR;
		if(isset($_ERROR) && strlen($_ERROR) > 0)
			;// keep the first error and discard the rest
		else
			$_ERROR = $message;
	}
	
	function setErrorString($stringName)
	{
		EncodeExplorerFiles::setError($this->getString($stringName));
	}

	//
	// Main function, activating tasks
	// 
	function run($location)
	{
		$this->location = $location;
		$this->calculateSpace();
		$this->readDir();
		$this->sort();
		$this->outputHtml();
	}
	
	public function printLoginBox()
	{
		?>
		<div id="login">
		<form enctype="multipart/form-data" action="/_admin/gallery/<?php print $this->makeLink(false, false, null, null, null, ""); ?>" method="post">
		<?php 
		if(GateKeeper::isLoginRequired())
		{
			$require_username = false;
			foreach(EncodeExplorerFiles::getConfig("users") as $user){
				if($user[0] != null && strlen($user[0]) > 0){
					$require_username = true;
					break;
				}
			}
			if($require_username)
			{
			?>
			<div><label for="user_name"><?php print $this->getString("username"); ?>:</label>
			<input type="text" name="user_name" value="" id="user_name" /></div>
			<?php 
			}
			?>
			<div><label for="user_pass"><?php print $this->getString("password"); ?>:</label>
			<input type="password" name="user_pass" id="user_pass" /></div>
			<div><input type="submit" value="<?php print $this->getString("log_in"); ?>" class="button" /></div>
		</form>
		</div>
	<?php 
		}
	}

	//
	// Printing the actual page
	// 
	function outputHtml()
	{
		global $_ERROR;
		global $_START_TIME;
?>
<?php
if(($this->getConfig('log_file') != null && strlen($this->getConfig('log_file')) > 0)
	|| ($this->getConfig('thumbnails') != null && $this->getConfig('thumbnails') == true && $this->mobile == false)
	|| (GateKeeper::isDeleteAllowed()))
{ 
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
<?php
	if(GateKeeper::isDeleteAllowed()){
?>
	$('td.del a').click(function(){
		var answer = confirm('Are you sure you want to delete : \'' + $(this).attr("data-name") + "\' ?");
		return answer;
	});
<?php 
	}	
	if($this->logging == true)
	{ 
?>
		function logFileClick(path)
		{
			 $.ajax({
		        	async: false,
					type: "POST",
					data: {log: path},
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					cache: false
				});
		}
		
		$("a.file").click(function(){
			logFileClick("<?php print $this->location->getDir(true, true, false, 0);?>" + $(this).html());
			return true;
		});
<?php 
	}
	if(EncodeExplorerFiles::getConfig("thumbnails") == true && $this->mobile == false)
	{
?>
		function positionThumbnail(e) {
			xOffset = 30;
			yOffset = 10;
			$("#thumb").css("left",(e.clientX + xOffset) + "px");

			diff = 0;
			if(e.clientY + $("#thumb").height() > $(window).height())
				diff = e.clientY + $("#thumb").height() - $(window).height();
			
			$("#thumb").css("top",(e.pageY - yOffset - diff) + "px");
		}
		
		$("a.thumb").hover(function(e){
			$("#thumb").remove();
			$("body").append("<div id=\"thumb\"><img src=\"../icons.php?thumb="+ $(this).attr("href") +"\" alt=\"Preview\" \/><\/div>");
			positionThumbnail(e);
			$("#thumb").fadeIn("medium");
		},
		function(){
			$("#thumb").remove();
		});

		$("a.thumb").mousemove(function(e){
			positionThumbnail(e);
			});

		$("a.thumb").click(function(e){$("#thumb").remove(); return true;});
<?php 
	}
?>
	});
//]]>                
</script>
<?php 
}
?>
<?php 
//
// Print the error (if there is something to print)
//
if(isset($_ERROR) && strlen($_ERROR) > 0)
{
	print "<div id=\"error\">".$_ERROR."</div>";
}
?>
<div id="frame">
<?php
if(EncodeExplorerFiles::getConfig('show_top') == true)
{
?>
<div id="top">
<?php 
if(EncodeExplorerFiles::getConfig("secondary_titles") != null && is_array(EncodeExplorerFiles::getConfig("secondary_titles")) && count(EncodeExplorerFiles::getConfig("secondary_titles")) > 0 && $this->mobile == false)
{
	$secondary_titles = EncodeExplorerFiles::getConfig("secondary_titles");
	print "<div class=\"subtitle\">".$secondary_titles[array_rand($secondary_titles)]."</div>\n";
}
?>
</div>
<?php
}

// Checking if the user is allowed to access the page, otherwise showing the login box
if(!GateKeeper::isAccessAllowed())
{
	$this->printLoginBox();
}
else 
{
if($this->mobile == false && EncodeExplorerFiles::getConfig("show_path") == true)
{
?>
<div class="breadcrumbs">
<a href="?dir="><?php print $this->getString("root"); ?></a>
<?php
	for($i = 0; $i < count($this->location->path); $i++)
	{
		print "&gt; <a href=\"".$this->makeLink(false, false, null, null, null, $this->location->getDir(false, true, false, count($this->location->path) - $i - 1))."\">";
		print $this->location->getPathLink($i, true);
		print "</a>\n";
	}
?>
</div>
<?php 
}
?>

<!-- START: List table -->
<table class="table">
<?php 
if($this->mobile == false)
{
?>
<tr class="row one header">
	<td class="icon"> </td>
	<td class="name"><?php print $this->makeArrow("name");?></td>
	<td class="size"><?php print $this->makeArrow("size"); ?></td>
	<td class="changed"><?php print $this->makeArrow("mod"); ?></td>
	<?php if($this->mobile == false && GateKeeper::isDeleteAllowed()){?>
	<td class="del"><?php print EncodeExplorerFiles::getString("del"); ?></td>
	<?php } ?>
</tr>
<?php 
}
?>
<tr class="row two">
	<td class="icon"><img alt="dir" src="../icons.php?img=directory" /></td>
	<td colspan="<?php print (($this->mobile == true?2:(GateKeeper::isDeleteAllowed()?4:3))); ?>" class="long">
		<a class="item" href="<?php print $this->makeLink(false, false, null, null, null, $this->location->getDir(false, true, false, 1)); ?>">..</a>
	</td>
</tr>
<?php
//
// Ready to display folders and files.
//
$row = 1;

//
// Folders first
//
if($this->dirs)
{
	foreach ($this->dirs as $dir)
	{
		$row_style = ($row ? "one" : "two");
		print "<tr class=\"row ".$row_style."\">\n";
		print "<td class=\"icon\"><img alt=\"dir\" src=\"../icons.php?img=directory\" /></td>\n";
		print "<td class=\"name\" colspan=\"".($this->mobile == true?2:3)."\">\n";
		print "<a href=\"".$this->makeLink(false, false, null, null, null, $this->location->getDir(false, true, false, 0).$dir->getNameEncoded())."\" class=\"item dir\">";
		print $dir->getNameHtml();
		print "</a>\n";
		print "</td>\n";
		if($this->mobile == false && GateKeeper::isDeleteAllowed()){
			print "<td class=\"del\"><a data-name=\"".htmlentities($dir->getName())."\" href=\"".$this->makeLink(false, false, null, null, $this->location->getDir(false, true, false, 0).$dir->getNameEncoded(), $this->location->getDir(false, true, false, 0))."\"><img src=\"../icons.php?img=del\" alt=\"Delete\" /></a></td>";
		}
		print "</tr>\n";
		$row =! $row;
	}
}

//
// Now the files
//
if($this->files)
{
	$count = 0;
	foreach ($this->files as $file)
	{
		$row_style = ($row ? "one" : "two");
		print "<tr class=\"row ".$row_style.(++$count == count($this->files)?" last":"")."\">\n";
		print "<td class=\"icon\"><img alt=\"".$file->getType()."\" src=\"".$this->makeIcon($file->getType())."\" /></td>\n";
		print "<td class=\"name\">\n";
		print "\t\t<a href=\"".$this->location->getDir(false, true, false, 0).$file->getNameEncoded()."\"";
		if(EncodeExplorerFiles::getConfig('open_in_new_window') == true)
			print "target=\"_blank\"";
		print " class=\"item file";
		if($file->isValidForThumb())
			print " thumb";
		print "\">";
		print $file->getNameHtml();
		if($this->mobile == true)
		{
			print "<span class =\"size\">".$this->formatSize($file->getSize())."</span>";
		}
		print "</a>\n";
		print "</td>\n";
		if($this->mobile != true)
		{
			print "<td class=\"size\">".$this->formatSize($file->getSize())."</td>\n";
			print "<td class=\"changed\">".$this->formatModTime($file->getModTime())."</td>\n";
		}
		if($this->mobile == false && GateKeeper::isDeleteAllowed()){
			print "<td class=\"del\">
				<a data-name=\"".htmlentities($file->getName())."\" href=\"".$this->makeLink(false, false, null, null, $this->location->getDir(false, true, false, 0).$file->getNameEncoded(), $this->location->getDir(false, true, false, 0))."\">
					<img src=\"../icons.php?img=del\" alt=\"Delete\" />
				</a>
			</td>";
		}
		print "</tr>\n";
		$row =! $row;
	}
}


//
// The files and folders have been displayed
//
?>

</table>
<!-- END: List table -->
<?php 
}
?>
</div>

<?php
if(GateKeeper::isAccessAllowed() && GateKeeper::showLoginBox()){
?>
<!-- START: Login area -->
<form enctype="multipart/form-data" method="post">
	<div id="login_bar">
	<?php print $this->getString("username"); ?>:
	<input type="text" name="user_name" value="" id="user_name" />
	<?php print $this->getString("password"); ?>:
	<input type="password" name="user_pass" id="user_pass" />
	<input type="submit" class="submit" value="<?php print $this->getString("log_in"); ?>" />
	<div class="bar"></div>
	</div>
</form>
<!-- END: Login area -->
<?php 
}
if(GateKeeper::isAccessAllowed() && $this->location->uploadAllowed() && (GateKeeper::isUploadAllowed() || GateKeeper::isNewdirAllowed()))
{
?>
<!-- START: Upload area -->
<form enctype="multipart/form-data" method="post">
	<div id="upload">
		<?php 
		if(GateKeeper::isNewdirAllowed()){
		?>
		<div id="newdir_container">
			<input name="userdir" type="text" class="upload_dirname" />
			<input type="submit" value="<?php print $this->getString("make_directory"); ?>" />
		</div>
		<?php 
		}
		if(GateKeeper::isUploadAllowed()){
		?>
		<div id="upload_container">
			<input name="userfile" type="file" class="upload_file" />
			<input type="submit" value="<?php print $this->getString("upload"); ?>" class="upload_sumbit" />
		</div>
		<?php 
		}
		?>
		<div class="bar"></div>
	</div>
</form>
<!-- END: Upload area -->
<?php
}

?>
<!-- START: Info area -->
<div id="info">
<?php
if(GateKeeper::isUserLoggedIn())

if(EncodeExplorerFiles::getConfig("mobile_enabled") == true)
{
	print "<a href=\"".$this->makeLink(true, false, null, null, null, $this->location->getDir(false, true, false, 0))."\">\n";
	print ($this->mobile == true)?$this->getString("standard_version"):$this->getString("mobile_version")."\n";
	print "</a> | \n";
}
if(GateKeeper::isAccessAllowed() && $this->getConfig("calculate_space_level") > 0 && $this->mobile == false)
{
	print $this->getString("total_used_space").": ".$this->spaceUsed." MB | ";
}
if($this->mobile == false && $this->getConfig("show_load_time") == true)
{
	printf($this->getString("page_load_time"), (microtime(TRUE) - $_START_TIME)*1000);
}
?> 
</div>
<!-- END: Info area -->
	
<?php
	}
}

//
// This is where the system is activated. 
// We check if the user wants an image and show it. If not, we show the explorer.
//
$EncodeExplorerFiles = new EncodeExplorerFiles();
$EncodeExplorerFiles->init();

GateKeeper::init();

if(!Logger::logQuery())
{
	$location = new Location();
	$location->init();
	if(GateKeeper::isAccessAllowed())
	{
		Logger::logAccess($location->getDir(true, false, false, 0), true);
		$fileManager = new FileManager();
		$fileManager->run($location);
	}
	$EncodeExplorerFiles->run($location);
}
?>
