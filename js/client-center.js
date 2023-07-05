(function () {
    window.addEventListener('load', ()=>{
        let logout = document.querySelector('#logout');
        addEventListene(logout, 'click', ()=>{
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '../common/execute.php');
            xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xhr.send('operation=drop_user_cookie');
            console.log(2);
            addEventListene(xhr, 'readystatechange', ()=>{
                console.log(1);
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        let obj = JSON.parse('[' + xhr.response + ']');
                        console.log(obj);
                        location.href = "/index.php";
                    }
                }
            });
            xhrTimeoutError(xhr);
        });
    });
})();