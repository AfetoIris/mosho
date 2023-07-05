<?php
session_start();
include '../libraries/functions.php';
$hostName =  file_get_contents('../config');
$hostName = json_decode($hostName, 0);
$hostName = $hostName->hostName;
$qq = $_POST['qq'] ?? null;
$qq = safety($qq);
$password = $_POST['password'] ?? null;
$password = safety($password);
$opeartion = $_POST['opeartion'] ?? null;
$opeartion = safety($opeartion);

//    设置头像
if (isset($qq) && $opeartion === "getTouXing" && is_numeric($qq)) {
    $qqInfo = get_qq_info($qq);
    if ($qqInfo->success === true) {
        $a = $qqInfo->imgurl;
        $b = $qqInfo->name;
        $temp = <<<EOL
{"touxiang":"$a", "name":"$b"}
EOL;
        echo $temp;
        die();
    } else {
        $temp = <<<EOL
{"touxiang":"../img/unit/rabbit_holding_flowers.png", "name":"baby"}
EOL;
        echo $temp;
        die();
    }
}

//    登录，未注册将自动注册，然后直接进个人信息页。
if (isset($qq) && isset($password) && $opeartion === "log_reg") {
    try {
        include '../libraries/Conn.php';
        // 查询是否已有账号
        $sql = <<<EOL
select `qq` from mosho.`user` where `qq` = "$qq";
EOL;
        $result = $conn->query($sql)->fetch();
        if ($result !== false) { // 已有账号
            $sql = <<<EOL
select * from mosho.`user` where `qq` = "$qq" and `password` = "$password";
EOL;
            $result = $conn->query($sql)->fetch();
            if ($result !== false) {  // 卡密正确，允许登录
                setcookie("user_id", $result['id'], time() + 60*60, '/');
                setcookie("user_name", $result['name'], time() + 60*60, '/');
                setcookie("user_qq", $result['qq'], time() + 60*60, '/');
                setcookie("user_tou_xiang_url", $result['tou_xiang_url'], time() + 60*60, '/');
                setcookie("user_reg_date", $result['reg_date'], time() + 60*60, '/');
                header("refresh:0;url=./client-center.php");
            } else { // 卡密错误，不允许登录
                echo "<script>alert('卡密错误！')</script>";
                header("refresh:0;url=$hostName/index.php");
            }
        } else { // 要注册
            $qqInfo = get_qq_info($qq);
            if ($qqInfo->success === true) {  // QQ正确，获取到了头像等数据；但也可能QQ存在，设置了私密，查询不到name
                file_put_contents('../img/upload/qq_tou_xiang/' . $qq . ".jpg", file_get_contents($qqInfo->imgurl));
                $qqInfo->name = safety($qqInfo->name);
                $sql = <<<EOL
insert into mosho.`user` (`qq`, `name`, `password`, `tou_xiang_url`) values ("$qq", "$qqInfo->name", "$password", "../img/upload/qq_tou_xiang/$qq.jpg");
EOL;
            } else { // QQ不存在
                // "访客：id
                $name = "访客";
                $sql = <<<EOL
insert into mosho.`user` (`qq`, `name`, `password`) values ("$qq", "$name", "$password");
EOL;
            }
            $conn->exec($sql);
            $sql = <<<EOL
select * from mosho.`user` where `qq` = "$qq";
EOL;
            $result = $conn->query($sql)->fetch();
            setcookie("user_id", $result['id'], time() + 60*60, '/');
            setcookie("user_name", $result['name'], time() + 60*60, '/');
            setcookie("user_qq", $result['qq'], time() + 60*60, '/');
            setcookie("user_tou_xiang_url", $result['tou_xiang_url'], time() + 60*60, '/');
            setcookie("user_reg_date", $result['reg_date'], time() + 60*60, '/');
            header("refresh:0;url=./client-center.php");
        }
    } catch (PDOException $e) {
//            echo $e->getMessage() . PHP_EOL . $e->getLine();
//            echo "<script>alert('注册失败,可能是该账号已注册');</script>";
//            header("refresh:0.5;url=StudentLogin.php");
    }
    die();
}

// 验证码
if ($opeartion === "verify") {
    $num1 = rand(1, 50);
    $num2 = rand(1, 50);
    $num3 = $num1 + $num2;
    $result = ["num1"=>$num1, "num2"=>$num2, "num3"=>$num3];
    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $result;
    die();
}

header("refresh:0;url=$hostName/index.php");
?>