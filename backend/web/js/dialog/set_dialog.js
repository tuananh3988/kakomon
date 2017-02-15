// 削除確認
function confirmDeleteContents(id){
	var contents = "";
	contents += '<form action="/maintenance/resident/delete" id="ResidentIndexForm" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>';
	contents += '<p class=confirm_text>選択したデータを削除しますが、よろしいですか？</p><div class=dialogItem>';
	contents += '<input type="hidden" name="residentId" value="' + id + '">';
	contents += '<input  class="confirm_ok" type="submit" value="OK"/>';
	contents += '</form>';
	contents += '<input type="button" value="キャンセル" class="confirm_cancel" onclick="hideDialog();"></div>';
	
	showDialog('データ削除確認',contents,'prompt');
}

// jquery-ui dialog
//jQuery("#jquery-ui-dialog").dialog({
//	autoOpen:false,
//	modal:true,
//	width:800,
//	draggable:false,
// resizable:false,
// show:"fade",
// hide:"fade"
//});
//
//jQuery("a.jquery-ui-opener").on("click",function(){
// jQuery("#jquery-ui-dialog").dialog("open");
// return false;
//});
