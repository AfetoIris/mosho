<?php
    if (!isset($_COOKIE['user_id'])) {
        header("refresh:0;url=./log_reg.php");
    }

    $hostName =  file_get_contents('../config');
    $hostName = json_decode($hostName, 0);
    $hostName = $hostName->hostName;

    // 获取用户角色
    include '../libraries/Conn.php';
    try {
        $sql = <<<EOL
    select role from mosho.`user` where `qq` = "{$_COOKIE['user_qq']}";
    EOL;
        $role = $conn->query($sql)->fetch()['role'];
    } catch (PDOException $e) {
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("../common/head.php"); ?>
    <link rel="stylesheet" href="../css/client-center.css">
    <style>
        header {
            background-color: rgba(51, 51, 51, .1);
        }
    </style>
    <script src="../js/client-center.js"></script>
</head>
<body>
<?PHP
    include("../common/header.php");
?>
<div id="client-center" class="clearfix">
    <table>
        <tr>
            <td colspan="2">
                <?php $temp = $_COOKIE['user_tou_xiang_url']; echo '<img src="' . $temp . '">'; ?>
            </td>
        </tr>
        <tr>
            <td>用户角色：</td>
            <td><?php echo $role; ?></td>
        </tr>
        <tr>
            <td>用户昵称：</td>
            <td><?php if ($_COOKIE['user_name'] !== "访客") {
                    echo $_COOKIE['user_name'];
                } else {
                    echo "访客：" . $_COOKIE['user_qq'];
                } ?></td>
        </tr>
        <tr>
            <td>注册QQ：</td>
            <td><?php echo $_COOKIE['user_qq']; ?></td>
        </tr>
        <tr>
            <td>注册日期：</td>
            <td><?php echo $_COOKIE['user_reg_date']; ?></td>
        </tr>
    </table>
    <div id="logout">退出登录</div>
</div>
<?php
    include "$hostName/common/footer.php";
?>
</body>
</html>