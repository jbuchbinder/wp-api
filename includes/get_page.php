<?php
@include_once('JSON.php');
@include_once ('DEV.php');
class get_page
{
	static $return_slug_links = true;

	function __construct() 
	{
		add_filter('rewrite_rules_array','get_page::insertRules');
		add_filter('query_vars','get_page::insertQueryVars');
		add_action('parse_query','get_page::insertParseQuery');	
	}
	static function page_info($dev,$comm,$con,$page_name)
	{
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->posts.' AS P WHERE P.post_type="page" AND P.post_name =\'' . addslashes($page_name) . '\' LIMIT 1;';
		$obj = $wpdb->get_results($sql);
		$check_err = true;
		$err_msg = '';
		$status = 'ok';
		if (!$obj)
		{
			$check_err = false;
			$err_msg = 'the query can not connect to database';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg,
			);
			$json = new Services_JSON();
  			$encode = $json->encode($info);
  			if($dev == 1)
  			{
  				$dev = new Dev();
  			 	$output = $dev-> json_format($encode);
  			 	print($output);
  			}
  			if ($dev != 1)
  			{
				print ($encode);
  			}
		}
		if(empty($obj))
		{
			$check_err = false;
			$err_msg = 'the table is empty';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg
			);
			$json = new Services_JSON();
  			$encode = $json->encode($info);
  			if($dev == 1)
  			{
  				$dev = new Dev();
  			 	$output = $dev-> json_format($encode);
  			 	print($output);
  			}
  			if ($dev != 1)
  			{
				print ($encode);
  			}
		}
		if($check_err)
		{
			if(empty($type))
			{
				foreach($obj as $key => $value)
				{
					$author = get_page::return_author($value->post_author);
					$meta = get_page::return_meta($value->ID);
					$exp = explode("\n",$value->post_content);
					if(empty($value->post_excerpt))
					{
						if(count($exp) > 1)
						{
							$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
							$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.( get_page::$return_slug_links ? get_site_url() . '/' . $value->post_name .'/' : $value->guid ).'" class="readmore"> Read more <span class="meta-nav">&rarr;</span></a>';
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
						}
						else
						{
							$value->post_excerpt = $value->post_content;
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
						}	
					}
					else $value->post_excerpt = $value->post_excerpt.' '.'<a href="'.( get_page::$return_slug_links ? get_site_url() . '/' . $value->post_name . '/' : $value->guid ).'" class="readmore"> Read more <span class="meta-nav">&rarr;</span></a>';
					if(function_exists('get_fields')) {
						$cfields = get_fields($value->ID);
					} else {
						$cfields = array();
					}
					if(($comm == null and $con == null) or ($comm == 0 and $con == 0))
					{
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'custom_fields' => $cfields,
						'author' => $author,
						'meta' => $meta,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status
						);
					}
					if($con == 1 or ($con == 1 and $comm == 0))
					{
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'content' => $value->post_content,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'author' => $author,
						'meta' => $meta,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status
						);					
					}
					if($comm == 1 or ($comm == 1 and $con == 0))
					{
						$com = get_page::return_comment($value->ID);
						if($com == null)
						{
							$com = array();
						}		
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'author' => $author,
						'meta' => $meta,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status,
						'comments' => $com
						);
					}
					if($comm == 1 and $con == 1)
					{
						$com = get_page::return_comment($value->ID);
						if($com == null)
						{
							$com = array();
						}
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'content' => $value->post_content,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'author' => $author,
						'meta' => $meta,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status,
						'comments' => $com
						);					
					}
					$page = $obj[$key];
				}
			} // empty type
			if(empty($page))
			{
				$page = array();
			}
			$info = array(
			'status' => $status,
			'page' => $page
			);
 			 $json = new Services_JSON();
  			 $encode = $json->encode($info);
  			 if($dev == 1)
  			 {
  			 	$dev = new Dev();
  			 	$output = $dev-> json_format($encode);
  			 	print($output);
  			 }
  			 if($dev != 1)
  			 {
  			 	print ($encode);
  			 }
		} // check_err
	}
	static function return_comment($id)
	{
		global $wpdb;
		$sqlc = 'SELECT DISTINCT * FROM '.$wpdb->comments.' AS COMM
                 WHERE COMM.comment_post_id="'.$id.'" AND COMM.comment_approved = 1';
		$obj = $wpdb->get_results($sqlc);
		foreach ($obj as $key => $comment)
		{
			$obj[$key] = array(
			'id' => $comment->comment_ID,
			'author' => $comment->comment_author,
			'author_url' => $comment->comment_author_url,
			'parent' => $comment->comment_parent,
			'date' => $comment->comment_date,
			'content' => $comment->comment_content,
			'gravatar' => get_page::get_gravatar($comment->comment_author_email)
			);
			$comments[] = $obj[$key];
		}		
		return $comments;
	}                       	
	static function return_author($id)
	{
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->users;
		$obj = $wpdb->get_results($sql);
		foreach($obj as $key => $value)
			{
				if($id == $value->ID)
				{
                                $avatar = array();
                                if (get_user_meta($value->ID, 'avatar', true) != '') {
		                    $sqli = 'SELECT DISTINCT * FROM '.$wpdb->postmeta.' AS PM
                                     WHERE PM.post_id="'.get_user_meta($value->ID, 'avatar', true).'"';
		                    $iobj = $wpdb->get_results($sqli);
		                    foreach ($iobj as $k => $v) {
                                        $avatar[$v->meta_key] = $v->meta_value;
                                    }
                                }
				$obj[$key] = array(
				'id' => $value->ID,
				'slug' => $value->user_nicename,
				'name' => $value->display_name,
				'first_name' => get_user_meta($value->ID, 'first_name', true),
				'last_name' => get_user_meta($value->ID, 'last_name', true),
				'nickname' => $value->user_nicename,
				'url' => $value->user_url,
				'description' => get_user_meta($value->ID, 'description', true),
				'avatar' => $avatar,
				'gravatar' => get_page::get_gravatar($value->user_email)
				);
				$authors[] = $obj[$key];
				return $authors;
				}
			}
			
	}
	static function return_meta($id)
	{
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->postmeta.' WHERE post_id='.((int)$id).';';
		$obj = $wpdb->get_results($sql);
		$meta = array();
		foreach($obj as $key => $value) {
			$meta[$value->meta_key] = $value->meta_value;
		}
		if (!empty($meta['_thumbnail_id'])) {
			// Pull thumbnail info if it exists, grab attachment metadata
			$sql = 'SELECT * FROM '.$wpdb->postmeta.' WHERE post_id='.((int)$meta['_thumbnail_id']).';';
			$obj = $wpdb->get_results($sql);
			foreach($obj as $key => $value) {
				if (substr($value->meta_value, 0, 2) === 'a:') {
					$meta[$value->meta_key] = unserialize($value->meta_value);
				} else { 
					$meta[$value->meta_key] = $value->meta_value;
				}
			}
		}
		return $meta;			
	}
	static function get_gravatar( $email, $s = 100, $d = 'mm', $r = 'g', $img = false, $atts = array() ) 
	{
    		$url = 'http://www.gravatar.com/avatar/';
    		$url .= md5( strtolower( trim( $email ) ) );
    		$url .= "?s=$s&d=$d&r=$r";
    		if ( $img ) 
    		{
        		$url = '<img src="' . $url . '"';
        		foreach ( $atts as $key => $val )
        		    	$url .= ' ' . $key . '="' . $val . '"';
       			$url .= ' />';
    		}
    		return $url;
	}
	static  function insertRules($rules){
		$newrules = array();
		$newrules['redirect/url/(.+)$']='index.php?wpapi=get_page&dev&id&page&comment&content';
		return $newrules+$rules;
	}
	static function insertQueryVars($vars){
		array_push($vars, 'wpapi','dev','id','comment','content','page');
		return $vars;
	}
	static function insertParseQuery($query)
	{
		if(!empty($query->query_vars['wpapi']) and $query->query_vars['wpapi'] == 'get_page')
		{
			$dev = $_GET['dev'];
			$id = $_GET['id'];
			$page = $_GET['page'];
			$comm = $_GET['comment'];
			$con = $_GET['content'];
			get_page::page_info($dev,$comm,$con,$page);
			header('Content-type: text/plain');
			exit();	
		}
	}
}
?>
