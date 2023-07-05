function addEventListene(element, eventName, fn) {
    if (element.addEventListener) {
        element.addEventListener(eventName, fn);
    } else if (element.attachEvent) {
        element.attachEvent('on' + eventName, fn);
    } else {
        element['on' + eventName] = fn;
    }
}

// 注意，只有addEventListener的参数二是函数名而非()=>{}这样的函数体，才能removeEventListener掉
function removeEventListene(element, eventName, fn) {
    if (element.removeEventListener) {
        element.removeEventListener(eventName, fn);
    } else if (element.detachEvent) {
        element.detachEvent('on' + eventName, fn);
    } else {
        element['on' + eventName] = null;
    }
}

// 阻止事件冒泡
function stopPropagation(e) {
    e = e || window.event;
    if(e.stopPropagation) {
        e.stopPropagation(); //W3C阻止冒泡方法
    } else {
        e.cancelBubble = true; //IE阻止冒泡方法
    }
}