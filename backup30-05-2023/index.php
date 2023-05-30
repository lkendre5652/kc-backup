<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
get_header(); ?>
<?php echo do_shortcode( '[snippet banner]' ); ?>		
<?php
$txnmy = "category";
$pstTyp = "post";
$pstTerms = get_terms( array( 'post_type' => $pstTyp, 'taxonomy' => $txnmy, 'parent' => 0,  'exclude' => 1, ) );
$postQuery = new WP_Query(['post_type'=>'post', 'order' => 'ASC', 'posts_per_page' => -1 ]);
$postCount = $postQuery->post_count;
?>
<div id="block-2" class="widget widget_block widget_search">
	<form method="post" id="post_form" >
	  <ul>   	    
		  <li class="form-controler">		    
		  	<div class="form-controler">
		      <input type="text"name="blog_search" id="blog_search" placeholder="Search Blog" onchange="obj.postFilter()" /><span class="hidden" id='loader'>loading...</span>
		    </div>
		  </li>
		   <?php foreach($pstTerms as $pstTerm ){ ?>              
		  <li class="form-controler">
		    <div class="form-controler">
		      <select name="cats_<?php echo $pstTerm->term_id;?>" id="cats_<?php echo $pstTerm->term_id;?>" onchange="obj.postFilter()" >
		      	<option value=""> <?php echo $pstTerm->name; ?> </option>
		      <?php 
		      $childTerms = get_terms( array( 'post_type' => $pstTyp, 'taxonomy' => $txnmy, 'parent' => $pstTerm->term_id ) ); 
		      foreach($childTerms as $childTerm ){ ?>                
		        <option value="<?php echo $childTerm->term_id; ?>"> <?php echo $childTerm->name; ?> </option>
		        <?php } ?>                
		      </select>
		    </div>
		  </li>
	  <?php } ?>          
	  <li>
	  	<button type="button" onclick="obj.isClear()">Clear Filters</button>
	  </li>
	</ul>
	</form>
</div>

<div id="primary" >				
	<div class="card_section">
		<div class="card_outer" id="bolg_posts">
			<?php $pctr = 0; ?>
			<?php while( $postQuery->have_posts() ) : $postQuery->the_post(); ?>
	       <div class="card_wrap <?php echo ($pctr >= 4 )? 'hidden' : ''; ?>">
	            <div class="card_inner">
	                <div class="card_box_left">			                	
	                	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="blogimagestop" style="background:url(<?php the_post_thumbnail_url(); ?>);">
		                	<img src="<?php echo the_post_thumbnail_url(); ?>" />
		                </a>								
	                </div>
	                <div class="card_box_right">
	                    <div class="card_box_Inner">
	                        <div class="date_time_wrap">
	                            <div class="date_content"><?php the_time( 'F j, Y' ); ?></div>
	                        </div>
	                        <h3 class="card_content"><?php the_title(); ?></h3>
	                        <a href="<?php the_permalink(); ?>">Read More</a>
	                    </div>
	                </div>
	            </div>
	      	</div>
		  	<?php $pctr++; endwhile; ?>
		</div>
		<?php if( $postCount > 4 ){ ?>
	    <div class="button_wraploadmore">
		   <button class="elementor-button-link elementor-button elementor-size-sm elementor-animation-float load_btn" role="button" onclick="obj.isLoadMe(this)">
				<span class="elementor-button-content-wrapper">
					<span class="elementor-button-text">Load More</span>
				</span>
			</button>
		</div>
		<?php } ?>
	</div>
</div>
<?php
	if ( astra_page_layout() == 'right-sidebar' ) :
		//get_sidebar();
	endif;
	get_footer(); 
?>
<script>
class FormSub{	
	constructor(){}				
	isLoadMe(values){
		var total_list = jQuery("#bolg_posts .card_wrap").length;
		if(total_list >= 4 ){
			jQuery("#bolg_posts .card_wrap").removeClass('hidden');
		}
		jQuery(values).hide();		
	}
	postFilter() {
		jQuery('.load_btn').show();
    	var formid = document.getElementById('post_form');
    	var formData = new FormData(formid);    	    	
    	var frmArr = {}	;
    	for( const [key,values] of formData.entries() ){
    		frmArr[key] = values;
    	}
    	frmArr['action'] = 'search_blog_filters';    	    	
    	jQuery.ajax({
    		
    		type: 'post',
    		dataType: 'json',
    		url: "<?= site_url() ?>/wp-admin/admin-ajax.php",
    		data: frmArr,
    		beforeSend: function() {
		      jQuery('#loader').removeClass('hidden');
		    },
		    complete: function() {
		      jQuery('#loader').addClass('hidden');
		    },
		    success: function(resp){
		    		    	
		    	var result_opt = '';   			    	
		    	if( resp.status === "error" ){
		    		result_opt += `<div class="card_wrap"><div class="card_inner"><div class="card_box_left">`;
		    		result_opt += `<h3 style="color:red;">${resp.msg}</h3>`;
		    		result_opt += `</div></div></div>`;	
		    		jQuery("#bolg_posts").html(result_opt);
		    	}
		    	if( resp.status === "success" ){
		    		var postlen = resp.data.length;	
		    		if( postlen >= 1 ){    	
		    			if( postlen >=4 ){
		    				jQuery('.load_btn').show();
		    			}else{
		    				jQuery('.load_btn').hide();
		    			}
		    			var bctr = 0;
		    			for( let l=0;l<postlen; l++){
			    			// console.log(resp.data[l].post_count)
			    			if(bctr >=4 ){
			    				result_opt += `<div class="card_wrap hidden"><div class="card_inner"><div class="card_box_left">`;
			    			}else{
			    				result_opt += `<div class="card_wrap "><div class="card_inner"><div class="card_box_left">`;
			    			}
			    			
							result_opt +=`<a href="${resp.data[l].url}" title="${resp.data[l].post_title}" class="blogimagestop">`;
							result_opt += `<img src="${resp.data[l].img_url}" /></a></div>`;											
							result_opt += `<div class="card_box_right"> <div class="card_box_Inner"><div class="date_time_wrap"><div class="date_content"></div></div>`;
							result_opt += `<h3 class="card_content">${resp.data[l].post_title}</h3><a href="${resp.data[l].url}">Read More</a>`;
							result_opt+=`</div></div></div></div>`;
							bctr++;
			    		}
			    		jQuery("#bolg_posts").html(result_opt);
			    		
			    	}
		    	}		    			    	
		    }
    	});	       	
  	}
  	isClear(){
  		jQuery("#blog_search").val('');  		
  		jQuery("#cats_32 option:selected").prop("selected", false)
  		jQuery("#cats_23 option:selected").prop("selected", false)
  		jQuery("#cats_14 option:selected").prop("selected", false)
		this.postFilter();			
	}	
}
obj = new FormSub();
</script>