function insertPostLink(elem){
	var ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
	e = dom.getParent(n, 'A');
	if(e == null){
		/*b = ed.selection.getBookmark();
		tinyMCEPopup.execCommand("UnLink", false);
		ed.selection.moveToBookmark(b);*/
		tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo : 1});
		tinymce.each(ed.dom.select("a"), function(n) {
			if (ed.dom.getAttrib(n, 'href') == '#mce_temp_url#') {
				e = n;
				ed.dom.setAttribs(e, {
					href : elem.href
				});
			}
		});
	}
	else{
			ed.dom.setAttribs(e, {
				href : elem.href
			});	
	}
	tinyMCEPopup.close();
	return false;
}
function showFilter(){
	$('#showFilter').css('display','none');
	$('#filter').css('display','block');
}
function hideFilter(){
	$('#showFilter').css('display','block');
	$('#filter').css('display','none');
}