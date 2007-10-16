<script>
function callbackMessage(msg){
var othermsg = "<br />";
//把父窗口显示消息的层打开
parent.document.getElementById("message").style.display = "block";
//把本窗口获取的消息写上去
parent.document.getElementById("message").innerHTML = msg + othermsg;
//并且设置为10秒后自动关闭父窗口的消息显示
setTimeout("parent.document.getElementById('message').style.display = 'none'", 10000);
}
[##upload_js_function##]
window.location='show_image.php';
</script>