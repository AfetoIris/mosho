<?php
    // 此文件用于js常常需要ajax呼叫php干些php方便干的事，每个js用$operation区分目的。
    include '../libraries/functions.php';
    $operation = $_POST['operation'] ?? null;
    if ($operation === 'drop_user_cookie') {
        drop_user_info_cookie();
        echo '{"msgCode":"1"}';
    } elseif ($operation === 'get_random_music') {
        // 由于找的随机音乐API没开放跨源请求，总之就是我js没法直接调用API
        // 咱也不知道明明php和js都是本网站下的文件，php这边不受跨源请求限制

        // 这个api太黑了。js调用报错“未开放跨站请求”，php调用，在本机是OK的，但是放在服务器上，不知道是限制了IP还是反爬
        // 用不了
//        $result = file_get_contents("https://api.wqwlkj.cn/wqwlapi/wyy_random.php?type=json");
//        $result = json_decode($result); // 先字符串转json对象
//        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
//        echo $result;
        $type = $_POST['sort'] ?? "热歌榜";  // 热歌榜/新歌榜/飙升榜/原创
        $result = file_get_contents("https://api.vvhan.com/api/rand.music?type=json&sort=" . $type);
        $result = json_decode($result);
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        echo $result;
    }
?>