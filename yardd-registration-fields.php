<?php
/*
Plugin Name: WP Custom user Registration
Plugin URI: 
Description: Add Custom Fields to Default Registration Form
Version: 1.0
Author: Yarddiant 
Author URI:https://www.yarddiant.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * 
 */

/* Add Menu page */

if ( ! defined( 'ABSPATH' ) ) exit;   // Exit if accessed directly
function yardd_registration () {
   global $wpdb;

   $table_name = $wpdb->prefix . "yarddian_registration";
   $charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE $table_name (
  id int(10) NOT NULL AUTO_INCREMENT,
  fname tinyint(2) NOT NULL DEFAULT 0,
  lname tinyint(2) NOT NULL DEFAULT 0,
  contact tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'yardd_registration');

add_action('admin_menu', 'yardd_reg_setup_menu');
 
function yardd_reg_setup_menu(){
        add_menu_page( 'Registration', 'Yardd Registration', 'manage_options', 'yard-registration', 'yardd_form_reg_init' );
}


function yardd_form_reg_init(){
  
   if($_POST['yupdated'] === 'true' ||
       wp_verify_nonce( $_POST['yardd_form'], 'yardd_update' )) { 
        
    yardd_reg_handle_fields();
  }
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
        echo '

        <div style="background-color:#fff; padding:30px; margin-top:30px; width:93%;">
        <h1>Choose the fileds...</h1></br>
        <form name="custom-reg" id="custom-reg" method="POST" >
        <input type="hidden" name="yupdated" value="true" /> ';
         wp_nonce_field( 'yardd_update', 'yardd_form' ); 
           echo '<input type="checkbox" name="fname" value="fname" />First Name</br></br>
           <input type="checkbox" name="lname" value="lname" /> Last Name</br></br>
            <input type="checkbox" name="contact" value="contact" />Contact Number</br></br>
           <input type="submit" name="yardd_submit" class="button button-primary" value="Submit"></li>
                  
          
        </form></div>';
        




}

function yardd_reg_handle_fields(){
   global $wpdb;
   if(
        ! isset( $_POST['yardd_form'] ) ||
        ! wp_verify_nonce( $_POST['yardd_form'], 'yardd_update' )
    ){ ?>
        <div class="error">
           <p>Sorry, your nonce was not correct. Please try again.</p>
        </div> <?php
        exit;
    } else {
         
       $subchecks = (isset($_POST['fname'])) ? 1 : 0;
        
       $subchecksb = (isset($_POST['lname'])) ? 1 : 0;
        $subchecksc = (isset($_POST['contact'])) ? 1 : 0;

        $subcheck = sanitize_text_field($subchecks);
         $subcheckb = sanitize_text_field($subchecksb);
          $subcheckc = sanitize_text_field($subchecksc);

        $success = $wpdb->insert("wp_yarddian_registration", array(
           "fname" => $subcheck,
           "lname" => $subcheckb,
          "contact" => $subcheckc,
        ));
   
    
 echo "<div class='updated notice is-dismissible' style='margin:0px; margin-top:10px; width:93%;'>
<p><strong>Successfully Submitted...<strong></p></div>";
   
    }


}


//1. Add a new form element...
add_action( 'register_form', 'yardd_myplugin_register_form' );
function yardd_myplugin_register_form() {
     global $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM wp_yarddian_registration"); 
    foreach($results as $row){   
        $fname =$row->fname;
        $lname =$row->lname;
        $contact =$row->contact;

    }

if($fname==1){
    $company_name = ( ! empty( $_POST['company_name'] ) ) ? sanitize_text_field( $_POST['company_name'] ) : '';
        
        ?>
        <p>
            <label for="company_name"><?php _e( 'First Name', 'mydomain' ) ?><br />
                <input type="text" name="company_name" id="company_name" class="input" value="<?php echo esc_attr(  $company_name  ); ?>" size="25" /></label>
        </p>
        
        <?php } if($lname==1){
     $contact_name  = ( ! empty( $_POST['contact_name '] ) ) ? sanitize_text_field( $_POST['contact_name'] ) : '';
     ?>
       <p>
            <label for="contact_name"><?php _e( 'Last Name', 'mydomain' ) ?><br />
                <input type="text" name="contact_name" id="contact_name" class="input" value="<?php echo esc_attr(  $contact_name  ); ?>" size="25" /></label>
        </p> 
         <?php } if($contact==1){
     $contact_nr  = ( ! empty( $_POST['contact_nr'] ) ) ? sanitize_text_field( $_POST['contact_nr'] ) : '';
     ?>
        <p>
            <label for="contact_nr"><?php _e( 'Contact Person Telephone Number', 'mydomain' ) ?><br />
                <input type="text" name="contact_nr" id="contact_nr" class="input" value="<?php echo esc_attr(  $contact_nr  ); ?>" size="25" /></label>
        </p> 

       
     <?php }
    }

    //2. Add validation. In this case, we make sure company_name is required.
    add_filter( 'registration_errors', 'yardd_myplugin_registration_errors', 10, 3 );
    function yardd_myplugin_registration_errors( $errors, $sanitized_user_login, $user_email ) {
         if($lname==1){
        if ( empty( $_POST['company_name'] ) || ! empty( $_POST['company_name'] ) && trim( $_POST['company_name'] ) == '' ) {
        $errors->add( 'company_name_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'You must include a company name.', 'mydomain' ) ) );

        }
       }if($lname==1){
        if ( empty( $_POST['contact_name'] ) || ! empty( $_POST['contact_name'] ) && trim( $_POST['contact_name'] ) == '' ) {
        $errors->add( 'contact_name_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'You must include a contact name.', 'mydomain' ) ) );

        }
        }if($contact==1){
        if ( empty( $_POST['contact_nr'] ) || ! empty( $_POST['contact_nr'] ) && trim( $_POST['contact_nr'] ) == '' ) {
        $errors->add( 'contact_nr_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'You must include a contact number.', 'mydomain' ) ) );

        }
    }


        return $errors;
    }

    //3. Finally, save our extra registration user meta.
    add_action( 'user_register', 'yardd_myplugin_user_register' );
    function yardd_myplugin_user_register( $user_id ) {

       
        $metas = array( 
   'fname'   => sanitize_text_field( $_POST['company_name'] ),
   
   'lname'  => sanitize_text_field( $_POST['contact_name'] ) ,
   'contact'   => sanitize_text_field( $_POST['contact_nr'] ) 
  
);
foreach($metas as $key => $value) {
   update_user_meta( $user_id, $key, $value );
}
           
        
    }

  

function yardd_render_profile_fields( WP_User $user ) { 
?>
<h3>Extra User information</h3> 
<table class="form-table">
<tr>

<th><label for="fname"><?php _e( 'Firstname', '' ) ?></label></th> 
<td> 
<input type="text" name="fname" id="fname" value="<?php echo esc_attr( get_the_author_meta( 'fname', $user->ID ) ); ?>" class="regular-text" maxlength="10"><br> 
</td>
</tr>
<tr>
<th><label for="lname"><?php _e( 'Lastname', '' ) ?></label></th> 
<td>
<input type="text" name="lname" id="lname" value="<?php echo esc_attr( get_the_author_meta( 'lname', $user->ID ) ); ?>" class="regular-text" maxlength="25"><br>           
</td>
</tr>
<tr>
<th><label for="contact"><?php _e( 'Contact', '' ) ?></label></th> 
<td>
<input type="text" name="contact" id="contact" value="<?php echo esc_attr( get_the_author_meta( 'contact', $user->ID ) ); ?>" class="regular-text" maxlength="25"><br>           
</td>
</tr>
</table>
<?php
}
add_action('show_user_profile', 'yardd_render_profile_fields' ); 
add_action( 'edit_user_profile', 'yardd_render_profile_fields' );

function yardd_save_custom_user_profile_fields( $id )
{
   //$id=get_current_user_id();
if (isset( $_POST['fname'] )  ) { 
update_user_meta( $id, 'fname', sanitize_text_field($_POST['fname'] ));  
}
if ( isset( $_POST['lname'])) { 
update_user_meta( $id, 'lname', sanitize_text_field($_POST['lname'] )); 
} 
if ( isset( $_POST['contact']) ) { 
update_user_meta( $id, 'contact', sanitize_text_field($_POST['contact'] )); 
} 

}
add_action( 'edit_user_profile_update', 'yardd_save_custom_user_profile_fields' );

add_action( 'personal_options_update', 'yardd_save_custom_user_profile_fields' );

