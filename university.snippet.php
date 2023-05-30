<style>
  .pgactive{
    background: silver;
  }
</style>
<?php 
$postType = "cb_universities";
$tax = "universities_type";
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = 2;

$uniTerms = get_terms( array( 'post_types' => $postType, 'taxonomy' => $tax ) );
$getUni = new WP_Query(['post_type' => array('cb_universities'),'posts_per_page' => $posts_per_page /*12*/,'paged'=> 3 ,'order' => 'ASC',]);

$temp_query = $wp_query;
$wp_query = $getUni;

?>
<section class="university-filter-wrap">

  <div class="university-srch-rfm">
    <form method="post" id="uni_srch_frm">
      <ul class="search-from">
        <li>
          <input type="text" name="search_university" onchange="universitySearch()" placeholder="Search Universities" id="search_university">
        </li>
        <li>
          <select name="university" id="university" onchange="universitySearch()">
            <option value="">Select Country</option>
            <?php foreach($uniTerms as $uniTerm){ ?>
              <option value="<?php echo $uniTerm->term_id; ?>" ><?php echo $uniTerm->name; ?></option>
            <?php } ?>            
          </select>
        </li>
      </ul>            
    </form>    
  </div>

  <div class="university-wrap">
    <h2 class="search-text">Showing Results in <span class="cntry_name">All Countries</span></h2>
    <ul class="uni-list-grid" id="uni-lists">
      <?php while($getUni->have_posts() ):$getUni->the_post(); ?>
      <li>
        <div class="uni-item">
          <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
          <h4><?php the_title(); echo get_field('cricos_code'); ?> </h4>
          <a href="#." target="_blank"><?php echo get_field('website_url'); ?></a>
        </div>
      </li>
    <?php endwhile; ?> 
    <?php 
    echo  $getUni->max_num_pages;
    echo $paged;

    //  echo paginate_links(array(
    //     'total'   => $getUni->max_num_pages,
    //     'current' => $paged,
    // ));

$wp_query = $temp_query;
wp_reset_postdata();

    ?>    
    </ul>
    <ul id="pagination">
      
    </ul>
  </div>

<div class="load_button" id="load_button" style="display: none;" >
  <input type="hidden" id="number" name='number' value="12" data-tag="" />
  <input type="button" onclick="incrementValue()" value="Load More" />
</div>


</section>

<script>

// load more button

function incrementValue()
{
    value = parseInt(document.getElementById('number').value );        
    value = isNaN(value) ? 0 : value;
    value+= 6;
    document.getElementById('number').value = value;
    universitySearch();
}
 
function universitySearch(valuespg) {

  var search_university = jQuery('#search_university').val();
  var university = jQuery('#university').val(); 
  var university_txt = jQuery('#university :selected').text();
  

  if( university.length >= 1 ){ 
    jQuery(".cntry_name").text(university_txt)
  }else{
     jQuery(".cntry_name").text("All Countries")
  }

  var page_no = valuespg; //parseInt(document.getElementById('number').value);
  jQuery.ajax({
    type: 'post',
    dataType: 'json',
    url: "<?= site_url() ?>/wp-admin/admin-ajax.php",
    data: {
      'action': 'search_action',
      'search_university': search_university,
      'university': university,      
      'page_no' : page_no,
    },
    beforeSend: function() {
      jQuery('.loader-img').show();
    },
    complete: function() {
      jQuery('.loader-img').hide();
    },
    success: function(resp) {
      
      // pagination code
      var last = resp.data.slice(-1);
      var crrpage = parseInt(last[0].crr_page); // 1
      var lastpage = parseInt(last[0].max_pages); // 6
      // pagination code
      

      if (resp.status == 'error') {
        var output = '';        
        output += `<li class="search-error-msg" >${resp.msg}</li>`;        
        jQuery('#uni-lists').html(output);        
        jQuery("#load_button").css('display', 'none');
      }      
      if (resp.status == 'success') {       
        console.log(resp.data);
        var datas = resp.data.length;
        var pcount = resp.data[0].post_count;        
        if(pcount > 12 ){
          // var pg_tag = jQuery("#number").attr("data-tag");
          // var pgid = jQuery("#number").attr("id");
          // if( pg_tag <= pgid ){

          //   jQuery("#load_button").css('display', 'none');
          // }
          jQuery("#load_button").css('display', 'block');
        }else{
          jQuery("#load_button").css('display', 'none');
        }
        
        if (datas >= 1) {
          var output = '';
          for (i = 0; i < datas-1; i++) {
            output += `<li>`;
            output += `<div class="uni-item"> <img src="${resp.data[i].thumbnail}" alt="" />`;
            output += `<h4>${resp.data[i].title} ${resp.data[i].cricos_code} </h4>`;
            output += `<a href="${resp.data[i].website_url}" target="_blank">${resp.data[i].website_url}</a>`;
            output += `</div></li>`;                    
          }

// pagination
out_pgi = '';
out_pgi += `<li onclick="getPrev(${crrpage}, ${lastpage})">Prev</li>`;
for(let j = 1; j<= lastpage; j++){
    out_pgi += `<li onclick="giveMyPosts(${j})" class="${crrpage === j? 'pgactive' : '' }">`;
    out_pgi += `${j}`;
    out_pgi += `<li>`;       
}
out_pgi += `<li onclick="getNext(${crrpage}, ${lastpage})">Next</li>`;
jQuery("#pagination").html(out_pgi);
// pagination
          jQuery('#uni-lists').html(output);
          jQuery("#number").attr("data-tag",pcount);          
        }
      }
    }
  })
}
function giveMyPosts(values){
    // alert(values)
    jQuery("#pagination li:eq("+values+")").addClass('pgactive');    
    universitySearch(values)  
}
function getPrev(crrpage,lastpage){
  if(crrpage >1 ){    
    giveMyPosts(crrpage-1)
  }
}
function getNext(crrpage,lastpage){
  if( crrpage < lastpage ){
   giveMyPosts(crrpage+1)
  }
}
</script>