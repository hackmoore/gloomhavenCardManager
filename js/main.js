function generateCard(parent, cardid, front){
	html = "<div class='card' data-cardid='"+ cardid +"' style='background-image: url(\"images/cards/"+ cardid + ".png\");'></div>";
	$(parent).append(html);
}