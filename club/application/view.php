<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');

	$view = false;

	// 등록신청서 조회
	$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_DETAIL_SELECT');
	$DB->bind_param('P_YEAR', 				$YEAR,									_VARCHAR,	4,			_PARAM_IN);
	$DB->bind_param('P_HAGGI',		 		$HAGGI,								_VARCHAR,	6,			_PARAM_IN);
	$DB->bind_param('P_TEAM_CD',		$TEAM_CD,							_VARCHAR,	10,		_PARAM_IN);
	$DB->bind_param('P_USER_NO', 		$USER_NO,							_VARCHAR,	20,		_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$Result = Array();
		$Result = $DB->get_fetch_assoc();

		If($Result){
			$view = true;

			$REG_NAME 				= $Result[0]['REG_NAME'];					// 성명
			$REG_MAJOR 				= $Result[0]['REG_MAJOR'];					// 소속학과
			$REG_GRADE 				= $Result[0]['REG_GRADE'];					// 학년
			$REG_HP_NO 				= $Result[0]['REG_HP_NO'];					// 연락처

			$CLUB_NM 					= $Result[0]['CLUB_NM'];						// 팀명
			$ITEM_NM 					= $Result[0]['ITEM_NM'];						// 창업아이템명
			$CORE_TECH 				= $Result[0]['CORE_TECH'];					// 핵심기술

			$FILE_NAME 				= $Result[0]['FILE_NAME'];						// 제출문서
			$REALFILE_NAME 		= $Result[0]['REALFILE_NAME'];				// 제출문서

			$BIZ_NAME 					= $Result[0]['BIZ_NAME'];						// 기업명
			$BIZ_NUM 					= $Result[0]['BIZ_NUM'];						// 사업자등록번호
			$BIZ_START_DT 			= $Result[0]['BIZ_START_DT'];				// 창업일
			$BIZ_TYPE 					= $Result[0]['BIZ_TYPE'];						// 업종
			$BIZ_SALES 				= $Result[0]['BIZ_SALES'];						// 매출
			$BIZ_EMP_CNT 			= $Result[0]['BIZ_EMP_CNT'];				// 고용인원수

			$IPR_APPLY_YN 			= $Result[0]['IPR_APPLY_YN'];				// 출원여부
			$IPR_ING_CNT 			= $Result[0]['IPR_ING_CNT'];					// 출원중
			$IPR_PATENT_CNT 		= $Result[0]['IPR_PATENT_CNT'];			// 등록완료
			$IPR_PATENT_NAME 	= $Result[0]['IPR_PATENT_NAME'];		// 발명의 명칭
			
			$STATUS 					= $Result[0]['STATUS'];							// 상태
		}
	}

	If(!$view) location(2, '동아리 등록신청서 조회에 실패하였습니다.', '/club/application/');

	Switch($STATUS){
		Case '승인' : $STATUS_CLASS = 'stat1'; Break;
		Case '반려' : $STATUS_CLASS = 'stat2'; Break;
		Default : $STATUS_CLASS = 'stat4';
	}
?>
							<div class="ofh hfont black margin_b_10">
								<p class="fleft"><b>상태</b> : <span class="stat <?php Echo $STATUS_CLASS; ?>"><?php Echo $STATUS; ?></span></p>
							</div>

							<div class="table_view2">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15">
                                        <col class="w31">
                                        <col class="w15">
                                    </colgroup>
                                    <tbody>
										<tr>
											<th colspan="6">신청자 정보 (팀장)</th>
										</tr>
										<tr>
											<th>년도/학기</th>
											<td colspan="3"><?php Echo $YEAR . '년도 ' . $HAGGI; ?></td>
										</tr>
										<tr>
											<th>학번</th>
											<td><?php Echo $USER_NO; ?></td>
											<th>성명</th>
											<td><?php Echo $REG_NAME; ?></td>
										</tr>
										<tr>
											<th>소속학과/학년</th>
											<td><?php Echo $REG_MAJOR; ?> / <?php Echo $REG_GRADE; ?>학년</td>
											<th>연락처</th>
											<td><?php Echo $REG_HP_NO; ?></td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="margin_t_10 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15" />
                                        <col />
                                    </colgroup>
                                    <tbody>
										<tr>
											<th colspan="2">창업동아리 정보</th>
										</tr>
										<tr>
                                            <th>팀명</th>
                                            <td><?php Echo $CLUB_NM; ?></td>
										</tr>
                                        <tr>
                                            <th>창업아이템명</th>
                                            <td><?php Echo $ITEM_NM; ?></td>
                                        </tr>
                                        <tr>
                                            <th>핵심기술</th>
                                            <td><?php Echo $CORE_TECH; ?></td>
                                        </tr>
										<tr>
											<th>제출문서</th>
											<td>
