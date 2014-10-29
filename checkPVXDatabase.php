<?php

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include('ms-dbfunc.php');

	$ms_connect = ms_connect();
	$basePath   = 'D:\\log\\';

	$date  = date('Ymd');

	$path =  $basePath ."\\test\\";


	if (is_dir($path) === false)
	{
		echo $path;
		echo "folder doesn't exist". $path;
		mkdir($path);
	}

	$path =  $basePath ."\\test\\".$date;

	if (is_dir($path) === false)
	{
		echo $path;
		echo "folder doesn't exist". $path;
		mkdir($path);
	}



	$filename = $path."\\tables.csv";
	$fp       = fopen($filename, 'w');
	$sql = "Select name from sysobjects where type like 'u' order by name asc";

	$arr_tables = ms_query_col($sql);


	foreach($arr_tables as $table)
	{	
		$arr= array();
		$arr[] = $table;
		fputcsv($fp, $arr);

	}
	fclose($fp);


	$path =  $path .'\\tables\\';

	if (is_dir($path) === false)
	{
		echo "folder doesn't exist". $path;
		mkdir($path);
	}


	foreach($arr_tables as $table)
	{

		$sql = "SELECT 
					c.name, 
					c.prec, 
					c.scale, 
					t.name as  type
				  FROM 
					syscolumns c, systypes t, sysobjects o
				  WHERE 
					o.name = '".$table."' AND o.id = c.id AND c.xtype = t.xtype";

		$arr_table_data = ms_query_all($sql);

		$filename = $path."\\".$table.".csv";
		$fp       = fopen($filename, 'w');

		foreach ($arr_table_data as $table_data)
		{
			fputcsv($fp, $table_data);
		}
		fclose($fp);

	}

	//get live system

echo "GET live<br>";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://www.stpvx.com/peoplevox/SurfdomeApi/checkPVXDatabase.php');
	$encoded = "mode=getData";
	//echo $encoded;

	curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 2000);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$ff =curl_exec($ch);
	$data = $ff;
	curl_close($ch);

	$arr_data = unserialize(base64_decode($data));


	print_r($arr_data);

	$path =  $basePath ."\\live\\";
	if (is_dir($path) === false)
	{
		echo $path;
		echo "folder doesn't exist". $path;
		mkdir($path);
	}

	
	$path =  $basePath ."\\live\\".$date;

	if (is_dir($path) === false)
	{
		echo "folder doesn't exist". $path;
		mkdir($path);
	}


	$filename = $path."\\tables.csv";
	$fp       = fopen($filename, 'w');

	foreach($arr_data['list_tables'] as $table)
	{	
		$arr= array();
		$arr[] = $table;
		fputcsv($fp, $arr);

	}
	fclose($fp);


	$path =  $path .'\\tables\\';

	if (is_dir($path) === false)
	{
		echo "folder doesn't exist". $path;
		mkdir($path);
	}



	foreach($arr_data['tables'] as $table => $arr_table_data)
	{

		$filename = $path."\\".$table.".csv";
		$fp       = fopen($filename, 'w');

		foreach ($arr_table_data as $table_data)
		{
			fputcsv($fp, $table_data);
		}
		fclose($fp);

	}
	









?>