<?php
require 'k_means.php';

//-------------Clustering----------------

function getClusters($doc_list)
{
	$matrix = buildMatrix($doc_list, true);
	
	//convert associative arrays
	foreach($matrix as $key => $doc)
	{
		$matrix[$key] = array_values($doc);
	}
	
	//call kmeans function to determine clusters
	$cluster_map = getClusterMap($matrix);
	
	//array to hold final document clusters
	$clusters = array();
	
	foreach($cluster_map as $clusterID => $cluster)
	{
		foreach($cluster as $docID)
		{
			$clusters[$clusterID][] = $doc_list[$docID];
		}
	}
	
	return $clusters = getLabels($clusters);
}

//get cluster labels
function getLabels($clusters)
{ 
	foreach($clusters as $clusterID => $cluster)
	{
		$doc_frq = buildMatrix($cluster, false);
		
		//take top term as label for cluster
		arsort($doc_frq);
		
		foreach($doc_frq as $term => $df)
		{
			$label = $term;
			//exclude previous labels
			if (!isset($clusters[$label]))
				break;
		}

		//change keys to be labels
		$clusters[$label] = $clusters[$clusterID];
		unset($clusters[$clusterID]);
	
	}
	return $clusters;
}

?>