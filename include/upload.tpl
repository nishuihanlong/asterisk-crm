<script>
function callbackMessage(msg){
var othermsg = "<br />";
parent.document.getElementById("divMessage").innerHTML = msg + othermsg;
}
[##upload_js_function##]
window.parent.init();
</script>