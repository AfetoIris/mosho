(function () {
    window.addEventListener('load', ()=>{
        let qq = document.querySelector('#qq');
        let password = document.querySelector('#password');
        let touXiangImg = document.querySelector('#tou-xiang-img');
        let headerTouXiang = document.querySelector('#header-touxiang');
        let verifyElement = document.querySelector('#verify');
        let submit = document.querySelector('#submit');

        addEventListene(qq, "change", ()=>{
            let xhr = new XMLHttpRequest();
            xhr.open('POST', "../html/log_reg_jud.php");
            xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xhr.send("opeartion=getTouXing&qq=" + qq.value);
            addEventListene(xhr, 'readystatechange', ()=>{
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        let obj = JSON.parse('[' + xhr.response + ']')[0];
                        touXiangImg.src = obj.touxiang;
                        headerTouXiang.style.background = 'url(' + obj.touxiang + ') no-repeat';
                        headerTouXiang.style.backgroundSize = "contain";
                    }
                }
            });
        });

        // 验证码
        verify();
        function verify() {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', "../html/log_reg_jud.php");
            xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xhr.send("opeartion=verify");
            addEventListene(xhr, 'readystatechange', ()=>{
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        let obj = JSON.parse('[' + xhr.response + ']')[0];
                        verifyElement.setAttribute('placeholder', "验证码：" + obj.num1 + "+" + obj.num2 + "=？");
                        verifyElement.setAttribute("data-num3" , obj.num3);
                    }
                }
            });
        }

        // 验证码正确再给注册登录
        addEventListene(submit, 'click', (e)=>{
            if (verifyElement.value != verifyElement.getAttribute('data-num3')) {
                // submit.setAttribute('disable', true);
                if ( e && e.preventDefault )
                    e.preventDefault();
                //IE阻止默认事件
                else
                    window.event.returnValue = false;
                alert("验证码错误！");
                verify();
                verifyElement.value = '';
            } else {
                submit.setAttribute('disable', false);
            }
        });
    });
})();