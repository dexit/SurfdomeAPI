 
<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include('ms-dbfunc.php');

$ms_connect = ms_connect();
$arr_data = array();


if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
{
  
   $sql = "SELECT 
     [Barcode]
       ,[Name]
  FROM [PeopleVox.OneBusinessPortal.SurfdomeQa2536].[dbo].[Holder]
  where Barcode like 'SD1.RT.%'";

	$arr_data = ms_query_all($sql);
}

$data = base64_encode(serialize($arr_data));

echo $data;

?>