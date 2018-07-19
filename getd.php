<?php
header("Content-type: text/html; charset=utf-8");
$host = 'localhost';
$dbName = 'senleh16_demo';
$userName = 'senleh16_root';
$password = 'bluehost';
$aim = 0;
$road = 0;
$juli = 100000;
$op = "no";
//连接数据库
$conn = mysqli_connect($host,$userName,$password,$dbName); //链接并返回
if(mysqli_connect_errno())
{
    die('连接失败'.mysqli_connect_errno());
}
//获取目的地坐标
$query3 = "select * from aim where go='{$_POST['aim']}'";
$result3 = mysqli_query($conn, $query3);
$row1 = mysqli_fetch_array($result3);
$road = $row1['xianlu'];
//获取用户坐标
$query4 = "select * from map where openid='{$_POST['openid']}'";
$result4 = mysqli_query($conn, $query4);
$row2 = mysqli_fetch_array($result4);
$aim = bccomp($row1['wei'],$row2['wei'],7);
$wei = $row2['wei'];
$jing = $_POST['longitude'];
$wei = $_POST['latitude'];
//更新乘客方向和路线
if($aim == 1)
{
    $query5 = "UPDATE map SET aim = '1',road = '$road',jing = '$jing',wei = '$wei' WHERE openid = '{$_POST['openid']}' ";
    $result5 = mysqli_query($conn, $query5);
}
elseif($aim==-1)
{
    $query6 = "UPDATE map SET aim = '-1',road = '$road',jing = '$jing',wei = '$wei' WHERE openid = '{$_POST['openid']}' ";
    $result6 = mysqli_query($conn, $query6);
}
else
{
    $query7 = "UPDATE map SET aim = '0',road = '$road',jing = '$jing',wei = '$wei' WHERE openid = '{$_POST['openid']}' ";
    $result7 = mysqli_query($conn, $query7);
}
//筛选最优司机
header("content-type:text/html;charset=utf-8");
header('Access-Control-Allow-Origin:*');
if($aim == '1')
    $query1 = "select * from mapd where aim=1 and wei<'$wei' and road = '$road'";
elseif($aim == "-1")
    $query1 = "select * from mapd where aim=-1 and wei>'$wei' and road = '$road'";
$result1 = mysqli_query($conn, $query1);
$t=mysqli_num_rows($result1); 
if (mysqli_num_rows($result1) <= 0)
{
    $query2 = "select * from mapd where aim='-$aim' and road = '$road'";
    $result2 = mysqli_query($conn, $query2);
    $row3['jing'] = 0.0000000001;
    $row3['wei'] = 0.0000000001;
    if($aim == 1)
    {
        $row3['jing'] = 112.9166811;
        $row3['wei'] = 27.9004213;
    }
    elseif($aim == -1)
    {
        if($road == 1)
        {
            $row3['jing'] = 112.9065846;
            $row3['wei'] = 27.9146352;
        }
        elseif($road == 2)
        {
            $row3['jing'] = 112.9127743;
            $row3['wei'] = 27.9133777;
        }
    }
    while($row = mysqli_fetch_array($result2))
    {
        $s = ($row3['jing'] - $row['jing'])*($row3['jing'] - $row['jing'])+($row3['wei'] - $row['wei'])*($row3['wei'] - $row['wei']);
        if($s<$juli)
        {
            $juli = $s;
            $op = $row['openid'];
        }
    }
    $query10 = "select * from mapd where openid = '$op'";
    $result10 = mysqli_query($conn, $query10);
    $row = mysqli_fetch_array($result10);
}
else
{
    while($row = mysqli_fetch_array($result1))
    {
        $s = ($row2['jing'] - $row['jing'])*($row2['jing'] - $row['jing'])+($row2['wei'] - $row['wei'])*($row2['wei'] - $row['wei']);
        if($s<$juli)
        {
            $juli = $s;
            $op = $row['openid'];
        }
    }
    $query9 = "select * from mapd where openid = '$op'";
    $result9 = mysqli_query($conn, $query9);
    $row = mysqli_fetch_array($result9);
}
//$row['wei'] = $row['wei']-0.00169;
//$row['jing'] = $row['jing']+0.00116;
echo json_encode($t);
?>
