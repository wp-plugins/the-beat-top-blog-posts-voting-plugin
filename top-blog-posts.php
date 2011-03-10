<?php
/*
Plugin Name: The Beat Top Blog Posts Voting Plugin
Description: Front page exposure for your posts on thatMLMbeat.com ranked by votes (beats) & adds posts into RSS feed which displays in plugin users WP Dashboard!
Version: 1.2.3
Author: George Fourie
Author URI: http://thatmlmbeat.com/
*/ 
define("WEBSITE_URL","http://thatmlmbeat.com/");

function tbpv_get_post_content(){
	if(isset($_POST["thatmlmbeat_get_voting_post"])){
		query_posts(array( 'p' => $_POST["thatmlmbeat_get_voting_post"] ));
		if ( have_posts() ){
			while ( have_posts() ){
				include_once("wp-includes/pluggable.php");
				the_post();
				echo serialize( array( "title"=>the_title_attribute(array("echo"=>0)), "content"=>get_the_excerpt(), "permalink"=>get_permalink(), "time"=>strtotime(get_the_time("Y-m-d")) ) );
			}
		}
		exit;
	}
}
add_action( 'init', 'tbpv_get_post_content' );

if( isset($_GET["tbpv_id"]) && isset($_GET["tbpv_username"]) && isset($_GET["tbpv_domain"]) && isset($_GET["tbpv_login"]) ){ ?>
	<script type="text/javascript">
		document.domain = '<?php echo $_GET["tbpv_domain"]; ?>';
		function login_popup(){
			location1 = location.href.split("?",1);
			window.open("http://thatmlmbeat.com/wp-voting-login.php?redirect_to="+location1[0]+"&tbpv_affiliate=<?php echo $_GET["tbpv_affiliate"]; ?>","","menubar=0,resizable=1,status=1,toolbar=0,location=0");
		}
		try{
			parent.show_login_form(<?php echo $_GET["tbpv_id"]; ?>, location.href, '<?php echo $_GET["tbpv_affiliate"]; ?>');
		}catch(err){
			login_popup();
		}
		location.href = '<?php echo WEBSITE_URL; ?>top_blog_posts.php?tbpv_id=<?php echo $_GET["tbpv_id"]; ?>&tbpv_username=<?php echo $_GET["tbpv_username"]; ?>&tbpv_domain=<?php echo $_GET["tbpv_domain"]; ?>&tbpv_affiliate=<?php echo urlencode($_GET["tbpv_affiliate"]); ?>';
	</script>
<?php
	exit;
}

if(isset($_GET["tbpv_action"]) && $_GET["tbpv_action"] == "close_loginbox"){ ?>
	<script type="text/javascript">
		try{
			parent.jQuery.colorbox.close();
		}catch(err){
			window.close();
		}
	</script>
<?php
	exit;
}

function top_blog_posts_add_admin_menu(){
	global $wpdb;
	
	//if ( !is_site_admin() )
		//return false;
	
	add_submenu_page( 'options-general.php', 'thatMLMbeat Voting Plugin Settings', 'thatMLMbeat Voting Plugin Settings', 9, "top_blog_posts_admin", "top_blog_posts_admin" );
}
add_action( 'admin_menu', 'top_blog_posts_add_admin_menu' );

function top_blog_posts_admin(){
	$settings_saved = false;
	if( $_POST && isset($_POST["thatmlmbeat_username"]) ){
		update_option( "thatmlmbeat_username", $_POST["thatmlmbeat_username"] );
		$settings_saved = true;
	}
	if( $_POST && isset($_POST["thatmlmbeat_affiliate_link"]) ){
		update_option( "thatmlmbeat_affiliate_link", $_POST["thatmlmbeat_affiliate_link"] );
		$settings_saved = true;
	}
	if( $_POST && isset($_POST["thatmlmbeat_button_allignment"]) ){
		update_option( "thatmlmbeat_button_allignment", $_POST["thatmlmbeat_button_allignment"] );
		$settings_saved = true;
	}
	$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
	$thatmlmbeat_affiliate_link = get_option( "thatmlmbeat_affiliate_link" );
	$thatmlmbeat_button_allignment = get_option( "thatmlmbeat_button_allignment" ); ?>
	<div class="wrap">
		<h2>thatMLMbeat.com Top Blog Posts Voting Settings</h2>
		<br />
<?php if($settings_saved){ ?>
		<div class="updated below-h2" id="message"><p>Settings saved.</p></div>
<?php } ?>
		<form action="" method="post">
		<table class="widefat fixed" cellspacing="0" style="width:auto;">
			<thead>
				<tr class="thead">
					<th scope="col" colspan="2">thatMLMbeat.com Setting</th>
				</tr>
			</thead>
			<tbody class="list:user user-list">
				<tr>
					<td>thatMLMbeat Username:</td>
					<td><input type="text" name="thatmlmbeat_username" value="<?php echo $thatmlmbeat_username; ?>" /></td>
				</tr>
				<tr>
					<td>thatMLMbeat Affiliate Referral Username:</td>
					<td>
						<input type="text" name="thatmlmbeat_affiliate_link" value="<?php echo $thatmlmbeat_affiliate_link; ?>" />
						<div style="font-size:10px; color:#A5A5A5;">http://thatmlmbeat.com/?ref=georgefourie-97<br />here "georgefourie-97" is referral username</div>
					</td>
				</tr>
				<tr>
					<td>Button alignemnt:</td>
					<td>
						<select name="thatmlmbeat_button_allignment" id="thatmlmbeat_button_allignment" style="width:70px;"><option value="left">Left</option><option value="right">Right</option></select>
		<?php if($thatmlmbeat_button_allignment!=""){ ?>
						<script type="text/javascript">
							document.getElementById("thatmlmbeat_button_allignment").value = '<?php echo $thatmlmbeat_button_allignment; ?>';
						</script>
		<?php } ?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Save" /></td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
<?php
}

