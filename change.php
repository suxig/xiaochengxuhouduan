<?php
header("Content-type: text/html; charset=utf-8");
$host = 'localhost';
$dbName = 'senleh16_demo';
$userName = 'senleh16_root';
$password = 'bluehost';
//获取id
function getopenid(){
$js_code = $_POST['code'];
$appid = 'wxe7fdf205417d46a7';
$appsecret = 'd35742db586a7bbc5dd11a6862e506b5';
$curl = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
$curl = sprintf($curl,$appid,$appsecret,$js_code);
$data = array(
	'openid'=>'OPENID',
      'session_key'=> 'SESSIONKEY',
      'unionid'=> 'UNIONID'
);
       $data = @http_build_query($data);  //把参数转换成URL数据  
  
       $aContext = array('http' => array('method' => 'POST',  
  
                                                                 'header'  => 'Content-type: application/x-www-form-urlencoded',  
  
                                                                 'content' => $data ));  
  
       $cxContext  = stream_context_create($aContext);  
  
       $sUrl = $curl; //此处必须为完整路径  
  
       $d = @file_get_contents($sUrl,false,$cxContext);   
  return $d;
}

//连接数据库
$conn = mysqli_connect($host,$userName,$password,$dbName); //链接并返回
if(mysqli_connect_errno()){
    die('连接失败'.mysqli_connect_errno());
}
$result = getopenid();
$arr = (array)json_decode( $result );
$a = $arr['openid'];
$query = "select * from user1 where openid = '$a'";
$result1 = mysqli_query($conn, $query);
if (mysqli_num_rows($result1) >= 0)
{    
     $query1 = "UPDATE user1 SET passenger = 0 WHERE openid = '$a'";
     $result2=mysqli_query($conn, $query1);
}
header("content-type:text/html;charset=utf-8");  
header('Access-Control-Allow-Origin:*');//解决跨域问题  
$result = array(  
    'passenger' => 0,   
);  
echo json_encode($result);

?>