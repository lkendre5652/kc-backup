<style>
    .nwsactive{
       /* background: #4161ee;
        color: #fff;*/
    }

    /*.myact {
        background: #4161ee !important;
        color: #fff !important;
    }*/
</style>

<?php 
    $txmny = 'news-events_type';
    $psttyp = 'cb_newsevents';
    $nwsTerms = get_terms( ['post_type' => $psttyp, 'taxonomy' => $txmny] ); 
    $nwCrt = 0;      
?>
<div class="news_event_section_filter card_section_filter">
	<div class="news_filter_left"> 
		<ul class="filter_tabs"> 
        <?php foreach($nwsTerms as $nwsTerm ){ ?>           
			<li onclick="getMyNews(this)" id="<?php echo $nwsTerm->term_id; ?>" class="<?php echo ( $nwCrt == 0 )? "active" : "" ?>"><span><?php echo $nwsTerm->name; ?></span></li>
        <?php $nwCrt++; } ?>			
		</ul>
	</div>
	<div class="news_filter_right"> 		
        <input type="text" name="searchp" class="search-box" id="searchp" placeholder="Search News" onkeydown="newEventFilter()" />      
        <!-- <span class="loaders hidden">loading...</span> -->
	</div>
</div>
<div class="card_section">
    <div class="card_outer" id="new_search_div">
      <?php 
        $tstQuery = new Wp_Query([
            'post_type' => 'cb_newsevents',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'order' => 'DESC',
            'tax_query' => array(
                array(
                    'taxonomy' => $txmny,
                    'field' => 'term_id',
                    'terms' => array(10),
                )
            )

    ]);
        $counter = 0;
        $nwspost = $tstQuery->post_count;
        while($tstQuery->have_posts()):$tstQuery->the_post();    
      ?> 
       <div class="card_wrap <?php echo ($counter >= 3 )? "hidden": ""; ?>">
            <div class="card_inner">
                <div class="card_box_left">
                    <img src="<?php echo the_post_thumbnail_url(); ?>" />
                </div>
                <div class="card_box_right">
                    <div class="card_box_Inner">
                        <div class="date_time_wrap">
                            <div class="date_content"><?php the_field('news-events-date'); ?></div>
                        </div>
                        <h3 class="card_content"><?php the_title(); ?></h3>
                        <a href="<?php the_permalink(); ?>">Read More</a>
                    </div>
                </div>
            </div>
      </div>
      <?php 
        $counter++;
        endwhile; ?>
    </div>
    <div class="button_wraploadmore <?php echo ( $nwspost < 3 )? "hidden" : ""; ?>" id="load_button" >
	    <button onclick="loadMore()" data-ctr="" class="elementor-button-link elementor-button elementor-size-sm elementor-animation-float" role="button">
			<span class="elementor-button-content-wrapper">
				<span class="elementor-button-text">Load More</span>
			</span>
		</button>      
	</div>
</div>
<script>

function loadMore(){ 
    var noofpost = jQuery("#new_search_div .card_wrap").length;    
    for(let l= 0; l<=noofpost; l++){
        if(l >= 3){
            jQuery('#new_search_div .card_wrap:eq('+l+')').removeClass('hidden');
            jQuery("#load_button").addClass('hidden');
        }      
    }      
}

function getMyNews(newsid){    
    jQuery('.filter_tabs li').removeClass('active');    
    jQuery(newsid).addClass('active');
    var Title = jQuery(newsid).find('span').text();
    jQuery("#searchp").attr('placeholder','Search '+Title);    
    jQuery('.myact').removeClass('myact');
    jQuery(newsid).find('span').addClass('myact');
    // console.log(jQuery(newsid).html());
    newEventFilter(newsid = parseInt(newsid.id));
}

function newEventFilter(newsid,pgum){

    var newsval = jQuery ('.active').attr('id'); 

    var newsidvalue  = (newsid == "" || newsid === undefined)? newsval : newsid ;    
    var search_text = jQuery('#searchp').val();    
    jQuery.ajax({
        type: 'post',
        dataType: 'json',
        url: "<?= site_url() ?>/wp-admin/admin-ajax.php",
        data: {
            'action': 'search_action_news',
            'search_text': search_text,
            'newsid': newsidvalue,                        
        },
        beforeSend: function() {
            jQuery('.loaders').removeClass('hidden');
        },
        complete: function() {
            jQuery('.loaders').addClass('hidden');
        },
        success: function(resp) {            
            if (resp.status == 'error') {
                var output = '';                
                output += `<li class="search-error-msg" >${resp.msg}</li>`;                
                jQuery('#new_search_div').html(output);                
            }
            if (resp.status == 'success') {
                var datas = resp.data.length;
                var pcount = resp.data[0].post_count;
                if (datas >= 1) {
                    var output = '';
                    for (i = 0; i < datas; i++) {                        
                        if(i >= 3 ){
                            output += `<div class="card_wrap hidden"><div class="card_inner"><div class="card_box_left">`;                            
                        }else{
                            output += `<div class="card_wrap "><div class="card_inner"><div class="card_box_left">`;
                        }
                        if (resp.data[i].thumbnail.length >= 1) {
                        output += `<img src="${resp.data[i].thumbnail}" />`;
                        } else { 
                        output += `<img src="<?= site_url() ?>/wp-content/uploads/2023/04/not_found.jpg" />`;
                        }                                        
                        output +=`
                        </div>
                        <div class="card_box_right">
                        <div class="card_box_Inner">
                        <div class="date_time_wrap">
                        <div class="date_content">${resp.data[i].newseventsdate}</div>
                        </div>
                        <h3 class="card_content">${resp.data[i].title}</h3>`;
                        if( resp.data[i].cat_id != 10 ){ 
                            output += `<a onclick="obj.showEnqForm('book-counselling')" class="ast-custom-button-link" href="#book-counselling">Register Now</a>`;
                        }else{
                            output += `<a href="${resp.data[i].permalink}">Read More</a>`;
                        }                       
                        output += `</div></div></div></div>`;                       
                    }
                    jQuery('#new_search_div').html(output);
                    var noofpost = jQuery("#new_search_div .card_wrap").length;
                    if( noofpost >= 3 ){
                        jQuery("#load_button").removeClass('hidden');
                    }else{
                        jQuery("#load_button").addClass('hidden');
                    }
                    
                }
            }
        }
    })
}  
</script>