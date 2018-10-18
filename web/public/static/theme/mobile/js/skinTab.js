function getCookie(Name) {
    var re = new RegExp(Name + "=[^;]+", "i"); //重新构建加载目标 名称/值
    if (document.cookie.match(re)) //记录cookie
        return document.cookie.match(re)[0].split("=")[1] //返回值
    return null
}
function setCookie(name, value, days) {
    var expireDate = new Date()
//区分
    var expstring = (typeof days != "undefined") ? expireDate.setDate(expireDate.getDate() + parseInt(days)) : expireDate.setDate(expireDate.getDate() - 5)
    document.cookie = name + "=" + value + "; expires=" + expireDate.toGMTString() + "; path=/";
}
function deleteCookie(name) {
    setCookie(name, "moot")
}
function setStylesheet(title) {
    var i, cacheobj
    for (i = 0; (cacheobj = document.getElementsByTagName("link")[i]); i++) {
        if (cacheobj.getAttribute("rel").indexOf("style") != -1 && cacheobj.getAttribute("title")) {
            cacheobj.disabled = true
            if (cacheobj.getAttribute("title") == title)
                cacheobj.disabled = false //启用选定的样式表
        }
    }
}
function chooseStyle(styletitle, days) {
    if (document.getElementById) {
        setStylesheet(styletitle)
        setCookie("mysheet", styletitle, days)
    }
}
var selectedtitle = getCookie("mysheet")
if (document.getElementById && selectedtitle != null) //加载用户选择样式表，如果有此样式表
    setStylesheet(selectedtitle)