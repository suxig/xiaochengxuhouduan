<?php
header("Content-type: text/html; charset=utf-8");
$host = 'localhost';
$dbName = 'senleh16_demo';
$userName = 'senleh16_root';
$password = 'bluehost'; 
$openid = $_POST['openid'];
$bianhao = 0;
$juli = 100000;
$road = 0;
$wz1 = array
(
    'jing' => 0,
    'wei'=> 0,
    'uptime' => ''
);
$wz2 = array
(
    'jing' => 0,
    'wei'=> 0,
    'uptime' => ''

);
//连接数据库
$conn = mysqli_connect($host,$userName,$password,$dbName); //链接并返回
if(mysqli_connect_errno()){
    die('连接失败'.mysqli_connect_errno());
}
$query1 = "select * from mapd where openid = '$openid'";
$result1 = mysqli_query($conn, $query1);
if (mysqli_num_rows($result1) <= 0)
{
	 $query2 = "insert into mapd (openid,jing,wei)  values('$openid','{$_POST['longitude']}','{$_POST['latitude']}')";
     $result2=mysqli_query($conn, $query2);
}
else
{
    //$query3 = "insert into mapd (openid,jing,wei)  values('$openid','{$_POST['longitude']}','{$_POST['latitude']}')";
    $query3 = "UPDATE mapd SET jing = '{$_POST['longitude']}',wei = '{$_POST['latitude']}' WHERE openid = '$openid'";
     $result3=mysqli_query($conn, $query3); 
}
$query7 = "insert into driver (openid,jing,wei)  values('$openid','{$_POST['longitude']}','{$_POST['latitude']}')";
//$result7=mysqli_query($conn, $query7);
$query4 = "select * from driver where openid = '$openid' order by uptime desc";
$result4 = mysqli_query($conn, $query4);
$aim = 0;
$row = mysqli_fetch_assoc($result4);
//从路网一中匹配最近的点
$query5 = "select * from luwang1";
$result5 = mysqli_query($conn, $query5);
while($row2 = mysqli_fetch_array($result5))
{
    $s = ($row2['jing'] - $row['jing'])*($row2['jing'] - $row['jing'])+($row2['wei'] - $row['wei'])*($row2['wei'] - $row['wei']);
    if($s<$juli)
    {
        $juli = $s;
        $bianhao = $row2['bianhao'];
        $road = 1;
    }
}
//从路网二中匹配最近的点$row['wei']
$query6 = "select * from luwang2";
$result6 = mysqli_query($conn, $query6);
while($row2 = mysqli_fetch_array($result6))
{
    $s = ($row2['jing'] - $row['jing'])*($row2['jing'] - $row['jing'])+($row2['wei'] - $row['wei'])*($row2['wei'] - $row['wei']);
    if($s<$juli)
    {
        $juli = $s;
        $bianhao = $row2['bianhao'];
        $road = 2;
    }
}
//查找last和now
$query8 = "select * from mapd where openid = '$openid'";
$result8 = mysqli_query($conn, $query8);
$row1 = mysqli_fetch_array($result8);
$now=$row1['now']; 
if($row1['last']==null)
{
    $query9 = "UPDATE mapd SET last='$bianhao',now='$bianhao',xianlu='$road' WHERE openid = '$openid'";
     $result9=mysqli_query($conn, $query9); 
}
elseif($row1['now']!=$bianhao)
{
    if($row1['now']>$bianhao)
    $aim = 1;
    else 
    $aim = -1;
    $query10 = "UPDATE mapd SET last='$now',now='$bianhao',xianlu='$road',aim='$aim' WHERE openid = '$openid'";
     $result10=mysqli_query($conn, $query10); 
}
echo json_encode($row);
?>