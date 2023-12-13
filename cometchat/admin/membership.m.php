<?php

function index() {
	global $body, $rt;
	global $body,$plugins,$crplugins,$ts,$hideconfig,$client,$settings;
	$plugins_core 	 = unserialize($settings['plugins_core']['value']);
	$modules_core 	 = unserialize($settings['modules_core']['value']);
	$extensions_core = unserialize($settings['extensions_core']['value']);
	$unsetextensions = array('ads' => 'Advertisements','mobileapp' => 'Mobile App', 'desktop' => 'Desktop App');
	$extensions_core = array_diff($extensions_core,$unsetextensions);
	$members_roles 	 = getRolesDetails();
	$UI = '';
	$i = 1;
	$total_count =  count($plugins_core)+count($modules_core)+count($extensions_core);
	$record_per_col = intval($total_count/4)+1;
	foreach ($members_roles as $member => $value) {
		global ${$member.'_plugins'} , ${$member.'_modules'}, ${$member.'_extensions'}, ${$member.'_disabledweb'}, ${$member.'_disabledmobileapp'}, ${$member.'_disableddesktop'}, ${$member.'_disabledcc'};
		$memberPlugins     	= ${$member.'_plugins'};
		$memberModules     	= ${$member.'_modules'};
		$memberExtensions  	= ${$member.'_extensions'};
		$webCheck      		= (${$member.'_disabledweb'} == 1) ? 'checked' : '';
		$mobileCheck   		= (${$member.'_disabledmobileapp'} == 1) ? 'checked' : '';
		$desktopCheck  		= (${$member.'_disableddesktop'} == 1) ? 'checked' : '';
		$ccCheck  			= (${$member.'_disabledcc'} == 1) ? 'checked' : '';

		$title 			= $value['name'];
		/*$open 			= ($i == 1) ? 'in' : '';*/
		$open 			= '';
		$UI .= <<<EOD
		    <div class="panel panel-default">
		      <div class="panel-heading">
		        <h4 class="panel-title">
		          <a style="display:block;" data-toggle="collapse" data-parent="#accordion" href="#collapse_{$member}">{$title}</a>
		        </h4>
		      </div>
		      <div id="collapse_{$member}" class="panel-collapse collapse {$open}">
		        <div class="panel-body">
		        <div class="form-group row">
		        	<div class="col-sm-12 col-lg-12">
		        		Disable CometChat completely for this role
		        		<hr>
						<div class="checkbox checkbox-success">
			                <input name="{$member}_disabledcc" $ccCheck value="1" id="{$member}_disabledcc" type="checkbox">
			                <label for="{$member}_disabledcc">
			                    Yes
			                </label>
			            </div>

		        	</div>
		        </div>
				<div class="row">
		        	<div class="col-sm-12 col-lg-12">
		        		Disable Specific Features of CometChat for this role
		        		<hr>
		        	</div>
		        </div>

			        <div class="form-group row">
EOD;
	$k = 0;
	foreach ($plugins_core as $pKey => $pVal) {
		if ($k == 0 || $k == $record_per_col) {
			$UI .= '<div class="col-sm-3 col-lg-3">';
		}
		$check = (in_array($pKey, $memberPlugins)) ? "" : 'checked';
		$featureTitle = ucwords($pVal[0]);
		$UI .= <<<EOD
		            <div class="checkbox checkbox-success">
		                <input name="{$member}_plugins[]" value="{$pKey}" $check id="checkbox_{$member}_{$pKey}" type="checkbox">
		                <label for="checkbox_{$member}_{$pKey}">
		                    {$featureTitle}
		                </label>
		            </div>
EOD;
	$k++;
		if ($k == $record_per_col) {
			$UI .= '</div>';
			$k = 0;
		}
	}
	foreach ($modules_core as $mKey => $mVal) {
		if ($k == 0 || $k == $record_per_col) {
			$UI .= '<div class="col-sm-3 col-lg-3">';
		}
		$check = (in_array($mKey, $memberModules)) ? "" : 'checked';
		$featureTitle = ucwords($mVal[1]);
		$UI .= <<<EOD
		            <div class="checkbox checkbox-success">
		                <input name="{$member}_modules[]" $check value="{$mKey}" id="checkbox_{$member}_{$mKey}" type="checkbox">
		                <label for="checkbox_{$member}_{$mKey}">
		                    {$featureTitle}
		                </label>
		            </div>
EOD;
	$k++;
		if ($k == $record_per_col) {
			$UI .= '</div>';
			$k = 0;
		}
	}

	foreach ($extensions_core as $eKey => $eVal) {
		if ($k == 0 || $k == $record_per_col) {
			$UI .= '<div class="col-sm-3 col-lg-3">';
		}
		$check = (in_array($eKey, $memberExtensions)) ? "" : 'checked';
		$featureTitle = ucwords($eVal);
		$UI .= <<<EOD
                    <div class="checkbox checkbox-success">
                        <input name="{$member}_extensions[]" $check value="{$eKey}" id="checkbox_{$member}_{$eKey}" type="checkbox">
                        <label for="checkbox_{$member}_{$eKey}">
                            {$featureTitle}
                        </label>
                    </div>
EOD;
	$k++;
		if ($k == $record_per_col) {
			$UI .= '</div>';
			$k = 0;
		}
	}

		$UI .= <<<EOD
		        </div>
		        </div>
		        <div class="form-group row">
		        	<div class="col-sm-12 col-lg-12">
		        		Disable Specific Platforms for this role
		        		<hr>
						<div class="checkbox checkbox-success">
			                <input name="{$member}_disabledweb" $webCheck value="1" id="{$member}_disabledweb" type="checkbox">
			                <label for="{$member}_disabledweb">
			                    Web
			                </label>
			            </div>
						<div class="checkbox checkbox-success">
			                <input name="{$member}_disabledmobileapp" $mobileCheck value="1" id="{$member}_disabledmobileapp" type="checkbox">
			                <label for="{$member}_disabledmobileapp">
			                    Mobile
			                </label>
			            </div>
						<div class="checkbox checkbox-success">
			                <input name="{$member}_disableddesktop" $desktopCheck value="1" id="{$member}_disableddesktop" type="checkbox">
			                <label for="{$member}_disableddesktop">
			                    Desktop
			                </label>
			            </div>
		        	</div>
		        </div>

		        </div>
		      </div>
		    </div>
EOD;
		$i++;
	}

$body = <<<EOD
<div class="row">
	<div class="col-sm-12 col-lg-12">
    <div class="card">
		<div class="card-header">
			Role Based Permissions
		</div>
		<div class="card-block">
			<form action="?module=membership&action=updatemembership&ts={$ts}" method="post">
				<div class="panel-group" id="accordion">
					{$UI}
			  	</div>
				<div class="form-actions">
					<input type="submit" value="Update" class="btn btn-primary">
				</div>
			</form>
		</div>
    </div>
  	</div>
</div>

EOD;
	template();
}

