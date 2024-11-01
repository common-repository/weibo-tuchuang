<?php
/* 
Plugin Name: 微博图床
Plugin URI: http://shuang.ca/2013/12/08/wordpress-tu-pian-wai-lian-cha-jian-wei-bo-tu-chuang/
Description:  在文章发布页面增加微博上传功能，使用微博作为图床
Version: 1.2
Author: 带头盔滴衰锅
Author URI: http://shuang.ca/
License: License: GPLv2 or later
*/
/*Copyright 2013 Shuang.Ca (email: ylqjgm@gmail.com )

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License along
  with this program; if not, write to the Free Software Foundation, Inc.,
  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
define('WEIBOTUCHUANG_VERSION', '1.2');
define('APP_KEY', '1151579785');
define('APP_SECRET', 'a7563b1a84330cf3675dd16e1b8dbc74');
define('CALL_BACK', admin_url('options-general.php?page=weibo-tuchuang&t=callback'));
define('PLUGIN_URL', plugins_url('', __FILE__));
define('PLUGIN_DIR', plugin_dir_path(__FILE__));

add_action('after_wp_tiny_mce', 'weibo_tuchuang_script');
function weibo_tuchuang_script(){
?>
<script type="text/javascript" src="<?php echo plugins_url('weibo_tuchuang.js', __FILE__); ?>"></script>
<?php
}

add_action('submitpost_box', 'weibo_tuchuang_post_box');
function weibo_tuchuang_post_box(){
    add_meta_box('weibo_tuchuang_div', __('微博图床'), 'weibo_tuchuang_post_html', 'post', 'side');
}
add_action('submitpost_box', 'weibo_tuchuang_style');
function weibo_tuchuang_style(){
	wp_enqueue_style('weibo_tuchuang_style', plugins_url('weibo_tuchuang.css', __FILE__));
}

function weibo_tuchuang_post_html(){
    echo '<script>var weibo_tuchuang_post_url="' . admin_url('options-general.php?page=weibo-tuchuang&t=upload') . '";</script>';
    echo '<div id="weibo_tuchuang_post">';
    _e("将图片拖拽到此区域上传", 'weibo_tuchuang_textdomain' );
    echo '</div><input type="file" id="weibo_tuchuang_input" />';
}

if(isset($_GET['t'])){
    if($_GET['t'] == 'callback'){
        if(isset($_REQUEST['access_token'])){
            update_option('weibo_tuchuang', array('access_token' => $_REQUEST['access_token'], 'expires_in' => $_REQUEST['expires_in'], 'access_time' => time()));
        }
    }
    
    if($_GET['t'] == 'upload'){
        $keys = get_settings('weibo_tuchuang');
        if(!isset($keys['access_token']) || ($keys['access_time'] + $keys['expires_in']) <= time()){
            echo '未获取授权或授权已过期，请更新授权！';
        }else{
            if(!class_exists('SaeTClientV2')){
                require_once PLUGIN_DIR . 'weiboOAuth.php';
            }
            $o = new SaeTClientV2(APP_KEY, APP_SECRET, $keys['access_token']);
            $r = $o->upload(date("l dS \of F Y h:i:s A"), $_FILES['weibo_tuchuang']['tmp_name']);
            echo '<a href="' . $r['original_pic'] . '" target="_blank" title="' . $_FILES['weibo_tuchuang']['name'] . '"><img src="' . $r['original_pic'] . '" alt="' . $_FILES['weibo_tuchuang']['name'] . '" /></a>';
            exit;
        }
    }
}

function weibo_tuchuang_menu(){
    add_options_page('微博图床', '微博图床', 'manage_options', 'weibo-tuchuang', 'weibo_tuchuang_options');
}
add_action('admin_menu', 'weibo_tuchuang_menu');

function weibo_tuchuang_options(){
    $keys = get_settings('weibo_tuchuang');
    $url = 'http://img.shuang.ca/redirect_uri.php?uri=' . CALL_BACK;
?>
<div class="wrap">
    <h2>微博图床设置:</h2>
    当前版本：<?php _e(WEIBOTUCHUANG_VERSION, 'weibo_tuchuang') ?> <a href="http://shuang.ca/2013/12/08/wordpress-tu-pian-wai-lian-cha-jian-wei-bo-tu-chuang/" title="微博图床官方主页">带头盔滴衰锅</a>出品。
    <p>获取授权：<a href="<?=$url?>" title="点击按钮获取授权"><img src="http://www.sinaimg.cn/blog/developer/wiki/240.png" alt="点击按钮获取授权" /></a>
<?php if(isset($keys['access_token'])){
    echo '，授权过期时间：' . date('Y-m-d H:m:i', $keys['access_time'] + $keys['expires_in']);
}?>
    </p>
</div>
<?php
}
?>