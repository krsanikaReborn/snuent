<?php
	Require_Once DIRNAME(__FILE__).'/inc/DBConnection.php';

	Define('webSite', '남서울*%*창업');
	Define('webPage', 'index');

	mb_internal_encoding('UTF-8');
?>
<!DOCTYPE html>
<html lang="ko">
	<head>
<?php
	// HTML Header
	Require_Once DIRNAME(__FILE__).'/inc/html.php';
?>
	</head>
	<body>
		<div id="commonLayout">
<?php
	// Header
	Require_Once DIRNAME(__FILE__).'/inc/head.php';
?>
			<div id="idxVisual">
				<div id="idxVisualSlogan">
					<p class="bottoms xfont"><b><span class="xfont darkorange">도전</span>과 <span class="xfont darkorange">열정</span>이 있는<br /><span class="xfont navyblue">예비창업인</span>을 지원합니다!</b></p>
					<p class="bfont bold middleline margin_t_25">
						"그대의 꿈을 현실로"<br />
						"할 수 있다. 하면 된다."<br />
						"도전, 도전, 끝없는 도전"
					</p>
				</div>
				<div id="idxVisualData" class="cycle-slideshow" data-cycle-fx="fadeout" data-cycle-slides="> div">
					<div class="v v1"></div>
					<div class="v v2"></div>
					<div class="v v3"></div>
				</div>
				<div id="idxArticleSlide">
					<p class="t"><a href="#n" class="as_u"><img src="/res/img/index/btn_up.png" alt="" /></a></p>
					<div class="ofh">
						<div class="cycle-slideshow ofh" data-cycle-fx="carousel" data-cycle-slides="> div" data-cycle-carousel-visible="3" data-cycle-carousel-vertical="true" data-cycle-pause-on-hover="true" data-cycle-prev="a.as_u" data-cycle-next="a.as_d">
<?php
	$DB->setProcName('SP_WEB_BOARD_MULTI_LIST_SELECT');
	$DB->bind_param('SEARCH_CATEGORY', NULL, _VARCHAR, 200, _PARAM_IN);
	$DB->bind_param('SEARCH_TYPE', NULL, _VARCHAR, 20,			_PARAM_IN);
	$DB->bind_param('SEARCH_KEYWORD', NULL, _VARCHAR, 200, _PARAM_IN);
	$DB->bind_param('START', 0, _INT, 11, _PARAM_IN);
	$DB->bind_param('END', 5, _INT, 11, _PARAM_IN);
	$isMainBbs = false;
	
	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();
		
		If($result) {
			$isMainBbs = True;
			Foreach($result as $list) {
?>
							<div>
								<dl>
									<dt class="bfont"><strong>
										<span>[<?php Echo $list['BOARD_NM']; ?>]</span> <?php Echo (mb_strlen(htmlspecialchars($list['TITLE'])) > 18) ? mb_substr(htmlspecialchars($list['TITLE']), 0, 15, 'UTF-8') . '...' : htmlspecialchars($list['TITLE']); ?></strong>
									</dt>
									<dd>
										<?php Echo (mb_strlen(Strip_Tags(Str_Replace('&nbsp;', ' ', $list['CONTENTS']))) > 65) ? mb_substr(Strip_Tags(Str_Replace('&nbsp;', ' ', $list['CONTENTS'])), 0, 62, 'UTF-8') . '...' : Strip_Tags(Str_Replace('&nbsp;', ' ', $list['CONTENTS'])); ?>
									</dd>
									<dd><?php Echo str_replace('-', '. ', $list['CREATE_DT']); ?>.</dd>
									<dd class="detail"><a href="/board/?id=<?php Echo $list['BOARD_ID']; ?>&mode=V&idx=<?php Echo $list['IDX']; ?>"><img src="/res/img/index/btn_detail.png" alt="자세히보기" /></a></dd>
								</dl>
							</div>
<?php
			}
		}
	}

	If($isMainBbs === False) {
?>
							<div>
								<dl>
									<dt>등록된 글이 없습니다.</dt>
								</dl>
							</div>
<?php
	}
?>
						</div>
					</div>
					<p class="d"><a href="#n" class="as_d"><img src="/res/img/index/btn_down.png" alt="" /></a></p>
				</div>
			</div>
			<div id="idxSideNavi">
				<div>
					<p class="prev"><a href="#n"><span class="access">이전</span></a></p>
					<ul class="ofh nostyle flefts centers hfont bold cycle-slideshow" data-cycle-fx="carousel" data-cycle-carousel-visible="5" data-allow-wrap="false" data-cycle-slides="> li" data-cycle-pause-on-hover="true" data-cycle-prev="div#idxSideNavi>div>p.prev>a" data-cycle-next="div#idxSideNavi>div>p.next>a">
						<li><a href="/sub.php?code=010100"><span>센터소개</span></a></li>
						<li><a href="/sub.php?code=020100"><span>창업마일리지</span></a></li>
						<li><a href="/mypage/report/"><span>창업실습보고서</span></a></li>
						<li><a href="/sub.php?code=040100"><span>창업동아리</span></a></li>
						<li><a href="/board/?id=notice"><span>커뮤니티</span></a></li>
					</ul>
					<p class="next"><a href="#n"><span class="access">다음</span></a></p>
				</div>
			</div>
			<div id="idxCont" class="ofh flefts">
				<div>
					<p class="hfont"><b>공지사항 및 창업관련정보</b></p>
					<ul class="nostyle">
