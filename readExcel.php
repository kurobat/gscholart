<?php

	header('Content-Type: text/html; charset=UTF-8');
	require_once("php/facade.php");
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', 10000);
	
	function retrieveCSVArray($file){
		$array=Array();
		$file=fopen($file,"r");
		fgetcsv($file);
		if ($file!== FALSE) {
			while (($data = fgetcsv($file)) !== FALSE) {
				$array[]=$data;
			}
			fclose($file);
		}
		return $array;
	}
	
	function retrieveCSVArrayByYear($file){
		$array=Array();
		$currentYear=date("Y")-1;
		$file=fopen($file,"r");
		fgetcsv($file);
		if ($file!== FALSE) {
			while (($data = fgetcsv($file)) !== FALSE) {
				if((!empty($data[3]) AND $data[3]!='-') AND $data[5]==$currentYear){
					$array[]=$data;
					
				}
			}
			fclose($file);
		}
		//var_dump($array[12680]);
		return $array;
		
	}
	
	$facade=new Facade();
	
	$category=retrieveCSVArray("category.csv");
	$cwts=retrieveCSVArrayByYear("cwts.csv");

	$scimago=retrieveCSVArray("scimagojr.csv");
	
	//var_dump($cwts);
	
	$facade->beginTransaction();
		
	for($i=0;$i<count($category);$i++){
		$data=Array("id"=>$category[$i][0],
					"name"=>$category[$i][1]);
		
		$quantity=$facade->existsCategory(Array("name"=>$category[$i][1]));
		
		if($quantity==0){
			$facade->insertCategory($data);
		}
	}
	
	for($i=0;$i<count($scimago);$i++){

		if(!empty($scimago[$i][3])){
				
			$data=Array("name"=>$scimago[$i][13]);
			$quantity=$facade->existsCountry($data);
			
			if($quantity==0){
				$facade->insertCountry($data);
			}
			
			$id_country=$facade->retrieveIdCountry($data);
			
			$issn=explode(" ",$scimago[$i][3]);
			$quantity=$facade->existsJournal(Array("issn"=>$issn[1]));
			
			if($quantity==0){
				
				if(empty($scimago[$i][2])){
					$scimago[$i][2]="n";
				}
				
				$data=Array("issn"=>$issn[1],
							"title"=>$scimago[$i][1],
							"type"=>$scimago[$i][2],
							"sjr"=>$scimago[$i][4],
							"h_index"=>$scimago[$i][5],
							"total_docs"=>$scimago[$i][6],
							"total_docs_three_years"=>$scimago[$i][7],
							"total_refs"=>$scimago[$i][8],
							"total_cites"=>$scimago[$i][9],
							"citable_docs"=>$scimago[$i][10],
							"avg_citation_doc_two_years"=>$scimago[$i][11],
							"avg_amount_refs_doc"=>$scimago[$i][12],
							"id_country"=>$id_country[0]['id']);
				//var_dump($data);
				$facade->insertJournal($data);
			}		
		}
	}
	
	$issnList=$facade->retrieveJournalISSN();
	for($i=0;$i<count($cwts);$i++){
		$issn=str_replace('-','',$cwts[$i][3]);
			
		foreach($issnList as $il){
			if($issn==$il['issn'] ){	
				$category=str_replace(" ","",$cwts[$i][4]);
				$category=explode(";",$category);
				
				for($j=0;$j<count($category);$j++){
					if($category[$j]!="NULL"){
						$quantity=$facade->existsJournalCategory(Array("id_journal"=>$issn,"id_category"=>$category[$j]));
						if($quantity==0){
							echo $issn."-->".$category[$j]."<br/>";
							$data=Array("id_journal"=>$issn,
										"id_category"=>$category[$j]);
						
							$facade->insertJournalCategory($data);
						}
					}
				}
			}
		}
	}
	
	
	$ljournal=$facade->retrieveJournalWithoutCategory();
	
	if($ljournal!=null){
		foreach($ljournal as $lj){
			$facade->deleteJournal(Array("issn"=>$lj['issn']));
		}
	}
	
	$lcategory=$facade->retrieveCategoriesWithoutJournal();
	
	if($lcategory!=null){
		foreach($lcategory as $lc){
			$facade->deleteCategory(Array("id"=>$lc['id']));
		}
	}
	
	$lcountry=$facade->retrieveCountriesWithoutJournal();
	
	if($lcountry!=null){
		foreach($lcountry as $lc){
			$facade->deleteCountry(Array("id"=>$lc['id']));
		}
	}
	
	
	if($facade->consolidate()){
		echo $facade->getMessage();
	}else{
		echo $facade->getMessage();
	}

	

?>