//add_action('wp_ajax_tbpv__vote', 'tbpv__vote');
function tbpv_enable_jquery(){
	//not load for admin page
	if (!is_admin()) {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'mlmbeat-voting-colorbox-js', WP_PLUGIN_URL .'/the-beat-top-blog-posts-voting-plugin/colorbox/jquery.colorbox-min.js' );
		wp_enqueue_script( 'mlmbeat-voting-js', WP_PLUGIN_URL .'/the-beat-top-blog-posts-voting-plugin/general.js?domain='.$_SERVER['HTTP_HOST'] );
		wp_enqueue_style( 'mlmbeat-voting-colorbox-css', WP_PLUGIN_URL .'/the-beat-top-blog-posts-voting-plugin/colorbox/colorbox.css');
	}
}
add_action('init', 'tbpv_enable_jquery');

function tbpv_the_content_filter($content){
	global $tbpv_but_dis;
	if(!is_array($tbpv_but_dis))
		$tbpv_but_dis = array();
	if( !(is_single() || is_home() || is_category() || is_archive() || is_front_page() || is_author()) )
		return $content;
	//if(is_page() || is_feed() || is_admin())
		//return $content;
	/*Uncomment if excerpts filter added*/
	//if( in_array(get_the_ID(), $tbpv_but_dis) )
		//return $content;
	$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
	$thatmlmbeat_affiliate_link = get_option( "thatmlmbeat_affiliate_link" );
	$thatmlmbeat_button_allignment = get_option( "thatmlmbeat_button_allignment" );
	if($thatmlmbeat_button_allignment=="")
		$thatmlmbeat_button_allignment = "left";
	if( !empty($thatmlmbeat_username) && get_the_ID()!="" ){
		$tbpv_but_dis[] = get_the_ID();
		$html = '';
		$html = '<div style="float:'.$thatmlmbeat_button_allignment.';"><iframe id="mlmbeat_tbpv" src="'.WEBSITE_URL.'top_blog_posts.php?tbpv_id='.get_the_ID().'&tbpv_username='.$thatmlmbeat_username.'&tbpv_domain='.$_SERVER['HTTP_HOST'];
		if(!empty($thatmlmbeat_affiliate_link))
			$html .= '&tbpv_affiliate='.urlencode($thatmlmbeat_affiliate_link).'&ref='.urlencode($thatmlmbeat_affiliate_link);
		$html .= '" frameborder="0" width="58" height="64" scrolling="no"></iframe></div>';
		/*echo '<script type="text/javascript">var tbpv_siteurl = "'.WEBSITE_URL.'";</script>';*/
	}
	return $html.$content;
}
add_filter('the_content', 'tbpv_the_content_filter');
/*function tbpv_remove_filter($content) {
	if(!is_feed()){
		remove_action('the_content', 'tbpv_the_content_filter');
	}
	return $content;
}
add_filter('get_the_excerpt', 'tbpv_remove_filter', 9);*/
//add_filter('the_excerpt', 'tbpv_the_content_filter');

function tbpv_dashboard_widget_function() {
	$rss = @fetch_feed( WEBSITE_URL.'activity/feed/topblogposts/' );
	if ( is_wp_error($rss) ) {
		if ( is_admin() || current_user_can('manage_options') ) {
			echo '<div class="rss-widget"><p>';
			printf(__('<strong>RSS Error</strong>: %s'), $rss->get_error_message());
			echo '</p></div>';
		}
	/*} elseif ( !$rss->get_item_quantity() ) {
		$rss->__destruct();
		unset($rss);
		return false;*/
	} else {
		echo '<div class="rss-widget"><ul>';
		//wp_widget_rss_output( $rss, $widgets['top_blog_posts_dashboard_widget'] );
		$maxitems = $rss->get_item_quantity(5);
		$rss_items = $rss->get_items(0, $maxitems);
		if($maxitems == 0)
			echo '<li>No Posts found.</li>';
		else{
    	foreach( $rss_items as $item ){
				echo '<li><a class="rsswidget" href="'.$item->get_permalink().'" title="Posted '.$item->get_date('j F Y | g:i a').'">'.$item->get_title().'</a><span class="rss-date">'.$item->get_date('F j, Y').'</span><div class="rssSummary">'.$item->get_description().'</div></li>';
			}
		}
		echo '</ul></div>';
		$rss->__destruct();
		unset($rss);
	}
}
function tbpv_add_dashboard_widgets() {
	wp_add_dashboard_widget('top_blog_posts_dashboard_widget', 'thatMLMbeat Top Blog Posts', 'tbpv_dashboard_widget_function');	
}
add_action('wp_dashboard_setup', 'tbpv_add_dashboard_widgets' );
?>
