<?php
	$DB->setProcName('SP_WEB_BOARD_LIST_SELECT');
	$DB->bind_param('BOARD_ID', 						$BID,											_VARCHAR, 		20,			_PARAM_IN);
	$DB->bind_param('SEARCH_CATEGORY',			$SEARCH_CATEGORY,				_VARCHAR, 		200,			_PARAM_IN);
	$DB->bind_param('SEARCH_TYPE', 					$SEARCH_TYPE,							_VARCHAR, 		20,			_PARAM_IN);
	$DB->bind_param('SEARCH_KEYWORD', 			$SEARCH_KEYWORD,					_VARCHAR, 		200,			_PARAM_IN);
	$DB->bind_param('START', 							0,												_INT, 				11,			_PARAM_IN);
	$DB->bind_param('END',	 							0,												_INT, 				11,			_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();
	}
	$totalCount = IntVal($result[0]['COUNT']);

	$totalPage = Ceil($totalCount / $MASTER[0]['PAGE_LIST']);
	$page = ($page > $totalPage) ? $totalPage : $page;

	$start = (($page - 1) * $MASTER[0]['PAGE_LIST']) + 1;
	$end = ($page * $MASTER[0]['PAGE_LIST']);
	$curnum = $totalCount - $start;
?>
					<div style="width:100%;">
						<form method="get" name="srhfrm" action="?">
							<div class="search_box center">
								<input type="hidden" id="id" name="id" value="<?php Echo htmlspecialchars($BID); ?>" />
								<select name="srhctgr">
									<option value="title"<?php If($SEARCH_TYPE == 'title') Echo ' selected = "selected"'; ?>>제목</option>
									<option value="contents"<?php If($SEARCH_TYPE == 'contents') Echo ' selected = "selected"'; ?>>내용</option>
									<option value="writer"<?php If($SEARCH_TYPE == 'writer') Echo ' selected = "selected"'; ?>>작성자</option>
									<option value="all"<?php If($SEARCH_TYPE == 'all') Echo ' selected = "selected"'; ?>>전체</option>
								</select>
								<input type="text" id="keyword" name="keyword" value="<?php Echo htmlspecialchars($SEARCH_KEYWORD); ?>" maxlength="30" onkeypress="if(event.keyCode==13){ document.form.submit(); }" class="textbox" style="width:400px" />
								<input type="submit" value="검색" class="submit" />
							</div>
						</form>
						<div style="clear:both"></div>
						<div class="photo_list">
<?php
	If ( $totalCount > 0 ) {
		$DB->setProcName('SP_WEB_BOARD_LIST_SELECT');
		$DB->bind_param('P_BOARD_ID', 						$BID,											_VARCHAR, 		20,			_PARAM_IN);
		$DB->bind_param('P_SEARCH_CATEGORY',			$SEARCH_CATEGORY,				_VARCHAR, 		200,			_PARAM_IN);
		$DB->bind_param('P_SEARCH_TYPE', 					$SEARCH_TYPE,							_VARCHAR, 		20,			_PARAM_IN);
		$DB->bind_param('P_SEARCH_KEYWORD', 			$SEARCH_KEYWORD,					_VARCHAR, 		200,			_PARAM_IN);
		$DB->bind_param('P_START', 								$start,										_INT, 				11,			_PARAM_IN);
		$DB->bind_param('P_END',	 								$end,										_INT, 				11,			_PARAM_IN);

		If ($DB->ExecuteProc()) {
			$result = $DB->get_fetch_assoc();
		} Else location(2, '조회 오류입니다. 관리자에게 문의하십시오.', '/');

		$i = 1;
		$lineOut = 3;

		Foreach ($result as $list) {
			$checkClass = "";
			If($i % $lineOut == 0) {
				If($i == $totalCount || $i % $lineOut == 0 || $i == $MASTER[0]['PAGE_SIZE']) $checkClass = " chk";
			}
			If(($i - 1) % $lineOut == 0) {
?>
							<div class="set_wrap ofh flefts">
<?php
			}
?>
								<a href="?id=<?php Echo $BID; ?>&mode=V&idx=<?php Echo $list['IDX']; ?>&p=<?php Echo $page; ?>" class="photo_set<?php Echo $checkClass; ?>">
									<p style="width:190px; height:140px;" class="ofh center">
<?php
			If (!Empty($list['FILE_NAME'])) {
?>
										<img src="getImage.php?idx=<?php Echo $list['IDX']; ?>&temp=m_thumb_" width="190" height="140" alt="" />
<?php
			} Else Echo '<img src="/cont/2/noimg.png" alt="" width="190" height="140" />';
?>
									</p>
									<p class="subject"><?php Echo (mb_strlen($list['TITLE']) > 19) ? mb_substr($list['TITLE'], 0, 16, 'UTF-8') . '...' : $list['TITLE']; ?></p>
								</a>
<?php
			If($i == $totalCount || $i % $lineOut == 0 || $i == $MASTER[0]['PAGE_SIZE']) {
?>
							</div>
<?php
			}

			$i++;
		}

	} Else {
?>
							<div><p style="text-align:center; width:100%; padding-top:40px; height:60px; font-weight:bold;">등록된 내역이 없습니다.</p></div>
<?php
	}
?>
						</div>
					</div>
					<br />
					<div class="paginate_complex">
<?php
	setPaging('', $param, $page, $totalPage, $MASTER[0]['PAGE_BLOCK']);
?>
					</div>
<?php
	If(In_Array($_SESSION['HOME']['USER_AUTH'], $WRITE_LEVEL_ARRAY)){
?>
					<div id="boardBtn">
						<div class="fright">
							<a href="?id=<?php Echo $BID; ?>&mode=W&srhctgr=<?php Echo $SEARCH_TYPE; ?>&keyword=<?php Echo $SEARCH_KEYWORD; ?>">등록</a>
						</div>
					</div>
<?php
	}
?>
