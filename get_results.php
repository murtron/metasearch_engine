<?php
function getResults($query)
{	
	//account for Boolean NOT queries
	$query = str_replace('NOT+', '-', $query);
	
	//-------------------Google---------------------------
	
	//set index to start results at
	$start = 1;
	
	$url ="https://www.googleapis.com/customsearch/v1?key=AIzaSyAzEcu1Wpcvjq5pAG5wRLAj6FL9f7x_u_8&cx=003313570654257649885:ihrkhui8va4&start=$start&q=$query";
	//loop through each set of 10 results
	for($i=0;$i<1;$i++)
	{
		//initiate cURL
		$ch = curl_init($url);
		
		//set options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		// get the web page source into $data
		$data = curl_exec($ch);
		curl_close($ch);
		
		$js = json_decode($data);
		
		//if API limit is reached
		if (isset($js->error->message))
		{
			 $_SESSION['results']['Google'][] = array(NULL, NULL, '$js->error->message'); ;
			 break;	
		}
		//if no results are returned
		elseif(!isset($js->items))
		{ 
			$_SESSION['results']['Google'][] = array(NULL, NULL, 'No results returned...');
			//turn off query expansion module
			$_SESSION['suggestions'] = NULL;
			break;
		}
		else
		{
			//put each result in the Google array 
			foreach($js->items as $value) 
			{ 
				if(isset($value->snippet))
					$_SESSION['results']['Google'][] = array($value->link, $value->title, $value->snippet);
				else $_SESSION['results']['Google'][] = array($value->link, $value->title, 'No description available...');
			}
		}
		
		//reset url to get next 10 results
		$start += 10;
		$url ="https://www.googleapis.com/customsearch/v1?key=AIzaSyAzEcu1Wpcvjq5pAG5wRLAj6FL9f7x_u_8&cx=003313570654257649885:ihrkhui8va4&start=$start&q=$query";
		
	}
	
	//-----------------Bing--------------------
	
	$url= "https://api.datamarket.azure.com/Bing/SearchWeb/Web?\$format=json&Query=$query";     
	
	//set credentials to send in header
	$credentials = "username:VNkr2xsPpCfgWwRBlU6eKITEwz99rnAzQ53Qcanf3QE=";
	$headers = array(
			"Authorization: Basic " . base64_encode($credentials)
		);
	//loop through 2 sets of 50 results
	for ($i=0;$i<1;$i++)
	{
		$ch = curl_init($url);
			 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$data = curl_exec($ch);
		curl_close($ch);
		
		$js = json_decode($data);
			
		foreach($js->d->results as $value) 
		{ 
			//put each result in the Bing array 
			$_SESSION['results']['Bing'][] = array($value->Url, $value->Title, $value->Description);
		}
		
		//get next 50 results
		$url = $url.'&$skip=50';		
	}
		
	//----------------Blekko-----------------
	
	$query = str_replace('+OR+', '', $query);
	$url ="http://blekko.com/ws/?q=$query+/ps=50+/json&auth=f4c8acf3/";
	
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$data = curl_exec($ch);
	curl_close($ch);
	
	$js = json_decode($data);
	
	if(!isset($js->RESULT))
	{
		
	}
	else
	{
		foreach($js->RESULT as $value) 
		{ 
			//put each result in the Blekko array 
			if (isset($value->snippet))
			{
				$_SESSION['results']['Blekko'][] = array($value->url, $value->url_title, $value->snippet);
			}
			else $_SESSION['results']['Blekko'][] = array($value->url, $value->url_title,  'No description available ...');
		}
	}
	
	//array to hold all results
	return $doc_list = array_merge($_SESSION['results']['Google'], $_SESSION['results']['Bing'], $_SESSION['results']['Blekko']);
}
?>