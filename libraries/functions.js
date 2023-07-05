// 想能被其他js文件引用某函数/变量，就在外边写，
// 如果用到元素，元素是要待加载完毕的，就得写在window.addEventListener('load'里
// 但也会不可被外界js调用

// 此函数作用是document.cookie得到cookie们，内容用空格和=和;区分开的一个字符串，
// 我们借用str.split('')得到对象形式的cookie
function get_cookie() {
    let obj = { };
    a = document.cookie;
    a = a.split(' ');
    for (i = 0; i < a.length; i++) {
        let temp = a[i];
        if (temp[temp.length - 1] == ';') {
            a[i] = temp.slice(0, -1);
        }
        obj[a[i].split('=')[0]] = decodeURI(a[i].split('=')[1]);
    }
    obj['user_tou_xiang_url'] = decodeURIComponent(obj['user_tou_xiang_url']);
    obj['user_reg_date'] = decodeURIComponent(obj['user_reg_date']);
    return obj;
}

// 设置 cookie。一次只能设置一个cookie，
// 调用示例：setCookie('a', 'b', 1000 * 60 * 60 * 1, '/');
// 设置第二个 cookie：setCookie('c', 'd', 1000 * 60 * 60 * 1, '/');
function setCookie(name, value, msTime, path) {
    var expires = new Date();
    expires.setTime(expires.getTime() + msTime);
    // Cookie 数据中不能包含分号、逗号或空格，因此在将数据存储到 Cookie 之前，
    // 可以使用 JavaScript 内置的 encodeURIComponent() 函数对数据进行编码。
    // 在读取 Cookie 时，使用对应的 decodeURIComponent() 函数来解析 Cookie 数据
    document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + expires.toUTCString() + ';path=' + path;
}

// 仅支持复制没请勿直接调用
// 用js爬虫获取访客ip、地理位置等信息的函数，但是你不能调用，
// 因为js的ajax是异步的所以你无法将结果带给ajax外的变量李
function get_visitor_location() {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', 'https://api.vvhan.com/api/visitor.info');
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhr.send('operation=drop_user_cookie');
    addEventListene(xhr, 'readystatechange', ()=>{
        if (xhr.readyState === 4) {
            if (xhr.status >= 200 && xhr.status < 300) {
                let obj = JSON.parse(xhr.response);
                // console.log(obj); // 返回对象
            }
        }
    });
}

// js设置访客位置、ip、操作系统的cookie
function set_location_ip_system_cookie(callback=null) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', 'https://api.vvhan.com/api/visitor.info');
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhr.send('operation=drop_user_cookie');
    addEventListene(xhr, 'readystatechange', ()=>{
        if (xhr.readyState === 4) {
            if (xhr.status >= 200 && xhr.status < 300) {
                let obj = JSON.parse(xhr.response);
                setCookie("user_location", obj.location, 1000 * 60 * 60 * 2, '/');
                setCookie("user_ip", obj.ip, 1000 * 60 * 60 * 2, '/');
                setCookie("user_system", obj.system, 1000 * 60 * 60 * 2, '/');
                if (callback) {
                    callback();
                }
            }
        }
    });
}