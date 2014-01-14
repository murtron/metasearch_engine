<?php 
//session variables
session_start();
ini_set('xdebug.max_nesting_level', 500);

if (!isset($_SESSION['ranked_list']))
{
	$_SESSION['ranked_list'] = NULL;
}
if (!isset($_SESSION['cluster_list']))
{
	$_SESSION['cluster_list'] = NULL;
}
if (!isset($_SESSION['suggestions']))
{
	$_SESSION['suggestions'] = NULL;
}
if (isset($_GET['aggregate']))
{
	$_SESSION['aggregate'] = $_GET['aggregate'];
	$_SESSION['cluster'] = NULL;
}
if(isset($_GET['cluster']))
{
	$_SESSION['cluster'] = $_GET['cluster'];	
}


require 'get_results.php';
require 'aggregation.php';
require 'display.php';
require 'clustering.php';
require 'query_expansion.php';

//if there is a query get results
if (isset($_GET['query']))
{
	$_SESSION['query'] = trim($_GET['query']);
	
	//encode query
	$query = urlencode("'{$_SESSION['query']}'");
	
	//array to hold results from each search engine
	$_SESSION['results'] = array(	'Google' => array(),
									'Bing' => array(),
									'Blekko' => array()
								);
								
	//retrieve search results from each api
	$doc_list = getResults($query);	
	
	//if clustering is not enabled and results have been returned expand query
	if(is_null($_SESSION['cluster']) && $doc_list[0][2] != 'No results returned...')
	{
		//array to hold related terms
		$_SESSION['suggestions'] = NULL;
		
		expandQuery($doc_list);
	}		
}

//-------------------Pagination---------------------
		
if (isset($_GET['page']))
{
	$page = (int)$_GET['page'];
}
else
{
	$page = 1;	
}

//--------------------Display module-----------------

require 'page_template.html';
	
	if (isset($_SESSION['cluster']))
	{	
		$display_type = 'cluster';
		
		if (isset($_GET['aggregate']))
		{
			aggregateResults($doc_list);	
			
			//cluster results
			$_SESSION['cluster_list'] = getClusters($_SESSION['ranked_list']);
		}
		
		displayResults($display_type, $page, NULL);
		
	}
	//aggregated results display
	elseif ($_SESSION['aggregate'] == 'true')
	{	
		$display_type = 'aggregate';
		
		if (isset($_GET['aggregate']))
		{
			aggregateResults($doc_list);	
		}
	
		displayResults($display_type, $page);	
	}
	elseif($_SESSION['aggregate'] == 'false')
	{
		$display_type = 'nonagg';
		
		displayResults($display_type, $page);
	}
?>			
</div>
</div>      
</body> 
</html>
