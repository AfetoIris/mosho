<?php
    include '../libraries/Conn.php';
    include '../libraries/functions.php';

    $paras = ["operation"=>($_POST['operation'] ?? null), "text"=>($_POST['text'] ?? null),
        "qq"=>($_POST['qq'] ?? null), "commentId"=>($_POST['commentId'] ?? null),
        "nums"=>($_POST['nums'] ?? null), "dPage"=>($_POST['dPage'] ?? null)];

    // 防SQL注入
    foreach ($paras as $k=>$v) {
        $paras[$k] = safety($v);
    }

    // 开始留言
    if ($paras['operation'] === 'comment') {
        $temp = getUrlName($conn, $paras['qq']);
        $paras['tou_xiang_url'] = $temp[0]['tou_xiang_url'];
        $paras['comment_name'] = $temp[0]['name'];

        try {
            $sql = <<<EOL
insert into mosho.`comment` (`text`, `qq`, `tou_xiang_url`, `comment_name`) values
    ("{$paras['text']}", "{$paras['qq']}", "{$paras['tou_xiang_url']}",
     "{$paras['comment_name']}");
EOL;
            $conn->exec($sql);
            echo '{"msgcode":"1"}';  // 1是成功
        } catch (PDOException $e) {
            echo '{"msgcode":"0"}';  // 0是失败
        }
    } elseif ($paras['operation'] === 'show') {  // 发送留言数据
        try {
            $sql = <<<EOL
select * from mosho.`comment` order by `comment_time` desc;  # 这样排序，越新留言越靠前
EOL;
            $result = $conn->query($sql)->fetchAll();
            $countData = count($result);

            // 截取部分,array_slice的第四个参数得是false，这样它会重新给最终元素从0排序，
            // 例如我只发生数据库中第20个元素，第四个参数true则js得写obj[20]，
            // false则js写obj[0]，后者才符合js的遍历习惯
            $result = array_slice($result, ($paras['dPage'] - 1) * $paras['nums'], $paras['nums']);
            $result['num'] = count($result);
            $result['msgcode'] = 1;
            $result['countData'] = $countData;
            $result = json_encode($result, JSON_UNESCAPED_UNICODE);
            echo $result;
        } catch (PDOException $e) {
            echo '{"msgcode":"0"}';  // 0是失败
        }
    }

    // 删除留言
    if ($paras['operation'] === 'deleteComment' && isset($paras['commentId']) && isset($paras['qq'])) {
        $sql = <<<EOL
select role from mosho.`user` where `qq` = "{$paras['qq']}" and role = "管理员";
EOL;
        $result = $conn->query($sql)->fetch();
        if ($result == array()) {
            echo '{"msgcode":"0"}';
            die();
        }

        $sql = <<<EOL
delete from comment where id = "{$paras['commentId']}";
EOL;
        try {
            $conn->exec($sql);
            echo '{"msgcode":"1"}';
        } catch (PDOException $e) {
            echo '{"msgcode":"0"}';
        }
    }

    // 获取tou_xiang_url、comment_name
    function getUrlName($conn, $qq) {
        $sql = <<<EOL
select `name`, `tou_xiang_url` from mosho.`user` where `qq` = "{$qq}";
EOL;
        $result = $conn->query($sql)->fetchAll();
        return $result;
    }

    $conn = null;
?>