function updatemembership(){
	global $plugins,$ts,$client,$settings;
	$members_roles 	 = getRolesDetails();
	$plugins_core 	 = array_keys(unserialize($settings['plugins_core']['value']));
	$modules_core 	 = array_keys(unserialize($settings['modules_core']['value']));
	$extensions_core = array_keys(unserialize($settings['extensions_core']['value']));
	$unsetextensions = array('ads','mobileapp', 'desktop');
	$extensions_core = array_diff($extensions_core,$unsetextensions);
	foreach ($members_roles as $member => $value) {
		$disablePlugins     = empty($_POST[$member.'_plugins']) ? array() : $_POST[$member.'_plugins'];
		$disableModules     = empty($_POST[$member.'_modules']) ? array() : $_POST[$member.'_modules'];
		$disableExtensions  = empty($_POST[$member.'_extensions']) ? array() : $_POST[$member.'_extensions'];
		$memberPlugins     	= array_diff($plugins_core,$disablePlugins);
		$memberModules     	= array_diff($modules_core,$disableModules);
		$memberExtensions  	= array_diff($extensions_core,$disableExtensions);
		configeditor(array(
			$member.'_plugins' => $memberPlugins,
			$member.'_modules' => $memberModules,
			$member.'_extensions' => $memberExtensions
			)
		);
		configeditor(array(
			$member.'_disabledweb' 		=> empty($_POST[$member.'_disabledweb']) ? 0 : 1,
			$member.'_disabledmobileapp' 	=> empty($_POST[$member.'_disabledmobileapp']) ? 0 : 1,
			$member.'_disableddesktop' 	=> empty($_POST[$member.'_disableddesktop']) ? 0 : 1,
			$member.'_disabledcc' 	=> empty($_POST[$member.'_disabledcc']) ? 0 : 1,
			)
		);
	}
	$GLOBALS['integration']->updateUserRoles();
	$_SESSION['cometchat']['error'] = 'Permissions applied successfully';
	header("Location:?module=membership&ts={$ts}");
	exit();
}
