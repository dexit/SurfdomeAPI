<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
	ini_set("memory_limit","999999999M");

$arr_sku = array('129157-13','129157-7','129158-10','129158-13','129158-5','129158-7','129160-10','129160-13','129160-5','129160-7','129161-10','129164-38','129164-44','129164-48','129164-51','129165-38','129165-44','129165-51','129166-77','129166-98','129168-67','129168-77','129168-98','129169-12','129169-67','129169-77','129169-98','129170-12','129170-67','129170-77','129170-98','129171-67','129171-77','129171-98','129172-12','129172-67','129172-77','129173-67','129173-77','129174-12','129174-77','129174-98','129175-12','129175-67','129175-77','129175-98','129176-12','129176-67','129176-98','129177-12','129177-67','129177-77','129178-98','129179-12','129179-67','129179-77','129179-98','129180-12','129180-67','129180-77','129181-67','129182-77','129208-77','129208-98','129209-67','129209-77','129209-98','129210-77','129211-77','129213-98','129214-12','129214-14','129214-77','129214-98','129215-12','129215-14','129215-67','129215-77','129215-98','129216-12','129216-14','129216-67','129216-77','129216-98','129217-12','129217-14','129217-67','129217-77','129217-98','129218-12','129218-14','129218-67','129218-77','129218-98','129219-46','129219-46','129219-46','129219-47','129219-47','129220-46','129220-46','129220-46','129220-47','129220-47','129221-38','129221-44','129221-48');
include('ms-dbfunc.php');

	$ms_connect = ms_connect();

$max = rand(0, 50);
$i =0;

while ($i < $max)
{

$sku = $arr_sku[$i];
$i++;

	for($j =0; $j <30; $j++)
	{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://10.37.142.242/peoplevox/SurfdomeApi/itemMovementHistory.php?mode=getData');
	$encoded = "mode=getData&itemcode=".$sku ;
	//	echo $encoded;
	curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 2000);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


//	$ff =curl_exec($ch);
	$rtn = $ff;

	//echo "DDD".$rtn;

	$sql = "SELECT I.ItemCode AS Column1, 
		    I.Name  AS Column2,	
	        I.Barcode AS Column3,
			N.DateTimeStamp AS Column4,
			U.DisplayName AS Column5,
			J.Barcode AS Column6,
			H.Barcode AS Column7,
			N.Quantity AS Column8,
			N.Comments AS Column9,
			S.SalesOrderNumber AS Column10
	FROM NonSerializedInventoryItemMovementHistory N
	INNER JOIN [User] U ON U.UserId = N.UserId
	INNER JOIN ItemType I ON I.ItemTypeId = N.ItemTypeId
	INNER JOIN Holder H ON H.HolderId = N.ToHolderId
	LEFT OUTER JOIN Holder J ON J.HolderId = N.FromHolderId
	LEFT OUTER JOIN (
		SELECT HolderId, SalesOrderNumber
		FROM Pick P
		INNER JOIN SalesOrder SO ON SO.SalesOrderId = P.SalesOrderId
	) S ON ((S.HolderId = H.HolderId AND H.HolderId = 4) OR (S.HolderId = J.HolderId AND J.HolderTypeId = 4))
	WHERE I.Active = 1
	--AND H.Active = 1
	ORDER BY NonSerializedInventoryItemMovementHistoryId DESC";

		//echo $sql;
		$arr_data = ms_query_all($sql);
		print_r($arr_data);
	}
}


?>