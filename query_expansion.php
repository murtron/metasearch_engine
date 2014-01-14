<?php
include 'porter_stemmer.php';

function expandQuery($doc_list)
{
	//get tfidf matrix of unstemmed terms docs for query expansion
	$doc_freq = buildMatrix($doc_list, false);
	
	//sort the array
	arsort($doc_freq);
	
	//take the top 5 terms and return them as suggestions
	$i = 0;
	foreach($doc_freq as $term => $score)
	{
		if ($i++ >= 5) break;
		$_SESSION['suggestions'][] = $term;	
	}
}

//function to compute tfidf matrix		
function buildMatrix($doc_list, $stem)
{	

	$matrix = array();
	$doc_frq = array();
	
	//break each snippet into words and perform preprocessing
	foreach($doc_list as $docID => $doc) 
	{
		//ignore  documents without snippets
		if($doc[2] == 'No description available') continue;
		
		//strip punctuation
		$terms = preg_replace("/\p{P}/u", "", $doc[2]);
		
		//strip HTML tags
		$terms = strip_tags($terms);
		
		//break into array of substrings
		$terms = explode(' ', strtolower($terms));
		
		//remove stopwords
		$terms = removeStopwords($terms);
		
		//stem terms
		if($stem)
		{
			$terms = stemTerms($terms);
		}
		
		//put each term into the matrix and count frequency
		foreach($terms as $term) 
		{
			//exclude the query terms
			$query_terms = explode(' ', strtolower($_GET['query']));
			
			if($stem)
			{
				$query_terms = stemTerms($query_terms);
			}
			
			$stop = false;
			foreach($query_terms as $query_term)
			{	
				if($term == $query_term) $stop = true;
			}

			//exclude query terms and nulls
			if ( $stop || $term == '') continue;
			
			foreach($doc_list as $docID2 => $doc2)
			{
				if(!isset($matrix[$docID2][$term]))
					$matrix[$docID2][$term] = 0;
			}
			//record term frequency
			$matrix[$docID][$term]++;
		}
		
		foreach($matrix[$docID] as $term => $tf)
		{
			//record doc frequency
			if ($tf > 0)
			{
				if(!isset($doc_frq[$term]))
					$doc_frq[$term] = 1;
				else $doc_frq[$term]++;
			}
		}
	}
	
	//loop over each term and calculate tfidf
	foreach($matrix as $docID => $doc)
	{		
		foreach($doc as $term => $tf)
		{
			//skip empty dimensions
			if($tf == 0) continue;
			
			$tfidf = getTfidf($tf, $doc_frq[$term], count($matrix));
			$matrix[$docID][$term] = $tfidf;
		}
	}
	if(!$stem)
		return $doc_frq;
	else
		return $matrix = normaliseVectors($matrix);
}
//------------------Helper Functions-----------------------

//function to calculate tf-idf
function getTfidf($tf, $df, $doc_count)
{
	$term_frequency = 1 + log($tf);
	$idf = log($doc_count/$df);

	return $term_frequency * $idf;
}

//function to remove stopwords
function removeStopwords($terms)
{
	//list of stopwords to remove
	$stopwords = file('stopwords.txt', FILE_IGNORE_NEW_LINES);

	foreach($terms as $key => $term)
	{
		foreach($stopwords as $stopword)
		{
			if($term == rtrim($stopword))
			{
				unset($terms[$key]);	
			}	
		}	
	}
	return array_values($terms);
}

//function to stem terms
function stemTerms($terms)
{
	foreach($terms as $key => $term)
	{
		$terms[$key] = PorterStemmer::Stem($term);
	}
	return array_values($terms);
}

//function to normalise vector lengths
function normaliseVectors($matrix)
{
	//loop over each document weight
	foreach($matrix as $key => $doc)
	{
		$total = 0;
		
		foreach($doc as $weight)
		{
			//skip empty dimensions
			if ($weight == 0) continue;
			$total += $weight*$weight;
		}
		$total = sqrt($total);
		
		foreach($doc as $term => $weight)
		{
			if ($weight == 0) continue;
			$matrix[$key][$term] = $weight/$total;	
		}
	}
	return $matrix;
}
?>