<?php
									If ($FILE_NAME != '') {
										Echo '<a href="/inc/download.php?type=nonMajorClubApplication&year='.$YEAR.'&haggi='.UrlEncode($HAGGI).'&teamCd='.$TEAM_CD.'">'.$REALFILE_NAME.'</a>';
									} Else Echo '&nbsp;';
?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="margin_t_10 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15" />
                                        <col class="w31" />
                                        <col class="w15" />
                                        <col />
                                    </colgroup>
                                    <tbody>
										<tr>
											<th colspan="4">창업 현황 (사업자등록 또는 법인등록한 동아리에 한해 작성)</th>
										</tr>
                                        <tr>
                                            <th>기업명</th>
                                            <td><?php Echo $BIZ_NAME; ?></td>
                                            <th>사업자등록번호</th>
                                            <td><?php Echo $BIZ_NUM; ?></td>
                                        </tr>
                                        <tr>
                                            <th>창업일</th>
                                            <td><?php Echo $BIZ_START_DT; ?></td>
                                            <th>업종</th>
                                            <td><?php Echo $BIZ_TYPE; ?></td>
                                        </tr>
                                        <tr>
                                            <th>매출<br />(전년도기준)</th>
                                            <td><?php Echo $BIZ_SALES; ?> 원</td>
                                            <th>고용인원수</th>
                                            <td><?php Echo $BIZ_EMP_CNT; ?> 명</td>
                                        </tr>
									</tbody>
								</table>
							</div>

							<div class="margin_t_10 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15">
                                        <col />
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th colspan="2">지적재산권 출원 및 등록 현황</th>
										</tr>
										<tr>
                                            <th>출원여부</th>
                                            <td>
												<?php Echo ($IPR_APPLY_YN == 'Y') ? '출원' : '미출원'; ?>
												<span style="margin-left:60px;">출원중 &nbsp;&nbsp;&nbsp;<?php Echo $IPR_ING_CNT; ?> 건</span>
												<span style="margin-left:40px;">등록완료 &nbsp;&nbsp;&nbsp;<?php Echo $IPR_PATENT_CNT; ?> 건</span>
											</td>
                                        </tr>
                                        <tr>
                                            <th>발명의 명칭</th>
                                            <td><?php Echo $IPR_PATENT_NAME; ?></td>
                                        </tr>
									</tbody>
								</table>
							</div>

							<div id="winTable" class="margin_t_10 margin_b_50 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w21">
                                        <col class="w21" />
                                        <col width="120">
                                        <col />
                                    </colgroup>
									<thead>
                                        <tr>
                                            <th colspan="5">입상 및 지원사업 선정경력</th>
										</tr>
										<tr>
                                            <th>대회명</th>
                                            <th>수상내역</th>
                                            <th>수상일자</th>
                                            <th>시행기관</th>
                                        </tr>
									</thead>
                                    <tbody>
<?php
		$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_CAREER_SELECT');
		$DB->bind_param('P_YEAR', 				$YEAR,						_VARCHAR,	4,			_PARAM_IN);
		$DB->bind_param('P_HAGGI', 			$HAGGI,					_VARCHAR,	6,			_PARAM_IN);
		$DB->bind_param('P_TEAM_CD', 		$TEAM_CD,				_VARCHAR,	10,		_PARAM_IN);
		$statcareer = false;
		If ($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();

			If($Result) {
				$statcareer = true;
				Foreach ($Result AS $LIST) {
?>
										<tr>
											<td class="text-center"><?php Echo $LIST['CONTEST_NM']; ?></td>
											<td class="text-center"><?php Echo $LIST['AWARD']; ?></td>
											<td class="text-center"><?php Echo $LIST['AWARD_DT']; ?></td>
											<td><?php Echo $LIST['AGENCY']; ?></td>
										</tr>
<?php
				}
			}
		}
		// 등록된 내역 없는 경우
		If($statcareer === false) {
?>
										<tr id="ENTRY1_ROW_0">
											<td colspan="4" class="center">&nbsp;</td>
										</tr>
<?php
		}
?>
                                    </tbody>
                                </table>

								<div id="boardBtn">
									<div class="center">
										<a href="?">목록</a>
<?php
	If($STATUS != '승인') {
?>
										<a href="?mode=M&year=<?php Echo $YEAR; ?>&haggi=<?php Echo $HAGGI; ?>&teamCd=<?php Echo $TEAM_CD; ?>">수정</a>
<?php
	}
?>
									</div>
								</div>
							</div>