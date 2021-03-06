<?php


	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include('ms-dbfunc.php');

	$ms_connect = ms_connect();
	$arr_data = array();

	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
	{

		$sql = "SELECT 
					HolderId
					,Barcode
				FROM 
					Holder h 
				WHERE 
					h.Barcode like 'PP%' AND H.Barcode !='PP'";
		
		$arr_pp_holder = ms_query_all($sql);


		foreach ($arr_pp_holder as $pp_holder)
		{


				$tsql = "Exec  Holder_InventoryCheck @HolderId=?";   

				$holderid =  $pp_holder['HolderId'];

				$params =   array($holderid);
				$stmt = sqlsrv_query($ms_connect, $tsql, $params);

				$arr_rows = array();

				$arr_items = array();
				while($row =  sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
				{
					$arr_rows[] = $row;
					$arr_items[] = $row['ItemTypeId'];
				}


				if (count($arr_rows) >0)
				{
					$arr_data[$pp_holder['Barcode']]['holder']  = $arr_rows;

					$str_sql = "(ad.ItemTypeId = ". implode(' or ad.ItemTypeId = ', $arr_items) . ")";
					
					$sql = "SELECT 
								so.SalesOrderNumber
								,so.SalesOrderId
								,ag.ActionGroupId
								,ad.ItemTypeId
								,so.statusid
								,ag.name as actionName
								FROM ActionGroup ag 
								  join 
								  ActionDetail ad on (ag.ActionGroupId = ad.ActionGroupId)
								  join 
								 [SalesOrderActionGroup] sg on ag.ActionGroupId = sg.ActionGroupId
								 join 
								 SalesOrder so on (sg.SalesOrderId = so.SalesOrderId)
								  
								  WHERE 
								  ag.ActionTypeId = 1 
								  and so.StatusId not in (2,5) 
								  and ad.SkipReasonId is null
								 and ".$str_sql;
				
					$arr_t = ms_query_all($sql);

					$arr_data[$pp_holder['Barcode']]['iteminbox'] = $arr_t;

					
					if (count($arr_data[$pp_holder['Barcode']]['iteminbox']))
					{

							$SalesOrderId = $arr_data[$pp_holder['Barcode']]['iteminbox'][0]['SalesOrderId'];

							$so = "";
							foreach($arr_data[$pp_holder['Barcode']]['iteminbox']  As $t)
							{
								if ($t['SalesOrderNumber'] !=  $so)
								{	
									$arr_data[$pp_holder['Barcode']]['SalesOrderNumber'][] = $t['SalesOrderNumber'];
									$so =  $t['SalesOrderNumber'];
								}
							}


							$ag = "";
							foreach($arr_data[$pp_holder['Barcode']]['iteminbox']  As $t)
							{
								if ($t['actionName'] !=  $ag)
								{	
									$arr_data[$pp_holder['Barcode']]['actionName'][] = $t['actionName'];
									$ag =  $t['actionName'];
								}
							}



							$sql = "SELECT
										sop.*,
										it.ItemCode,
										it.Name
										FROM 
											[SalesOrderItemPicking] sop
										  join 
										  SalesOrderItem si on (sop.SalesOrderItemId = si.SalesOrderItemId)
										  join
										  ItemType it on (sop.ItemTypeId = it.ItemTypeId)
										  where 
										  si.SalesOrderId = ".  $SalesOrderId ;

							$arr_data[$pp_holder['Barcode']]['salesorderdata'] = ms_query_all($sql);

									
					}
				
				}

		}


	}


	$data = base64_encode(serialize($arr_data));

	echo $data;
