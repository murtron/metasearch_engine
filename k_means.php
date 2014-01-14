<?php

function getClusterMap($matrix)
{
	//pick initial seeds randomly
	for($i=0;$i<5;$i++)
	{
		$centroids[] = $matrix[rand(0, count($matrix)-1)];
	}
	
	//assign documents to new clusters and update centroids x10 or until there are no changes made
	for($i=0;$i<10;$i++)
	{
		$stop = $centroids;
		$clusters = assignDocuments($matrix, $centroids);
		$centroids = updateCentroids($matrix, $clusters);
		if($centroids == $stop) break;
	}
	
	//take out weights and return an array with cluster/doc values
	foreach($clusters as $clusterID => $cluster)
	{
		foreach($cluster as $docID => $doc)
		{
			$cluster_map[$clusterID][] = $docID;
		}
	}

	return $cluster_map;
}
function assignDocuments($matrix, $centroids)
{
	//loop over each document in the matrix
	foreach($matrix as $docID => $doc)
	{
		$min_cos_sim = -1;
		$closest_centroid = NULL;
		
		//loop over each centroid and calculate the cosine similarity between doc and centroid
		foreach($centroids as $centroidID => $centroid)
		{
			$cos_sim = getCosineSim($centroid, $doc);
			
			//find the closest centroid to the document and assign it to that cluster
			if($cos_sim > $min_cos_sim)
			{
				$min_cos_sim = $cos_sim;
				$closest_centroid = $centroidID;	
			}
		}
			
		$clusters[$closest_centroid][$docID] = $doc;
	}
	return $clusters;
}
//update centroids
function updateCentroids($matrix, $clusters)
{
	//get the average length of the vectors in each cluster
	foreach($clusters as $clusterID => $cluster)
	{
		$dimensions = count($matrix[0]);
		for ($i = 0;$i < $dimensions;$i++)
		{
			$total_weight = 0;
			foreach($cluster as $doc)
			{	
				$total_weight += $doc[$i];
			}
			$new_centroid[$i] = $total_weight/$dimensions;
		}
		$new_centroids[$clusterID] = $new_centroid;
	}
	
	return $new_centroids;
}

//returns dot product of each vector
function getCosineSim($centroid, $doc)
{
	$total = 0;
	foreach($centroid as $dimension => $weight)
	{
		$total += $weight * $doc[$dimension];
	}
	
	return $total;
}
?>