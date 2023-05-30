<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

add_filter( 'wpcf7_validate_email', 'custom_name_validation_filteremail', 20, 2 );
add_filter( 'wpcf7_validate_email*', 'custom_name_validation_filteremail', 20, 2 );
function custom_name_validation_filteremail( $result, $tag ) {    
   $regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
   if ( "your-email" == $tag->name ) {
    $name = isset( $_POST[$tag->name] ) ? $_POST[$tag->name]  : ''; 
    if ( $name != "" && !preg_match($regex,$name) ) {
      $result->invalidate( $tag, "Please enter valid email address." );
    }
  } 
  return $result;

} 

add_filter( 'wpcf7_validate_text', 'custom_name_validation_filter', 20, 2 );
add_filter( 'wpcf7_validate_text*', 'custom_name_validation_filter', 20, 2 );
function custom_name_validation_filter( $result, $tag ) {     
  if ( "your-name" == $tag->name ) {
    $name = isset( $_POST[$tag->name] ) ? $_POST[$tag->name]  : ''; 
    if ( $name != "" && !preg_match("/^[a-zA-Z ]*$/",$name) ) {
      $result->invalidate( $tag, "The name entered is invalid." );
    }
  } 
  $regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
   if ( "your-email" == $tag->name ) {
    $name = isset( $_POST[$tag->name] ) ? $_POST[$tag->name]  : ''; 
    if ( $name != "" && !preg_match($regex,$name) ) {
      $result->invalidate( $tag, "Please enter valid email address." );
    }
  } 
  if ( "your-mobile" == $tag->name ) {
    $name = isset( $_POST[$tag->name] ) ? $_POST[$tag->name]  : ''; 
    if ( $name != "" && !preg_match("/^[0-9]+$/",$name) ) {
      $result->invalidate( $tag, "Please enter valid contact number." );
    }else if(strlen($name) < 8 || strlen($name) > 15 ){
        $result->invalidate( $tag, "Contact no should be 8 to 15 charactors only");
    }else{
        $numberlen = strlen($name);
        $zeros = substr_count($name,'0');
        if($numberlen == $zeros ){
            $result->invalidate( $tag, "Please enter valid contact number.");
        }             
    }   
  }   

  return $result;  
}
// add_action( 'phpmailer_init', 'my_phpmailer_smtp' );
// function my_phpmailer_smtp( $phpmailer ) {
//     $phpmailer->isSMTP();     
//     $phpmailer->Host = SMTP_HOST;
//     $phpmailer->SMTPAuth = SMTP_AUTH;
//     $phpmailer->Port = SMTP_PORT;
//     $phpmailer->Username = SMTP_USER;
//     $phpmailer->Password = SMTP_PASS;
//     $phpmailer->SMTPSecure = SMTP_SECURE;
//     $phpmailer->From = SMTP_FROM;
//     $phpmailer->FromName = SMTP_NAME;
// }

function srchpage( $query, $error = true ) {  
  if ( is_search() ) {  
    $query->is_search = false;  
    $query->query_vars[s] = false;  
    $query->query[s] = false;  
    if ( $error == true )  
      $query->is_404 = true;  
  }  
}  
add_action( 'parse_query', 'srchpage' );  
add_filter( 'get_search_form', create_function( '$a', "return null;" ) );


// breadcrumb
// function get_breadcrumb() {
//     global $post, $wp_query;
//      $page_title = $wp_query->post->post_title; 
//     echo '<a  href="'.home_url().'" rel="nofollow">Home </a> <span class="breadcrub-links">+</span> '.$page_title;
//     if (is_category() || is_single()) {
//         echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
//         the_category(' &bull; ');
//             if (is_single()) {
//                 echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
//                 the_title();
//             }
//     } elseif (is_page()) {
//         echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
//         $pages = get_pages(); 
//         echo "<ul>";
//         foreach ($pages as $page_data) {
//             if($page_data->post_title == $page_title){
//               continue;
//             }
//             $content = apply_filters('the_content', $page_data->post_content);            
//             echo '<li class="inactive"> <a  href="'.home_url().'/'.strtolower($page_data->post_title).'" rel="nofollow">'.$page_data->post_title."</a></li>"; 
            
            
//         }
//         echo "</ul>";

