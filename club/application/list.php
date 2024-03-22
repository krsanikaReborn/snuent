<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');
	
	// 동아리등록신청 목록 조회
	$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_LIST_SELECT');
	$DB->bind_param('P_YEAR', 				$YEAR,									_VARCHAR,	4,			_PARAM_IN);
	$DB->bind_param('P_HAGGI',		 		$HAGGI,								_VARCHAR,	6,			_PARAM_IN);
	$DB->bind_param('P_USER_NO', 		$USER_NO,							_VARCHAR,	20,		_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$Result = Array();
		$Result = $DB->get_fetch_assoc();
		$totalCount = Count($Result);
	}
	
	If($totalCount > 0) {
?>
						<script>
							$(function(){
								$('table#applicationList tr').on({
									mouseenter: function(){
										$(this).addClass('bold blue');
										$(this).find('td').css('background-color', '#ddd');
									},
									mouseleave: function(){
										$(this).removeClass('bold blue');
										$(this).find('td').css('background-color', '#fff');
									}
								});
								$('#applicationList').find('tr:not(:first)').css('cursor', 'pointer');
							});
						</script>
<?php
	}
?>
						<p class="margin_b_10 hfont bold blue_dot_small padding_l_15 ">동아리 신청내역</p>
						<noscript><p class="fright">동아리명을 클릭하시면 상세내용을 보실 수 있습니다.</p></noscript>
						<div class="margin_b_20 table_listA">
							<table id="applicationList">
								<thead>
									<tr>
										<th id="th_Year" scope="col" width="60">년도</th>
										<th id="th_Haggi" scope="col" width="60">학기</th>
										<th id="th_ClubNm" scope="col">동아리명</th>
										<th id="th_LeaderNm" scope="col" width="120">팀장</th>
										<th id="th_Status" scope="col" width="60">상태</th>
									</tr>
								</thead>
								<tbody>
<?php
	If($totalCount > 0) {
		Foreach($Result AS $LIST) {
?>
									<tr onclick="location.href = '?mode=V&year=<?php Echo $LIST['YEAR']; ?>&haggi=<?php Echo UrlEncode($LIST['HAGGI']); ?>&teamCd=<?php Echo $LIST['TEAM_CD']; ?>';">
										<td headers="th_Year"><?php Echo $LIST['YEAR']; ?></td>
										<td headers="th_Haggi"><?php Echo $LIST['HAGGI']; ?></td>
										<td headers="th_ClubNm">
											<noscript><a href="?mode=V&teamCd=<?php Echo $LIST['TEAM_CD']; ?>"></noscript>
											<?php Echo $LIST['CLUB_NM']; ?>
											<noscript></a></noscript>
										</td>
										<td headers="th_LeaderNm"><?php Echo $LIST['LEADER_NM']; ?></td>
										<td headers="th_Status"><?php Echo $LIST['STATUS']; ?></td>
									</tr>
<?php
		}
	} Else {
?>
									<tr>
										<td colspan="5" style="height:80px">동아리등록신청 내역이 없습니다.<br />아래의 신청하기 버튼을 클릭하여 신청할 수 있습니다.</td>
									</tr>
<?php
	}
?>
								</tbody>
							</table>
						</div>
<?php
	/*
?>
						<div class="alert alert-info">
							신청기간이 아닙니다.
						</div>
<?php
	*/
?>
						<div id="boardBtn">
							<div class="fright">
								<a href="?mode=W">신청하기</a>
							</div>
						</div>