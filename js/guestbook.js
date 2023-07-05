(function () {
    window.addEventListener('load', ()=>{
        // let statusElement = document.querySelector('#status');
        // let statusFlag = true;
        // let status = "public";
        let textElement = document.querySelector("#text");
        let submit = document.querySelector("#submit");
        let commentTemplate = document.querySelector('#comment-template');
        let commentContainer = document.querySelector('#comment-container');
        let dPageElement = document.querySelector('#dPage');
        let countPageElement = document.querySelector('#countPage');
        let countDataElement = document.querySelector('#countData');
        let firstPageElement = document.querySelector('#firstPage');
        let prePageElement = document.querySelector('#prePage');
        let lastPageElement = document.querySelector('#lastPage');
        let endPageElement = document.querySelector('#endPage');
        let dPage = 1;  // 当前页
        let nums = 5;  // 每页几个
        let countPage = 0;
        let countData = 0;
        let deletes = document.querySelectorAll('.delete');

        // // 状态：公开 / 仅站长可见
        // addEventListene(statusElement, 'click', ()=>{
        //     if (statusFlag) {
        //         statusElement.innerHTML = "当前留言状态：仅站长可见";
        //         statusFlag = false;
        //     } else {
        //         statusElement.innerHTML = "当前留言状态：公开";
        //         statusFlag = true;
        //     }
        // });

        // 首页
        addEventListene(firstPageElement, 'click', ()=>{
            dPage = 1;
            clearCommentContainerChilds();
            get_comment(dPage, nums);
        });

        // 上页
        addEventListene(prePageElement, 'click', ()=>{
            dPage = (dPage - 1) > 0 ? (dPage - 1) : dPage;
            clearCommentContainerChilds();
            get_comment(dPage, nums);
        });

        // 下页
        addEventListene(lastPageElement, 'click', ()=>{
            dPage = (dPage + 1) > countPage ? dPage : (dPage + 1);
            clearCommentContainerChilds();
            get_comment(dPage, nums);
        });

        // 尾页
        addEventListene(endPageElement, 'click', ()=>{
            dPage = countPage;
            clearCommentContainerChilds();
            get_comment(dPage, nums);
        });

        // 开始留言
        addEventListene(submit, 'click', ()=>{
            let text = textElement.value; // 后端需要对text做SQL注入过滤
            // if (statusFlag) {
            //     status = "public";
            // } else {
            //     status = "private";
            // }
            let cookies = get_cookie();
            let qq = cookies.user_qq;
            // let user_location = cookies.user_location;
            // if (user_location == undefined) {
            //     user_location = "未知的地点";
            // }
            comment(text, qq);
        })

        // 获取留言
        get_comment(dPage, nums);

        function get_comment(dPage=1, nums=5) {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', './guestbook-jud.php');
            xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xhr.send('operation=show&nums='.concat(nums) + "&dPage=" + dPage);
            addEventListene(xhr, 'readystatechange', ()=>{
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        let obj = JSON.parse(xhr.response);
                        if (obj.msgcode === 1) {
                            show_in_page(obj);
                            deletes = document.querySelectorAll('.delete');
                            // 删除留言
                            for (i = 0; i < deletes.length; i++) {
                                deletes[i].onclick = function () {
                                    deleteComment(this.getAttribute('data-index'), cookies.user_qq);
                                }
                            }
                            countData = obj.countData;
                            countPage = Math.ceil(countData / nums);
                            fenYe(dPage, countData);
                        }
                    }
                }
            });
            xhrTimeoutError(xhr);
        }

        // 展示留言函数
        function show_in_page(obj) {
            for (let i = 0; i < obj.num; i++) {
                let temp = commentTemplate.cloneNode(true);
                temp.setAttribute("id", "");
                temp.setAttribute("data-index", obj[i].id);
                if (temp.querySelector('.delete')) {  // 若元素不存在，js会报错卡死在这
                    temp.querySelector('.delete').setAttribute("data-index", obj[i].id);
                }
                temp.setAttribute("class", "comment");
                // temp.querySelector('.from').innerHTML = "暂存值";
                temp.querySelector('.note').innerHTML = obj[i].text;
                temp.querySelector('.note_time').innerHTML = obj[i].comment_time;
                // temp.querySelector('.read-times').innerHTML = "阅读：".concat(obj[i].read_times);
                temp.querySelector('.who').innerHTML =  obj[i].comment_name;
                temp.querySelector('.tou-xiang').querySelector('img').src = hostName.concat(obj[i].tou_xiang_url.substring(2));
                commentContainer.appendChild(temp);
            }
        }

        // 留言
        function comment(text, qq) {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', './guestbook-jud.php');
            xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xhr.send('operation=comment&text=' + text + "&qq=" + qq);
            addEventListene(xhr, 'readystatechange', ()=>{
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        let obj = JSON.parse(xhr.response);
                        if (obj.msgcode === "1") {
                            alert("留言成功！");
                            location.reload();
                        }
                    }
                }
            });
            xhrTimeoutError(xhr);
        }

        // 设置分页
        function fenYe(dPage, countData) {
            dPageElement.innerHTML = dPage;
            countDataElement.innerHTML = countData;
            countPage = Math.ceil(countData / nums);
            countPageElement.innerHTML = countPage;
        }

        // 除了第一次加载，其他每次调用展示留言函数，都要先清除已存在的留言
        function clearCommentContainerChilds() {
            let commentContainerChilds = commentContainer.querySelectorAll('.comment');
            let a = commentContainerChilds.length;
            for (let i = 1; i < a; i++) {
                commentContainer.removeChild(commentContainerChilds[i]);
            }
        }

        // 删除留言
        function deleteComment(commentId, qq) {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', './guestbook-jud.php');
            xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xhr.send('operation=deleteComment&commentId=' + commentId + "&qq=" + qq);
            addEventListene(xhr, 'readystatechange', ()=>{
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        let obj = JSON.parse(xhr.response);
                        if (obj.msgcode === "1") {
                            alert("删除成功！");
                            location.reload();
                        } else {
                            alert("删除失败！");
                        }
                    }
                }
            });
        }
    });
})();