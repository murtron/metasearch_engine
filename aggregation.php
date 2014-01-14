<?php

function aggregateResults($doc_list)
{
	$_SESSION['ranked_list'] = removeDuplicates($doc_list); 		
	$_SESSION['ranked_list'] = sortResults($_SESSION['ranked_list']);
}

//function to remove duplicate results
function removeDuplicates($array)
{
	for ($i = 0; $i < count($array); $i++)
	{
		for ($j = $i+1; $j < count($array); $j++)
		{
			if (strcmp($array[$j][0],$array[$i][0]) === 0)
			{ 	
				array_splice($array,$j, 1);
			}
		} 
	}
	return $array;
}

//function to sort results, quicksort with Condorcet fuse
function sortResults($doc_list) 
{
	if(count($doc_list) <= 1) 
		return $doc_list;
	
	//select and remove pivot	
	$pivot = $doc_list[round(count($doc_list)/2)];
	unset($doc_list[round(count($doc_list)/2)]);

	$low = $high = array();
	
	foreach($doc_list as $key => $doc) 
	{
		$count = 0;

		//loop through each set of results
		foreach ($_SESSION['results'] as $system)
		{
			foreach ($system as $result) 
			{
				if ($result[0] == $doc[0])
				{	
					$count++;
					break;
				}
				if ($result[0] == $pivot[0])
				{
					$count--;
					break;
				}	
			}
		}
		
		//put doc in lower array
		if($count > 0) 
		{	
			$low [] = $doc;
		} 
		//put doc in higher array
		else 
		{
			$high[] = $doc;
		}
	}
	return array_merge(sortResults($low), array($pivot), sortResults($high));
}


?>