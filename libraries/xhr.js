// 需要调用EventListene.js

function xhrTimeoutError(xhr) {
    let tips = '';
    addEventListene(xhr, 'timeout', ()=>{
        tips = '网络异常，请稍后重试！';
        return tips;
    });
    addEventListene(xhr, 'error', ()=>{
        tips = '您的网络似乎出了一些问题！';
        return tips;
    });
}

// 此函数不能调用，看下面注释。仅可用于复制粘贴
function xhr(url, payload, callback='') {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', url);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhr.send(payload);
    addEventListene(xhr, 'readystatechange', ()=>{
        if (xhr.readyState === 4) {
            if (xhr.status >= 200 && xhr.status < 300) {
                // let obj = JSON.parse('[' + xhr.response + ']');  // obj是数组，obj[0]等于下行的对象
                let obj = JSON.parse(xhr.response);  // obj是对象
               //  由于等待ajax回传数据时间过长，加上事件和定时器属于异步任务，所以我们无法在此处通过return obj;
               //  的方式传值给let result = xhr（）的result变量，console.log(result);输出undefined,
                //  因为result在主空间是优先于异步任务执行的同步任务。解决方案：回调函数？:试过了，不可行！，两个文件的变量不互通
            }
        }
    });
    // xhrTimeoutError(xhr);
}