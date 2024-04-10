<?php

	require '../config.php';
	
	$term=GETPOST('term');
	$get = GETPOST('get');
	$table = GETPOST('table');
	
	switch ($get) {
		case 'nextref':
			
			echo json_encode(_nextref($term,$table));
						
			break;
	}

function _nextref($term,$table) {
	global $db, $conf,$user,$langs;
	
	
	if($table == 'projet'){
  		$prefix_list =  getDolGlobalString('DANDELION_DEFAULT_PREFIX_PROJECT');
		$total_nb_char = getDolGlobalInt('DANDELION_TOTAL_NB_CHAR_PROJECT');
		$nb_min_char = getDolGlobalInt('DANDELION_BASE_NB_CHAR_PROJECT');
	} 
	else {
		$prefix_list =  getDolGlobalString('DANDELION_DEFAULT_PREFIX');
		$total_nb_char = getDolGlobalInt('DANDELION_TOTAL_NB_CHAR');
		$nb_min_char = getDolGlobalInt('DANDELION_BASE_NB_CHAR');
	}
	
	dol_include_once('/core/lib/functions2.lib.php');
		 
	$sql = "SELECT DISTINCT( CHAR_LENGTH( ref) ) as l FROM ".MAIN_DB_PREFIX.$table." WHERE ref LIKE '".$term."%' ";
	//echo $sql;
	$res = $db->query($sql);
	$TPrefix=array( $total_nb_char );
  	while($obj = $db->fetch_object($res)) {
  		$TPrefix[] = $obj->l;
  	}
  
  	$Tab = array(); $length_term = strlen($term);
  
  	foreach($TPrefix as $prefix_length) {
  		
		$length = $prefix_length - $length_term;
		
		if($length<=0) continue;
		
		$mask = $term.'{'.str_pad('', $length,'0').'}';
		
		$ref = get_next_value($db,$mask,$table,'ref'," AND ref LIKE '".$term."%' ");
		if(!in_array($ref,$Tab))$Tab[] = $ref;
		
  	}
		
	
	
	return $Tab;
	
}
