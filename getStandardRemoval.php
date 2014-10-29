
<?php

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include('ms-dbfunc.php');

	$ms_connect = ms_connect();
	$arr_data = array();

	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
	{

		
		$sql = "SELECT
					 IM.NonSerializedInventoryItemMovementHistoryId as id
					,IT.itemcode as sku
					,im.Quantity as qty
					,im.userid as userid
					,im.Comments as comments
					,h.Barcode as toloc
				    ,CONVERT(CHAR(19),im.DATETIMESTAMP,120) as logged
				FROM 
					NonSerializedInventoryItemMovementHistory as IM
					JOIN 
					ItemType as IT on (IM.ItemTypeId = IT.ItemTypeId)
					JOIN 
					[User] as u on  (IM.userid = u.userid)
					JOIN 
					Holder as H on (H.HolderId = IM.ToHolderId)
					LEFT JOIN 
					Holder as h1  on (H1.HolderId = IM.FromHolderId)  
					left JOIN 
					Location as L ON (IM.ToHolderId = L.HolderId)
					LEFT JOIN 
					Location as L1  on (IM.FromHolderId = L1.HolderId)
				WHERE 
					DateTimeStamp >= DATEADD(MINUTE,-10, SYSDATETIME())
					AND
					Comments not in ('Picking', 'Moved using the device.', 'Transfered to pack bench.', 'Picking', 'Despatched', 'Moved with put-away.', 'Received through mobile unit', 'Modified through adjustment module')
				   AND 
					Comments NOT LIKE 'ACT201%'
					AND 
					h.Barcode ='STD.REM' 
				order by id
					";

		$arr_data = ms_query_all($sql);


	}

	$data = base64_encode(serialize($arr_data));
	echo $data;
?>