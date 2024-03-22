<?php
	$page = (IsSet($_GET['p']) && !Empty($_GET['p'])) ? IntVal($_GET['p']) : 1;

	$srhctgr = $_GET['srhctgr'];
	$keyword = $_GET['keyword'];
	$lv = null;

	if(!empty($keyword)) {
		switch($srhctgr) {
			case 'subject':
				$lv = 'and `TITLE` like \'%'.mysql_real_escape_string($keyword).'%\'';
			break;
			case 'contents':
				$lv = 'and `CONTENTS` like \'%'.mysql_real_escape_string($keyword).'%\'';
			break;
			case 'author':
				$lv = 'and (`WRITER` like \'%'.mysql_real_escape_string($keyword).'%\'';
			break;
		}
	}
?>
							<form method="get" name="srhfrm" action="?">
								<p class="search">
									<input type="hidden" id="id" name="id" value="<?php Echo htmlspecialchars($BID); ?>" />
									<input type="image" src="/res/img/board/btn_search.jpg" alt="검색" onclick="this.form.submit(); return false;" />
									<input type="text" id="keyword" name="keyword" value="<?php Echo htmlspecialchars($keyword); ?>" maxlength="30" onkeypress="if(event.keyCode==13){ document.form.submit(); }" />
									<select name="srhctgr">
										<option value="subject"<?php If($srhctgr == 'subject') Echo ' selected = "selected"'; ?>>책자</option>
										<option value="author"<?php If($srhctgr == 'author') Echo ' selected = "selected"'; ?>>저자</option>
										<option value="contents"<?php If($srhctgr == 'contents') Echo ' selected = "selected"'; ?>>내용</option>
									</select>
								</p>
							</form>
<?php
	$r = mysql_query("SELECT COUNT(`IDX`) FROM `T_BOARD` WHERE BOARD_ID = '{$BID}' {$lv} ");
	$c = mysql_fetch_row($r);
	$totalCount = IntVal($c[0]);
	
	mysql_free_result($r);

	$total_page = ceil($totalCount / $pages);
	$start = ($page - 1) * $pages;
	$num = $totalCount - $start;

	$r = mysql_query("SELECT `IDX`, `SEQ`, `LEVEL`, `NOTI`, `TITLE`, `WRITER`, `CONTENTS`, `READ`, `FILE_NAME` FROM `T_BOARD` WHERE BOARD_ID = '{$BID}' {$lv} ORDER BY `NOTI` ASC, `GROUP` DESC, `LEVEL` ASC  LIMIT {$start}, {$pages} ");
?>
							<div id="webzine">
<?php
	If ( $totalCount > 0 ) {
		While ( $list = mysql_fetch_assoc($r) ) {
			$fileCheck = False;
?>
								<div class="wrap">
									<div class="imgWrap">
										<div class="img">
<?php
			If (!Empty($list['FILE_NAME'])) {
				$ext = strToLower(substr(strrchr($list['FILE_NAME'], '.'), 1));
				$filePath = $_SERVER['DOCUMENT_ROOT'] . '/files/' . $BID . '/' . $list['FILE_NAME'];
				If (File_Exists($filePath)) $fileCheck = True;
				$extArray = Array('jpeg', 'jpg', 'png', 'gif', 'bmp');
				If ($fileCheck == True && !in_Array($ext, $extArray)) $fileCheck = False;
			}
			
			If ($fileCheck == True) {
?>
											<img src="/files/<?php Echo $BID; ?>/<?php Echo $list['FILE_NAME']; ?>" alt="" width="80" height="100" />
<?php
			} Else {
?>
											<img src="/res/img/board/noimg_h.png" alt="" width="80" height="100" />
<?php
			}
?>
										</div>
									</div>
									<div class="cont">
										<p class="title">
											<a href="?id=<?php Echo $BID; ?>&amp;mode=V&amp;idx=<?php Echo htmlspecialchars($list['IDX']); ?>&amp;srhctgr=<?php Echo $srhctgr; ?>&amp;keyword=<?php Echo $keyword; ?>&amp;p=<?php Echo $page; ?>"  class="ls_lnk">
												<?php Echo $list['TITLE']; ?>
											</a>
										</p>
										<ul>
											<li>저자 : <?php Echo $list['WRITER']; ?> </li>
										</ul>
										<p class="sub">
											<?php Echo (mb_strlen(strip_tags($list['CONTENTS'])) > 200) ? mb_substr(strip_tags($list['CONTENTS']), 0, 196, 'UTF-8') . '...' : strip_tags($list['CONTENTS']); ?>
										</p>
									</div>
								</div>
<?php
			$num--;
		}
	} Else {
?>
								<div class="wrap">
									<p class="center">등록된 추천서가 없습니다.</p>
								</div>
<?php
	}

	mysql_free_result($r);
?>
							</div>
							<!-- 목록 끝 -->

							<div id="CNPDBoardPage">
								<p>
<?php
								$param = "id={$BID}&amp;srhctgr={$srhctgr}&amp;keyword={$keyword}";
								setPaging($param, $page, $total_page, $block);
?>
								</p>
							</div>