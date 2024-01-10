<?php
add_action( 'wp_enqueue_scripts', 'hubspot_blog_theme_enqueue_styles' );

function hubspot_blog_theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

add_theme_support( 'post-formats', array ( 'aside', 'gallery', 'quote', 'image', 'video' ) );
add_theme_support( 'menus' );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function theme_enqueue_styles() {
wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css',array('parent-style')
);
wp_enqueue_style('slick-style',get_stylesheet_directory_uri() .'/assets/slick/slick.css');
wp_enqueue_style('slick-theme-style',get_stylesheet_directory_uri() .'/assets/slick/slick-theme.css');
wp_enqueue_script( 'slick-js', get_stylesheet_directory_uri() . '/assets/slick/slick.min.js');
}

function create_posttype() {
  
    register_post_type( 'features',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Features' ),
                'singular_name' => __( 'Feature' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'features'),
            'show_in_rest' => true,
            'supports' => array( 'title', 'editor', 'custom-fields','thumbnail','excerpt' ),
  
        )
    );

    register_post_type( 'teams',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Teams' ),
                'singular_name' => __( 'Team' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'teams'),
            'show_in_rest' => true,
            'supports' => array( 'title', 'editor', 'custom-fields','thumbnail','excerpt' ),
  
        )
    );
}

add_action( 'init', 'create_posttype' );

function wpb_feature_shortcode() { 

	$loop = new WP_Query( array( 'post_type' => 'features', 'posts_per_page' => 6 ,'order' => 'ASC') );
	$count = 0;
	$message = '<div class="wp-block-group feature-main"><div class="feature-row">';

    while ( $loop->have_posts() ) : $loop->the_post(); 
    $count ++;
    $message .=  '<div class="feature-item">' .
    			 '<div class="feature-img">'.get_the_post_thumbnail().'</div>'.
    			 '<h6>'.get_the_title() .'</h6>'.
			     '<div class="feature-cnt">' .
					   get_the_excerpt().
				 '</div>'.
				 '</div>';

	if($count % 3 == 0 && $count < 6){
		$message .= '</div><div class="feature-row">';
	}
    endwhile;

    $message .= '</div></div>';

	return $message;
}

add_shortcode('features_listing', 'wpb_feature_shortcode');

function team_add_meta_boxes( $post ){
	add_meta_box( 'team_meta_box', __( 'Designation & Socail Links', 'team_example_plugin' ), 'team_build_meta_box', 'teams', 'side', 'low' );
}
add_action( 'add_meta_boxes', 'team_add_meta_boxes' );

function team_build_meta_box( $post ){
	// make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'team_meta_box_nonce' );


	// retrieve current value
	$current_designation = get_post_meta( $post->ID, '_team_designations', true );
	$current_fb = get_post_meta( $post->ID, '_team_fb', true );
	$current_twitter = get_post_meta( $post->ID, '_team_twitter', true );
	$current_google = get_post_meta( $post->ID, '_team_google', true );
	$current_linkedin = get_post_meta( $post->ID, '_team_linkedin', true );

	?>
	<div class='inside'>

		<h3><?php _e( 'Designation', 'team_example_plugin' ); ?></h3>
		<p>
			<input type="text" name="designations" value="<?php echo $current_designation; ?>" /> 
		</p>

		<h3><?php _e( 'Social Links', 'team_example_plugin' ); ?></h3>
		<p>
			Facebook : <input type="text" name="fb" value="<?php echo $current_fb; ?>" /> 
		</p>
		<p>
			Twitter : <input type="text" name="twitter" value="<?php echo $current_twitter; ?>" /> 
		</p>
		<p>
			Google Plus : <input type="text" name="google" value="<?php echo $current_google; ?>" /> 
		</p>
		<p>
			Linkedin : <input type="text" name="linkedin" value="<?php echo $current_linkedin; ?>" /> 
		</p>
		

	</div>
	<?php
}

function team_save_meta_box_data( $post_id ){
	// verify meta box nonce
	if ( !isset( $_POST['team_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['team_meta_box_nonce'], basename( __FILE__ ) ) ){
		return;
	}

	// return if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	}

  // Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}

	// store custom fields values
	
	if ( isset( $_REQUEST['designations'] ) ) {
		update_post_meta( $post_id, '_team_designations', sanitize_text_field( $_POST['designations'] ) );
	}
	if ( isset( $_REQUEST['fb'] ) ) {
		update_post_meta( $post_id, '_team_fb', sanitize_text_field( $_POST['fb'] ) );
	}
	if ( isset( $_REQUEST['twitter'] ) ) {
		update_post_meta( $post_id, '_team_twitter', sanitize_text_field( $_POST['twitter'] ) );
	}
	if ( isset( $_REQUEST['google'] ) ) {
		update_post_meta( $post_id, '_team_google', sanitize_text_field( $_POST['google'] ) );
	}
	if ( isset( $_REQUEST['linkedin'] ) ) {
		update_post_meta( $post_id, '_team_linkedin', sanitize_text_field( $_POST['linkedin'] ) );
	}


}
add_action( 'save_post', 'team_save_meta_box_data' );

function wpb_team_shortcode() { 

	$loop = new WP_Query( array( 'post_type' => 'teams', 'posts_per_page' => 5 ,'order' => 'ASC') );
	$message = '<div class="wp-block-group team-main"><div class="team-row">';

    while ( $loop->have_posts() ) : $loop->the_post(); 

    $current_designation = get_post_meta( get_the_ID(), '_team_designations', true );
	$current_fb = get_post_meta( get_the_ID(), '_team_fb', true );
	$current_twitter = get_post_meta( get_the_ID(), '_team_twitter', true );
	$current_google = get_post_meta( get_the_ID(), '_team_google', true );
	$current_linkedin = get_post_meta( get_the_ID(), '_team_linkedin', true );

    $message .=  '<div class="team-item">' .
    			 '<div class="team-img">'.get_the_post_thumbnail().'</div>'.
    			 '<h6>'.get_the_title() .'</h6>'.
			     '<div class="team-desi">' .
					   $current_designation.
				 '</div>'.
				 '<div class="team-social" />' .
				 '<a href="'.$current_fb.'" target="_blank"><img src="'.site_url().'/wp-content/uploads/2024/01/fb.jpg" /></a>'.
				 '<a href="'.$current_twitter.'" target="_blank"><img src="'.site_url().'/wp-content/uploads/2024/01/twitter.jpg" /></a>'.
				 '<a href="'.$current_google.'" target="_blank"><img src="'.site_url().'/wp-content/uploads/2024/01/google.jpg" /></a>'.
				 '<a href="'.$current_linkedin.'" target="_blank"><img src="'.site_url().'/wp-content/uploads/2024/01/linkedin.jpg" /></a>'.
				 '</div>'.
				 '</div>';

    endwhile;

    $message .= '</div></div>';

	return $message;
}

add_shortcode('team_listing', 'wpb_team_shortcode');
?>