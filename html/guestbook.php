<!DOCTYPE html>
<?php
    if (!isset($_COOKIE['user_id'])) {
        header("refresh:0;url=../index.php");
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
<html lang="zh-CN">

<head>
    <title>可以给我留个言吗 (๑♡ω♡๑)</title>
    <?php include("../common/head.php"); ?>
    <link rel="stylesheet" href="../css/guestbook.css">
    <script src="../js/jquery.mi.js"></script>
    <script src="../js/guestbook.js"></script>
</head>

<body>
    <?php include '../common/header.php'; ?>
    <div id="type-ground">
        <textarea placeholder="正文" id="text"></textarea>
<!--        <button id="status">当前留言状态：公开</button>-->
        <input  type="submit" id="submit" value="发表">
    </div>
    <div id="comment-container">
        <div class="comment hidden" id="comment-template" data-index="-1">
            <div class="comment-hd">
<!--                <h1 class="from">from 天津市-天津市</h1>-->
                <div class="noter-info">
                    <span class="tou-xiang"><img src="https://note.youdao.com/yws/api/personal/file/WEB34578f3416eb7e73a604c62ef6de1961?method=download&shareKey=7a299bb14a3375868b50ddbfcccc5548"></span>
                    <span class="who">who</span>
                </div>
                <?php if ($role === "管理员") {
                    echo <<<EOL
                <button class="delete">删除</button>
EOL;
                } ?>
            </div>
            <hr>
<!--     让pre里的文字自动换行       -->
            <pre class="note" style="white-space: pre-wrap; word-wrap: break-word;">正文</pre>
            <div class="comment-info clearfix">
                <div class="note_time">2023-06-14 11:15:36</div>
<!--                <div class="read-times">阅读：5</div>-->
            </div>
        </div>
    </div>
    <!-- 翻页 -->
    <ul class="fan-ye">
        <li>当前第<i id="dPage">1</i>页/共<i id="countPage"></i>页/共<i id="countData"></i>条数据</li>
        <li id="firstPage">首页</li>
        <li id="prePage">上一页</li>
        <li id="lastPage">下一页</li>
        <li id="endPage">尾页</li>
    </ul>
    <?php
        include "$hostName/common/footer.php";
    ?>
</body>

</html>