<?php
	$DB->setProcName('SP_WEB_BOARD_MULTI_LIST_SELECT');
	$DB->bind_param('SEARCH_CATEGORY', NULL, _VARCHAR, 200, _PARAM_IN);
	$DB->bind_param('SEARCH_TYPE', NULL, _VARCHAR, 20,			_PARAM_IN);
	$DB->bind_param('SEARCH_KEYWORD', NULL, _VARCHAR, 200, _PARAM_IN);
	$DB->bind_param('START', 1, _INT, 11, _PARAM_IN);
	$DB->bind_param('END', 5, _INT, 11, _PARAM_IN);

	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();
	}

	Foreach ($result as $list) {
		$addStyle = "";
		If($list['NOTI'] == 1) $addStyle = ' style="color:red"';
?>
						<li class="bold"><a href="/board/?id=<?php Echo $list['BOARD_ID']; ?>&amp;mode=V&amp;idx=<?php Echo htmlspecialchars($list['IDX']); ?>"<?php Echo $addStyle; ?>><?php Echo (mb_strlen(htmlspecialchars($list['TITLE'])) > 32) ? mb_substr(htmlspecialchars($list['TITLE']), 0, 28, 'UTF-8') . '...' : htmlspecialchars($list['TITLE']); ?></a></li>
<?php
	}
?>
					</ul>
					<p class="more"><a href="/board/?id=notice"><img src="/res/img/index/btn_more_12x12.png" alt="" /></a></p>
				</div>
				<div><a href="/board/?id=qna"><img src="/res/img/index/btn_qna.png" alt="Q&amp;A" /></a></div>
				<div>
					<p class="hfont"><b>특강 및 경진대회</b></p>
					<ul class="nostyle">
<?php
	$DB->setProcName('SP_WEB_LECTURE_COURSE_INDEX_SELECT');
	$DB->bind_param('P_COUNT', 5, _INT, 11, _PARAM_IN);

	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();
	}

	If ($result) {
		Foreach ($result as $list) {
?>
						<li class="bold"><a href="/lecture/"><?php Echo (mb_strlen(htmlspecialchars($list['LECTURE_NM'])) > 32) ? mb_substr(htmlspecialchars($list['LECTURE_NM']), 0, 28, 'UTF-8') . '...' : htmlspecialchars($list['LECTURE_NM']); ?></a></li>
<?php
		}
	} else {
?>
						<li class="bold">등록된 일정이 없습니다.</li>
<?php
	}
?>
					</ul>
					<p class="more"><a href="/lecture/"><img src="/res/img/index/btn_more_12x12.png" alt="" /></a></p>
				</div>
				<div class="photo">
					<p class="hfont"><b>포토 갤러리</b></p>
					<div>
						<ul class="ofh nostyle flefts photos">
<?php
	$DB->setProcName('SP_WEB_BOARD_LIST_SELECT');
	$DB->bind_param('BOARD_ID', 'photo', _VARCHAR, 20, _PARAM_IN);
	$DB->bind_param('SEARCH_CATEGORY', NULL, _VARCHAR, 200, _PARAM_IN);
	$DB->bind_param('SEARCH_TYPE', NULL, _VARCHAR, 20,			_PARAM_IN);
	$DB->bind_param('SEARCH_KEYWORD', NULL, _VARCHAR, 200, _PARAM_IN);
	$DB->bind_param('START', 1, _INT, 11, _PARAM_IN);
	$DB->bind_param('END', 3, _INT, 11, _PARAM_IN);

	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();
	}

	Foreach ($result as $list) {
?>
							<li>
								<a href="/board/?id=photo&amp;mode=V&amp;idx=<?php Echo $list['IDX']; ?>">
									<p class="img"><?php If (!Empty($list['FILE_NAME'])) { ?><img src="/board/getImage.php?idx=<?php Echo $list['IDX']; ?>&temp=i_thumb_" width="170" height="110" alt="" /><?php } Else Echo '<img src="/res/img/noimg.png" alt="" width="170" height="110" />'; ?></p>
									<p class="ofh center margin_t_10" style="width:172px"><?php Echo (mb_strlen($list['TITLE']) > 18) ? mb_substr($list['TITLE'], 0, 15, 'UTF-8') . '...' : $list['TITLE']; ?></p>
								</a>
							</li>
<?php
	}
?>
						</ul>
					</div>
					<p class="more"><a href="/board/?id=photo"><img src="/res/img/index/btn_more_12x12.png" alt="" /></a></p>
				</div>
				<div>
					<div>
						<img src="/res/img/tmp.png" alt="" />
					</div>
				</div>
			</div>

<?php
	// Footer
	Require_Once DIRNAME(__FILE__).'/inc/foot.php';
?>
		</div>
	</body>
</html>