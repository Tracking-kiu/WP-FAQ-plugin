<?php
/*
Plugin Name: CPT Bootstrap FAQ
Plugin URI: 
Description: 
Version: 1.0
Author: Sten Winroth
Author URI: www.innodrift.com
License: GPLv2
*/

// Custom Post Type Setup
add_action( 'init', 'cptModal_post_type' );
function cptModal_post_type() {
	$labels = array(
		'name' => 'Modals',
		'singular_name' => 'Modal',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New modal',
		'edit_item' => 'Edit modal',
		'new_item' => 'New modal',
		'view_item' => 'View modal',
		'search_items' => 'Search modals',
		'not_found' =>  'No modal',
		'not_found_in_trash' => 'No modal found in Trash', 
		'parent_item_colon' => '',
		'menu_name' => 'FAQ'
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'page',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => 21,
		'supports' => array('title','editor', 'page-attributes', 'custom-fields')
	); 
	register_post_type('cptModal', $args);
}

// FRONT END

// Shortcode
function cptModal_shortcode($atts, $content = null) {
	// Set default shortcode attributes
	$defaults = array(
		'parent' => 'false',
		'toggle' => 'false',
	);

	// Parse incomming $atts into an array and merge it with $defaults
	$atts = shortcode_atts($defaults, $atts);

	return cptModal_frontend($atts);
}
add_shortcode('FAQ_CPT', 'cptmodal_shortcode');

// Display latest WftC
function cptModal_frontend($atts){
	$args = array( 'post_type' => 'cptModal', 'orderby' => 'menu_order', 'order' => 'ASC');
	$loop = new WP_Query( $args );
	$modals = array();
	while ( $loop->have_posts() ) {
		$loop->the_post();
		if ( '' != get_the_title() ) {
			$title = get_the_title();
			$content = get_the_content();
			$id = get_the_ID();
			$script = get_post_custom_values("script");
			$modals[] = array('title' => $title, 'content' => $content, 'id' => $id, 'divSpecial' => $divSpecial);
		}
	}
	if(count($modals) > 0){
		ob_start();
?>
<div class="container">
	<div class="row">
		<div class="panel-group" id="accordion">
		<?php $i = 0; ?>
		<?php foreach ($modals as $key => $title) { ?>
		<?php $i++; ?>
		  <div class="panel panel-default">
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a id="<?php echo $title['id']; ?>" data-toggle="collapse" data-parent="#accordion" href="#cptmodal_<?php echo $title['id']; ?>">
		        	<?php echo $i; ?>: <?php echo $title['title'];?>
		        </a>
		       </h4>
		    </div><!--panel-heading-->
		   </div><!--panel panel-default-->
		  <div id="cptmodal_<?php echo $title['id']; ?>" class="panel-collapse collapse">
      		<div class="panel-body">
      			<p><?php echo $title['content'] ?></p>
			</div>
    	</div>
    	<?php } ?>
  		</div><!--class="panel-group" id="accordion"-->
  	</div><!--class="row"-->
</div><!--class="container"-->

<?php }
	$output = ob_get_contents();
	ob_end_clean();
	
	// Restore original Post Data
	wp_reset_postdata();	
	
	return $output;
}
?>