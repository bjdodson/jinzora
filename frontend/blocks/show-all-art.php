<?php
	global $this_page;
		
	$artArray = $node->getSubNodes("nodes",distanceTo("album",$node),false,-1,true);
	if (count($artArray) == 0){ return; }
	
	$display = new jzDisplay();
	$smarty = smartySetup();
	
	// Let's setup our page links	
	$link = "Page: ";
	$link .= '<form action="'. $this_page. '" method="POST">'. "\n";
	$link .= '<input type="hidden" name="'. jz_encode("action"). '" value="'. jz_encode("viewallart"). '">';
	$link .= '<input type="hidden" name="'. jz_encode("jz_path"). '" value="'. jz_encode($node->getPath("String")). '">';
	$link .= '<select name="'. jz_encode("page"). '" class="jz_select" onChange="form.submit();">'. "\n";
	$link .= '<option ';
	$_POST['page'] = (isset($_POST['page'])) ? $_POST['page'] : '';
	if ($_POST['page'] == "RANDOM"){ $link .= " selected "; }
	$link .= 'value="'. jz_encode("RANDOM"). '">'. word("Random"). '</option>'. "\n";
	$link .= '<option ';
	if ($_POST['page'] == "ALL"){ $link .= " selected "; }
	$link .= 'value="'. jz_encode("ALL"). '">'. word("All"). '</option>'. "\n";
	$link .= '<option ';
	if (($_POST['page']+1) == 1 and ($_POST['page'] <> "ALL")){ $link .= " selected "; }
	$link .= 'value="'. jz_encode("0"). '">1</option>'. "\n";
	// Now let's dynamically do all the middle ones
	$i=0;$e=0;$c=1;
	while($i < count($artArray)){
		if ($e == "24"){
			$e=0;
			$c++;
			$link .= '<option ';
			if (($_POST['page']+1) == $c){ $link .= " selected "; }
			$link .= 'value="'. jz_encode(($c - 1)). '">'. $c. '</option>'. "\n";
		}
		$i++;$e++;
	}
	$link .= '</select></form>';		
	$link .= "&nbsp; &nbsp;";
	
	$smarty->assign('title',word("All Album Art"));
	$smarty->assign('title_right',$link);

	//$this->blockHeader(word("All Album Art"), $link);
	//$this->blockBodyOpen();
	
	// Now let's slice this up into pages
	if (isset($_POST['page'])){
		if ($_POST['page'] == "RANDOM"){
			$start = (1 * 24);
			shuffle($artArray);
			$artArray = array_slice($artArray,$start,24);
		} elseif ($_POST['page'] == "ALL"){
			$artArray = $artArray;
		} else {
			$start = ($_POST['page'] * 24);
			$artArray = array_slice($artArray,$start,24);
		}
	} else {
		$artArray = array_slice($artArray,0,24);
	}
	
	jzTemplate($smarty, "block-open");
	flushdisplay();

	$i=0;$c=0;
	foreach($artArray as $item){
		$array[$c]['image'] = $display->link($item,$display->returnImage($item->getMainArt("100x100"),$item->getName(),100,100,"fixed"),false,false,true);
		if ($i == 6){
			$i=-1;
			$array[$c]['row'] = '</tr><tr>';
		} else {
			$array[$c]['row'] = "";
		}
		$i++;$c++;
	}
	$smarty->assign('items',$array);
	
	jzTemplate($smarty, "show-all-art");
	jzTemplate($smarty, "block-close");
?>