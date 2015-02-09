<?php

/*  
	"Μισθοδοσία online" - Εφαρμογή άντλησης και παρουσίασης οικoνομικών στοιχείων από αρχεία XML
    Copyright (C) 2011 Βελέντζας Αλέξανδρος (fractalbit@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/copyleft/gpl.html.
*/

/* *********** ΓΕΝΙΚΗ ΠΕΡΙΓΡΑΦΗ ΛΕΙΤΟΥΡΓΙΑΣ ΑΡΧΕΙΟΥ *********** */
// Συχνά χρησιμοποιούμενες συναρτήσεις (fuf? :P)
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */

function save_file($filename, $data){
 	$data = serialize($data); 	
 	$file = fopen($filename, 'w');
 	$data = '<?php $person = \''. addslashes($data) . '\';';
 	fwrite($file, $data);
 	fclose($file);
 	unset($data);
 }

 function load_file($file){
 	//$contents = file_get_contents($file);
 	include($file);
 	$data = unserialize(stripslashes($person));

 	return $data;
 }

function rand_str($length = 10) {    
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }

    return $string;
}

function savelog($message, $file = 'admin_log.txt'){
    $file = APP_DIR . '/' . $file; 
    $log = fopen($file, 'a+');

    $message = $message . "\r\n" ;
    fwrite($log, $message);
    fclose($log);
}

function get_admin_menu(){
    $menu = array(
                    array('name' => 'Αρχική', 'url' => 'index.php'),
                    array('name' => 'Μισθοδοτούμενοι', 'url' => 'list_users.php'),
                    array('name' => 'Διαχείριση XML', 'url' => 'manage_xml.php'),
                    array('name' => 'Ρυθμίσεις', 'url' => 'settings.php'),
                    array('name' => 'Αρχείο καταγραφής', 'url' => 'view_log.php'),
                    //array('name' => 'Τεκμηρίωση', 'url' => 'https://github.com/fractalbit/misthodosia-online/blob/master/readme.md', 'target' => '_blank'),
                );

    return $menu;
}

function print_admin_menu(){
    global $admin;

    $menu = get_admin_menu();
    $current_script = end(explode('/', $_SERVER['PHP_SELF']));
    ?>
    <div id="admin-menu-container" class="clearfix">
        <div class="left">
            <ul id="admin-menu" class="clearfix">
                <?php
                    foreach($menu as $item){
                        $class = '';
                        if($item['url'] == $current_script) $class = 'class="current"';
                        ?>
                        <li <?php echo $class; ?>><a href="<?php echo $item['url']; ?>" <?php if(isset($item['target'])) echo 'target="_blank"'; ?>><?php echo $item['name']; ?></a></li>
                        <?php
                    }
                ?>
            </ul>
        </div>
        <div class="right">
            <?php echo $admin->message; ?>
        </div>
    </div>
    <?php
}


function print_header(){
    global $admin;
	?>
	<!doctype html>
	<html lang="el">
	<head>
		<meta charset="utf-8">
		<title>Μισθοδοσία online - <?php echo ORG_TITLE; ?></title>

		<link rel="stylesheet" href="css/reset.css">
		<link rel="stylesheet" href="css/style.css">

        <!-- Include Font Awesome. -->
          <link href="js/froala/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

          <?php if( admin_configured() && $admin->check_logged_in() ){ ?>
              <!-- Include froala Editor style. -->
              <link href="js/froala/css/froala_editor.min.css" rel="stylesheet" type="text/css" />

              <!-- Include pws tabs style. -->
              <link type="text/css" rel="stylesheet" href="js/pwstabs/assets/jquery.pwstabs-1.2.1.css">
          <?php
            }
          ?>

          <link href="js/froala/css/froala_style.min.css" rel="stylesheet" type="text/css" />


        <!-- load google hosted jquery with local fallback -->
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/jquery-1.7.1.min.js"><\/script>')</script>

        <script src="js/jquery.slideto.v1.2.custom.js"></script>
        <?php if( admin_configured() && $admin->check_logged_in() ){ ?>
              <script src="js/jquery.fastLiveFilter.js"></script>
        <?php
           }
        ?>

        <script src="js/script.js"></script>
	</head>
	<body>
	<div class="container">

    <?php
        $txt = '';
        if(!$admin->check_logged_in()){
            $txt = '<a href="list_users.php">Διαχείριση</a>';
        }
		echo '<div id="header" class="clearfix">
                <div class="left"><h2><a href="'.ORG_URL.'" class="button">'.ORG_TITLE.'</a> &raquo; <a href="index.php" class="button">Μισθοδοσία online</a></h2></div>
                <div class="right subtle">'.$txt.'</div>
              </div>';

        if($admin->check_logged_in()){
            print_admin_menu();
        }

}