//         // echo the_title();
//     } elseif (is_search()) {
//         echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
//         echo '"<em>';
//         echo the_search_query();
//         echo '</em>"';
//     }
// }
// breadcrumb


// Search filter start
add_action('wp_ajax_nopriv_search_action', 'searchdata_fetch');
add_action('wp_ajax_search_action', 'searchdata_fetch');
function searchdata_fetch(){    
    $search = (!empty($_POST['search_university']) )? sanitize_text_field($_POST['search_university']) : ''; 
    $university = (!empty($_POST['university']) )? sanitize_text_field($_POST['university']) : '';


    
    // load more pending    
    $no_post = (!empty($_POST['page_no']) )? sanitize_text_field($_POST['page_no']) : '';   
    $no_post = (int)$no_post; 
    //pagination    
    if($no_post == 0 ){
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }else{
        $paged = $no_post;
    }
//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;       
$posts_per_page = 2;
    //pagination



    if( ( !empty($search) ) && ( !empty($university) )  ){                  
        $args = array(
            'post_type' => array('cb_universities'),            
            'posts_per_page' => $posts_per_page, //$no_post,
            'paged'=> $paged,
            'order' => 'ASC',
            's' => $search,
            'tax_query'=> array(
              array(
              'taxonomy'  => 'universities_type',
              'field'     => 'term_id',
              'terms'     => array($university)
              )
            )
        );
        $getPosts = new WP_Query($args);        
        // echo "<pre>";
        // print_r($getPosts);
        // echo "</pre>";
        // exit();
    }else if( ( !empty($search) )  ){
                                                  
        $args = array(
            'post_type' => array('cb_universities'),
            'posts_per_page' => $posts_per_page, //$no_post,
            'paged'=> $paged,
            'order' => 'ASC',
            's' => $search,

            // 'tax_query'=> array(
            //   // 'relation' => 'OR',
            //   array(
            //   'taxonomy'  => 'university_type',
            //   'field'     => 'term_id',
            //   'terms'     => array($university)
            //   )
            // )
        );
        $getPosts = new WP_Query($args);
        // echo "<pre>";
        // echo "single";
        // print_r($getPosts);
        // echo "</pre>";
        // exit();

    }else if( ( !empty($university) )  ){

       $args = array(
            'post_type' => array('cb_universities'),
            'posts_per_page' => $posts_per_page,//$no_post,
            'paged'=> $paged,
            'order' => 'ASC',            
            'tax_query'=> array(          
              array(
              'taxonomy'  => 'universities_type',
              'field'     => 'term_id',
              'terms'     => array($university)
              )
            )
        );
        $getPosts = new WP_Query($args);
        // echo "<pre>";
        // echo "single";
        // print_r($getPosts);
        // echo "</pre>";
        // exit();

        $temp_query = $wp_query;
        $wp_query = $getPosts;


  }else{
    $args = array(
        'post_type' => array('cb_universities'),
        'posts_per_page' => $no_post,
        'order' => 'ASC',            
    );
    $getPosts = new WP_Query($args);

        // $result = [
        // 'status' => 'error',        
        // 'msg' => ( 'No Data found!! ' ),        
        // ];
        // wp_send_json($result);
        // wp_die(); 
    }
   
    $post_count = $getPosts->post_count;

    

    if($post_count == 0) {
        $result = [
        'status' => 'error',        
        'msg' => ( 'No Result Found!!' ),        
        ];
        wp_send_json($result);
        wp_die();    
    }

    $posts = [];
     if ( $getPosts->have_posts() ) { 
          while ($getPosts->have_posts()) {
            $getPosts->the_post();                       
            $posts[] = array(
                'title' => get_the_title(),
                'permalink' => get_permalink(),                
                'thumbnail' => get_the_post_thumbnail_url(),
                'post_count' => $post_count,
                'cricos_code' => get_field('cricos_code'),
                'website_url' => get_field('website_url'),                 

            );
        }


$posts[] = array(
    'max_pages' => $getPosts->max_num_pages,
    'crr_page' => $paged,
);

$wp_query = $temp_query;
wp_reset_postdata();
}

    $result = [
        'status' => 'success',
        'response_type' => 'get posts',
        'msg' => 'results',        
        'data' => $posts,              
    ];



    // echo "<pre>";
    // print_r($result);
    // echo "</pre>";
    // exit();

    wp_send_json($result);
    wp_die();    
}

// search title

