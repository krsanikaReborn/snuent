<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');
?>
							<p class="mfont bold padding_l_2">멤버를 등록할 동아리를 선택하세요.</p>
<?php
	$stat = false;
	$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_APPLY_LIST_SELECT');
	$DB->bind_param('P_YEAR',		 			$YEAR,														_VARCHAR,	4,			_PARAM_IN);
	$DB->bind_param('P_HAGGI',		 			$HAGGI,													_VARCHAR,	6,			_PARAM_IN);
	$DB->bind_param('P_USER_NO',			$USER_NO,												_VARCHAR,	20,		_PARAM_IN);
	
	$totalCount = 0;
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
										<tr onclick="location.href = '?mode=W&teamCd=<?php Echo $LIST['TEAM_CD']; ?>';">
											<td headers="th_Year"><?php Echo $LIST['YEAR']; ?></td>
											<td headers="th_Haggi"><?php Echo $LIST['HAGGI']; ?></td>
											<td headers="th_ClubNm">
												<noscript><a href="?mode=W&year=<?php Echo $LIST['YEAR']; ?>&haggi=<?php Echo UrlEncode($LIST['HAGGI']); ?>&teamCd=<?php Echo $LIST['TEAM_CD']; ?>"></noscript>
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
										<tr><td style="height:40px" colspan="5">승인된 동아리가 없습니다.</td></tr>
<?php
	}
?>
									</tbody>
								</table>
							</div>