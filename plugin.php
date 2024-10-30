<?php
/**
 * Plugin Name: Bulk Delete Users by Email
 * Plugin URI: http://www.speakdigital.co.uk
 * Description: Delete users by entering plaintext which will be searched for email addresse and matching user accounts then deleted.
 * Version: 1.2
 * Author: Ben Konyn | Speak Digital
 * Author URI: http://www.speakdigital.co.uk
 * License: GPL2
 */

add_action('admin_menu', 'baw_create_menu');

function baw_create_menu() {

	//create new top-level menu
	add_menu_page('Delete Users', 'Delete Users', 'administrator', __FILE__, 'baw_settings_page','dashicons-dismiss');

	//call register settings function
	//add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	//register_setting( 'baw-settings-group', 'new_option_name' );
	//register_setting( 'baw-settings-group', 'some_other_option' );
	//register_setting( 'baw-settings-group', 'option_etc' );
}

function baw_settings_page() {
?>
<div class="wrap">
<h2>Delete Users</h2>

<form method="post">
<p>Copy and paste plain text below. We will search for email addresses and then attempt to delete users with matching email addresses.</p>
<textarea rows=10 cols=120 name="de-text"><?=$_REQUEST['de-text']?></textarea>
    <?php submit_button("Search and Delete"); ?>

<?PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (isset($_REQUEST['de-text']))
{	$emails = extract_email_address ($_REQUEST['de-text']);
	$emails = array_unique($emails);
	$found = array();
	foreach ($emails as $email)
	{	$blogusers = get_users( array( 'search' => $email ) );
		$found = array_merge( $found, $blogusers);
	}
	if (count($found)) 
	{	print "<p><b>Matched ".count($found)." emails...</b></p>";
		print "<table>";
		foreach ( $found as $user ) {
                	echo '<tr><td>' . esc_html( $user->user_email ) . '</td><td>';
			if (in_array("administrator",$user->roles))
			{	print "<b>Not deleted as Administrator role</b>";
			} else
			{	wp_delete_user($user->ID);
				print "Deleted";
			}
			print "</td></tr>";
		}
		print "</table>";
        } else 
	{	print "<p>No matching users found.</p>";
	}

}

?>
</form>
</div>
<?php } 

function extract_email_address ($string) {
    foreach(preg_split('/\s/', $string) as $token) {
        $email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
        if ($email !== false) {
            $emails[] = $email;
        }
    }
    return $emails;
}

?>
