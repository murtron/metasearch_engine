<?php

//function to display results 
function displayResults($display_type, $page)
{	
	//clustered display
	if($display_type == 'cluster')
	{
		$per_page = 10;
		$start = ($page - 1)* $per_page;
		
		//display labels for each cluster
		echo '<div id="labels">';
		foreach($_SESSION['cluster_list'] as $label => $cluster)
		{
			echo '<span class="label">
				<a href="results.php?&label='.$label.'">'.$label.' ('.count($cluster).')</a>
				</span>';
		}
		echo '</div>';
		
		//if a cluster has been selected
		if(isset($_GET['label']))
		{
			//title of cluster and page number
			echo '<h3>Cluster \''.$_GET['label'].'\'';
			if(isset($_GET['page'])) echo " - page ".$_GET['page'];
			echo "</h3>";
			
			for($i=$start;$i < ($start + $per_page) && $i < count($_SESSION['cluster_list'][$_GET['label']]); $i++)
			{
				echo 	"<div class = \"result\">
						<a href=\"{$_SESSION['cluster_list'][$_GET['label']][$i][0]}\">{$_SESSION['cluster_list'][$_GET['label']][$i][1]}</a>
						<br />
						<a class=\"url\" href=\"{$_SESSION['cluster_list'][$_GET['label']][$i][0]}\">{$_SESSION['cluster_list'][$_GET['label']][$i][0]}</a>
						<p>{$_SESSION['cluster_list'][$_GET['label']][$i][2]}</p>
						</div>";	
			}
		}
		echo '</div>';
		
		//pagination links
		displayPages($display_type, $page);
	}					
	//aggregated display				
	elseif($display_type == 'aggregate')
	{				
		//display related terms
		displaySuggestions();
		
		$per_page = 10;
		$start = ($page - 1)* $per_page;
		
		echo "<h3>Aggregated results for '{$_SESSION['query']}'";
		if(isset($_GET['page'])) echo " - page ".$_GET['page'];
		echo "</h3>";
			
		for ($i = $start;$i < ($start + $per_page) && $i < count($_SESSION['ranked_list']); $i++)
		{	
			echo 	"<div class = \"result\">
					<a href=\"{$_SESSION['ranked_list'][$i][0]}\">{$_SESSION['ranked_list'][$i][1]}</a>
					<br />
					<a class=\"url\" href=\"{$_SESSION['ranked_list'][$i][0]}\">{$_SESSION['ranked_list'][$i][0]}</a>
					<p>{$_SESSION['ranked_list'][$i][2]}</p>
					</div>";
		}
		echo '</div>';
		
		//pagination links
		displayPages($display_type, $page);
	}				
		//nonaggregated display			
	elseif($display_type == 'nonagg')
	{
		//display related terms
		displaySuggestions();
		
		$per_page = 5;
		$start = ($page - 1)* $per_page;
		
		
		echo "<h3>Non-aggregated results for '{$_SESSION['query']}'";
		if(isset($_GET['page'])) echo " - page ".$_GET['page'];
		echo "</h3>";
		
		foreach ($_SESSION['results'] as $key => $system)
		{
			echo '<div class="engine_name"><h4>'.$key.'</h4></div>';
			for ($i = $start;$i < ($start + $per_page) && $i < count($system); $i++)
			{
				echo 	"<div class = \"result\">
						<a href=\"{$system[$i][0]}\">{$system[$i][1]}</a>
						<br />
						<a class=\"url\" href=\"{$system[$i][0]}\">{$system[$i][0]}</a>
						<p>{$system[$i][2]}</p>
						</div>";
			}
		}
		echo '</div>';
		
		//pagination links
		displayPages($display_type, $page);
	}
}

//function to display page numbers at bottom of page
function displayPages($display_type, $page)
{
	echo '<div id="footer">';
			
	switch ($display_type)
	{
		//for inital clustering display we don't want page numbers
		case 'cluster':
			if (isset($_GET['label']))
			{
				$total_results = count($_SESSION['cluster_list'][$_GET['label']]);
			
				$pages = ceil($total_results / 10);
			}else $pages = 0;
			break;
				
		case 'aggregate':

			$total_results = count($_SESSION['ranked_list']);
			
			$pages = ceil($total_results / 10);
			break;
			
		case 'nonagg':
		
			$total_results = count($_SESSION['results']['Google']) + count($_SESSION['results']['Bing']) + count($_SESSION['results']['Blekko']);		
			
			$pages = ceil($total_results / 15);
			break;	
	}
	
	if ($pages >= 1 && $page <= $pages)
	{
		echo '<ul class="pagination">';
		for ($i = 1; $i <= $pages; $i++)
		{
			if($display_type == 'cluster')
				echo '<li><a href="?page='.$i.'&label='.$_GET['label'].'">'.$i.'</a></li> ';		
			else
				echo '<li><a href="?page='.$i.'">'.$i.'</a></li> ';
		}
		echo '</ul>';
	}	
}
function displaySuggestions()
{
	if (is_null($_SESSION['suggestions'])) return;
	
	//display suggested terms
	echo '<div id="hints">Related: ';
	foreach($_SESSION['suggestions'] as $term)
	{
		echo 	'<span class="hint">
				<a href="results.php?query='.$term.'&aggregate='.$_SESSION['aggregate'].'"> '.$term.'</a>
				</span>';	
	}
	echo '</div>';	
}
?>