function print_footer(){
	?>
    	<br />
    	<hr />
    	<div class="subtle">Λογισμικό ανοικτού κώδικα "<a href="http://dide.arg.sch.gr/grmixan/misthodosia-online-app/" target="_blank">Μισθοδοσία online</a>"</span>
    	</div>
        <?php
            if(file_exists(APP_DIR . '/cms/google_analytics.code')){
                $ga_code = trim(file_get_contents(APP_DIR . '/cms/google_analytics.code'));
                if(!empty($ga_code)) echo $ga_code;
            }

        ?>
    	</body>
    	</html>
    <?php
}

function full_dir(){
    return dirname(__FILE__);
}

/*function current_dir(){
	$temp = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
	$dir = array_pop($temp);
    //dump($dir);
	return $dir;
}
*/
function current_dir()
{
    $path = dirname($_SERVER[PHP_SELF]);
    $position = strrpos($path,'/') + 1;
    return substr($path,$position);
} 



////////////////////////////////////////////////////////
// Function:         dump
// Inspired from:     PHP.net Contributions
// Description: Helps with php debugging

function dump(&$var, $info = FALSE)
{
    $scope = false;
    $prefix = 'unique';
    $suffix = 'value';
  
    if($scope) $vals = $scope;
    else $vals = $GLOBALS;

    $old = $var;
    $var = $new = $prefix.rand().$suffix; $vname = FALSE;
    foreach($vals as $key => $val) if($val === $new) $vname = $key;
    $var = $old;

    echo "<pre style='margin: 0px 0px 10px 0px; display: block; background: white; color: black; font-family: Verdana; border: 1px solid #cccccc; padding: 5px; font-size: 12px; line-height: 18px;'>";
    if($info != FALSE) echo "<b style='color: red;'>$info:</b><br>";
    do_dump($var, '$'.$vname);
    echo "</pre>";
}

////////////////////////////////////////////////////////
// Function:         do_dump
// Inspired from:     PHP.net Contributions
// Description: Better GI than print_r or var_dump

function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
{
    $do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';

    if (is_array($var) && isset($var[$keyvar]))
    {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    }
    else
    {
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];
    
        $type = ucfirst(gettype($avar));
        if($type == "String") $type_color = "<span style='color:green'>";
        elseif($type == "Integer") $type_color = "<span style='color:red'>";
        elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
        elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
        elseif($type == "NULL") $type_color = "<span style='color:black'>";
    
        if(is_array($avar))
        {
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#a2a2a2'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach($keys as $name)
            {
                $value = &$avar[$name];
                do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        }
        elseif(is_object($avar))
        {
            echo "$indent$var_name <span style='color:#a2a2a2'>$type</span><br>$indent(<br>";
            foreach($avar as $name=>$value) do_dump($value, "$name", $indent.$do_dump_indent, $reference);
            echo "$indent)<br>";
        }
        elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
        elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color\"$avar\"</span><br>";
        elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
        elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>";
        elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>";
        else echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $avar<br>";

        $var = $var[$keyvar];
    }
}

/**
 * Appends a trailing slash.
 *
 * Will remove trailing slash if it exists already before adding a trailing
 * slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 1.2.0
 * @uses untrailingslashit() Unslashes string if it was slashed already.
 *
 * @param string $string What to add the trailing slash to.
 * @return string String with trailing slash added.
 */
function trailingslashit($string) {
    return untrailingslashit($string) . '/';
}

/**
 * Removes trailing slash if it exists.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 2.2.0
 *
 * @param string $string What to remove the trailing slash from.
 * @return string String without the trailing slash.
 */
function untrailingslashit($string) {
    return rtrim($string, '/');
}

function check_afm($afm){
// Ελέγχει αν ο ΑΦΜ είναι έγκυρος και επιστρέφει true ή false
    if ($afm == '' || strlen($afm) != 9){
        return false;
    } else {
        $cd = substr($afm, 8, 1); 
    }
    if ($afm == '000000000'){
        return false;
    }

    $sum = 0;
    $afm_ok = false;

    for($i=0; $i<8; $i++){
        if (ord(substr($afm, $i, 1)) < 48 || ord(substr($afm, $i, 1)) > 57){
            return false;        
        } else {
            $d = substr($afm, $i, 1);
            if ($i<8){
                $sum = $sum + $d * pow(2,8-$i);
            }
        }
    }
    if ($sum == 0){
        return false;
    } else {
        $calc = $sum % 11;
        if ($calc == $cd || (($calc == 0 || $calc == 10) && $cd == 0) ){
            return true;
        } else {
            return false;
        } 
    }

}