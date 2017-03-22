<?php
/*
  Yun Parse 云解析,QQ:157503886
  请在下面地址查询统计情况。
  http://120.27.155.106/login
*/

//文件名称
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
// 网站根目录
define('FCPATH', str_replace("\\", "/", str_replace(SELF, '', __FILE__)));
//加载配置文件
require_once FCPATH.'sys.php';

//接收参数
$vid = $url = $_GET['url'];
$hd = empty($_GET['hd']) ? VOD_HD : $_GET['hd'];
$up = (int)$_GET['up'];
if(!empty($_GET['wap'])) $wap = $_GET['wap'];

//判断http地址模式
$arr = explode('~',$vid);
$vid = $arr[0];
$type = !empty($arr[1])?$arr[1]:'';

//组装URL参数
$param = 'url='.$vid.'&type='.$type.'&hd='.$hd.'&wap='.$wap;

//判断缓存是否存在
$cache=0;
$filemd5 = FCPATH.'cache/'.md5($param.USER_TOKEN);
if($up==0 && file_exists($filemd5)){
     $json = file_get_contents($filemd5);
	 $arr = json_decode($json,1);
	 if($ctime > time()) $cache++;
}
if($cache==0){
     $json = get_url(API_URL.'?uid='.USER_ID.'&up='.$up.'&token='.USER_TOKEN.'&'.$param);
     $arr = json_decode($json,1);
	 if($arr['success']==0){
		 get_json(array('msg'=>$arr['msg']));
	 }else{
	     file_put_contents($filemd5,$json);
		 if($arr['ext']=='m3u8_list'){
			 $filem3u8 = FCPATH.'cache/'.md5($vid.USER_TOKEN).'.m3u8';
	         file_put_contents($filem3u8,base64_decode($vodurl));
			 $arr['url'] = 'http://'.$_SERVER['HTTP_HOST'].WEB_PATH.'cache/'.md5($vid.USER_TOKEN).'.m3u8';
		 }
	 }
}
get_json($arr);
