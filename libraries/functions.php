<?php
// 获取本机（非访客）的信息，api来源 https://api.vvhan.com/fangke.html ,ip不是很准，就这样吧
// 由于php是在服务器上装好页面，所以想获取访客IP，得用js的爬虫如ajax、axiox、jquery调用接口
function get_visitor_info() {
    $result = file_get_contents("https://api.vvhan.com/api/visitor.info");
    // 返回字符串，这儿不方便下方代码，要去别的地方修改$result['location']
//        if ($result['location'] === "---") {
//            $result['location'] = "未知的地方";
//        }
    return $result;
}

//    根据QQ号获取头像、昵称等信息
function get_qq_info($qq) {
    // api来自https://api.vvhan.com/qq.html
    $touxiang = file_get_contents("https://api.vvhan.com/api/qq?qq=" . $qq);
    $touxiang = json_decode($touxiang, false);  // 第二个参数为 TRUE 时，将返回数组，FALSE 时返回对象。
    if ((!isset($touxiang->name)) || ($touxiang->name === '')) {
        $touxiang->name = "访客";
    }
    return $touxiang;
}

// 函数抵御SQL注入攻击，将特殊字符去掉
function safety($str) {
    $arr = ['"', "'", "<", ">", "=", "(", ")"];
    foreach ($arr as $v) {
        $str = str_replace($v, '', $str);
    }
    return $str;
}

// js设置、注销cookie比较麻烦，我借助php注销cookie
function drop_user_info_cookie(): bool
{
    try {
        setcookie("user_id", '', time() - 60*60, '/');
        setcookie("user_name", '', time() - 60*60, '/');
        setcookie("user_qq", '', time() - 60*60, '/');
        setcookie("user_tou_xiang_url", '', time() - 60*60, '/');
        setcookie("user_reg_date", '', time() - 60*60, '/');
        setcookie("user_system", '', time() - 60*60, '/');
        setcookie("user_location", '', time() - 60*60, '/');
        setcookie("user_ip", '', time() - 60*60, '/');
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// 记载访客的函数
// 定义函数时，把最不可能被赋值的形参放后面，例如定义为function user_log($conn, $qq=null, $user_location='null', $user_ip='null' )
// 写user_log($conn=$conn, $user_location=$user_location, $user_ip=$user_ip);
// 赋值会是$conn=$conn， $qq=$user_location， $user_location==$user_location
function user_log($conn, $user_location='null', $user_ip='null', $user_system="", $qq=null) {
    try {
        // include '../libraries/Conn.html';  // 我就不在这引入了，自己引
        if (isset($qq)) {
            $sql = <<<EOL
        insert into mosho.`user_log` (`qq`, `user_location`, `user_ip`, `user_system`) values ('$qq', "$user_location", "$user_ip", "$user_system");
        EOL;
        } else {
            $sql = <<<EOL
        insert into mosho.`user_log` (`user_location`, `user_ip`, `user_system`) values ("$user_location", "$user_ip", "$user_system");
        EOL;
        }
        $conn->exec($sql);
        setcookie("had_log", 'true', time() + 60*60*2, '/');  // 设置标记
    } catch (PDOException $e) {
        echo $e->getLine() . PHP_EOL . $e->getMessage();
    }
}
?>