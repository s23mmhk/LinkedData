<html>
	<head>
		<title>Films World</title>
		<link rel="stylesheet"type="text/css"href="style.css">
	</head>
	
	<body>
	
		<?php 
		
		// ARC2 static class inclusion 
		include_once('semsol/ARC2.php'); 
		
		// 
		function executeQuery($query){
			$dbpconfig = array(
				"remote_store_endpoint" => "http://dbpedia.org/sparql",
			);
		
			$store = ARC2::getRemoteStore($dbpconfig);
		
			if ($errs = $store->getErrors()) {
				echo "<h1>getRemoteSotre error<h1>" ;
			}
		
		
			$rows = $store->query($query, 'rows'); /* execute the query */
		
			if ($errs = $store->getErrors()) {
				echo "Query errors" ;
				print_r($errs);
			}
			return $rows;
		}
		
		
		function getGenre($i){
			if($i==0){
				return "romance";
			}
			if($i==1){
				return "comedy";
			}
			if($i==2){
				return "action";
			}
			if($i==3){
				return "drama";
			}
			if($i==4){
				return "thriller";
			}
			if($i==5){
				return "animation";
			}
			if($i==6){
				return "adventure";
			}
			if($i==7){
				return "fantasy";
			}
			if($i==8){
				return "crime";
			}
			return  "";
		}
		?>
		<header>
				<table class="logo">
					<tr>
						<td><img alt="" src="mymoviesicon.png" width="80" height="80"></td>
						<td><span style="font-size:70px">M</span><span style="font-size:68px">o</span><span style="font-size:66px">v</span><span style="font-size:64px">i</span><span style="font-size:62px">e</span><span style="font-size:60px">s</span></td>
					</tr>
				</table>
				
		</header>
		<nav class="search_block">
			<select style="margin-left: 75%;margin-top: 8px" form="myform" name="lang" id="lang" ><option value="0">Language</option><option value="1">English</option><option value="2">French</option><option value="3">Arabic</option></select>
			<?php 
			if(isset($_POST['lang'])){
				echo ' <script>
						var objSelect = document.getElementById("lang");
						objSelect.options['.$_POST['lang'].'].selected = true;
					</script> ';
			}
			
			?>
			
		</nav>
		<section class="main_section">
			<header>
				<form id="myform" action="home.php" method="post">
					<span style="font-size: 16">The Top Number of Movies : </span>
					<?php 
						if(isset($_POST['refresh']) || isset($_POST['next']) || isset($_POST['prev'])){
							echo '<input type="number" value="'.$_POST['k'].'" name="k" min="10" max="50">';
						}
						else {
							echo '<input type="number" value="10" name="k" min="10" max="50">';
						}
					?> 
				&nbsp; Genre :
				<select name="genre" id="genre">
					<option value="0">Romance</option>
					<option value="1" >Comedy</option>
					<option value="2" >Action</option>
					<option value="3" >Drama</option>
					<option value="4" >Thriller</option>
					<option value="5" >Animation</option>
					<option value="6" >Adventure</option>
					<option value="7" >Fantasy</option>
					<option value="8" >Crime</option>
				</select>
				&nbsp;&nbsp; <input type="submit" name="refresh" value="Display">
				</form>
				
			
			</header>
			
			<?php 
			$limit;
			$offset;
			$query;
			$lang;
			$filmgenre;
			if(isset($_POST['lang'])){
				if($_POST['lang'] == 0){
					$lang = "en";
				}
				if($_POST['lang'] == 1){
					$lang = "en";
				}
				if($_POST['lang'] == 2){
					$lang = "fr";
				}
				if($_POST['lang'] == 3){
					$lang = "ar";
				}
				
			}
			if(isset($_POST['refresh'])){
				
				$limit = $_POST['k'];
				$offset = 0;
				
				echo '<input type="hidden" name="lastoffset" value="0" form="myform" />';
				echo '<input type="hidden" name="lastgenre" value="'.$_POST['genre'].'" form="myform" />';
				
				echo ' <script>
						var objSelect = document.getElementById("genre");
						objSelect.options['.$_POST['genre'].'].selected = true;
						</script> ';
				$filmgenre = getGenre($_POST['genre']);
				
				$query = '
			    	PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
      				PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
					PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
					PREFIX dbc: <http://dbpedia.org/ontology/>
					PREFIX dbp: <http://dbpedia.org/property/>
					PREFIX dtc: <http://purl.org/dc/terms/>
					select distinct  ?name ?abstract ?runtime ?date ?budget ?cinematography ?link
					where {
					   ?film a dbc:Film.
					   ?film dbp:name ?name.
					   ?film dbp:released ?date.
					   ?film dbp:runtime ?runtime.
					   ?film dbc:abstract ?abstract.
					   ?film dtc:subject ?genre.
					   ?film dbc:cinematography ?cinematography.
			           ?film dbc:wikiPageExternalLink ?link.
					   ?film dbp:budget ?budget.
						FILTER(lang(?abstract)="'.$lang.'").
						FILTER ( datatype(?date) = xsd:date ).
						FILTER (!REGEX(str(?name),"Original Motion Picture Soundtrack")).
						FILTER (REGEX(str(?genre),"'. $filmgenre .'"))
					}
					ORDER BY DESC (?date)
					limit '. $limit . '
					offset '.$offset;
				
			}
			else if(isset($_POST['next'])){
				
				$offset = $_POST['k'] + $_POST['lastoffset'];
				echo '<input type="hidden" name="lastoffset" value="'. $offset .'"  form="myform" />';
				
				
				$limit = $_POST['k'];
				$genre = '' ;
				if($_POST['lastgenre'] != $_POST['genre']){
					echo ' <script>
						var objSelect = document.getElementById("genre");
						objSelect.options['.$_POST['lastgenre'].'].selected = true;
						</script> ';
					$genre = $_POST['lastgenre'];
				}
				else {
					echo ' <script>
						var objSelect = document.getElementById("genre");
						objSelect.options['.$_POST['genre'].'].selected = true;
						</script> ';
					$genre = $_POST['lastgenre'];
				}
				
			
				
				echo '<input type="hidden" name="lastgenre" value="'.$genre.'" form="myform" />';
				
				
				$filmgenre = getGenre($genre);
				$query = '
			    	PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
      				PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
					PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
					PREFIX dbc: <http://dbpedia.org/ontology/>
					PREFIX dbp: <http://dbpedia.org/property/>
					PREFIX dtc: <http://purl.org/dc/terms/>
					select distinct  ?name ?abstract ?runtime ?date ?budget ?cinematography ?link
					where {
					   ?film a dbc:Film.
					   ?film dbp:name ?name.
					   ?film dbp:released ?date.
					   ?film dbp:runtime ?runtime.
					   ?film dbc:abstract ?abstract.
					   ?film dtc:subject ?genre.
					   ?film dbc:cinematography ?cinematography.
			          ?film dbc:wikiPageExternalLink ?link.
					   ?film dbp:budget ?budget.
						FILTER(lang(?abstract)="'.$lang.'").
						FILTER ( datatype(?date) = xsd:date ).
						FILTER (!REGEX(str(?name),"Original Motion Picture Soundtrack")).
						FILTER (REGEX(str(?genre),"'. $filmgenre .'"))
					}
					ORDER BY DESC (?date)
					limit '. $limit . '
					offset '.$offset;
				
			}
			else if (isset($_POST['prev'])){
				$offset =  $_POST['lastoffset'] - $_POST['k'];
				if($offset <= 0){
					$offset = 0;
				} 
				echo '<input type="hidden" name="lastoffset" value="'. $offset .'"  form="myform" />';
				
				$limit = $_POST['k'];
				$genre = '' ;
				if($_POST['lastgenre'] != $_POST['genre']){
					echo ' <script>
						var objSelect = document.getElementById("genre");
						objSelect.options['.$_POST['lastgenre'].'].selected = true;
						</script> ';
					$genre = $_POST['lastgenre'];
				}
				else {
					echo ' <script>
						var objSelect = document.getElementById("genre");
						objSelect.options['.$_POST['genre'].'].selected = true;
						</script> ';
					$genre = $_POST['lastgenre'];
				}
				
				
				
				echo '<input type="hidden" name="lastgenre" value="'.$genre.'" form="myform" />';
				
				$filmgenre = getGenre($genre);
				
				$query = '
			    	PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
      				PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
					PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
					PREFIX dbc: <http://dbpedia.org/ontology/>
					PREFIX dbp: <http://dbpedia.org/property/>
					PREFIX dtc: <http://purl.org/dc/terms/>
					select distinct  ?name ?abstract ?runtime ?date ?budget ?cinematography ?link
					where {
					   ?film a dbc:Film.
					   ?film dbp:name ?name.
					   ?film dbp:released ?date.
					   ?film dbp:runtime ?runtime.
					   ?film dbc:abstract ?abstract.
					   ?film dtc:subject ?genre.
					   ?film dbc:cinematography ?cinematography.
			           ?film dbc:wikiPageExternalLink ?link.
					   ?film dbp:budget ?budget.
						FILTER(lang(?abstract)="'.$lang.'").
						FILTER ( datatype(?date) = xsd:date ).
						FILTER (!REGEX(str(?name),"Original Motion Picture Soundtrack")).
						FILTER (REGEX(str(?genre),"'. $filmgenre .'"))
					}
					ORDER BY DESC (?date)
					limit '. $limit . '
					offset '.$offset;
			}
			else {

				$limit = 10;
				$offset = 0;
				
				echo '<input type="hidden" name="lastoffset" value="0"  form="myform" />';
				echo '<input type="hidden" name="lastgenre" value="-1" form="myform" />';
				
				$query = '
			    PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
      			PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
				PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
				PREFIX dbc: <http://dbpedia.org/ontology/>
				PREFIX dbp: <http://dbpedia.org/property/>
				PREFIX dtc: <http://purl.org/dc/terms/>
				select distinct  ?name ?abstract ?runtime ?date ?budget ?cinematography ?link
				where {
				   ?film a dbc:Film.
				   ?film dbp:name ?name.
				   ?film dbp:released ?date.
				   ?film dbp:runtime ?runtime.
				   ?film dbc:abstract ?abstract.
				   ?film dbc:cinematography ?cinematography.
			       ?film dbc:wikiPageExternalLink ?link.
				   ?film dbp:budget ?budget.
					FILTER(lang(?abstract)="en").
					FILTER ( datatype(?date) = xsd:date ).
 					FILTER (?date > "2015-01-01"^^xsd:date).
					FILTER (!REGEX(str(?name),"Original Motion Picture Soundtrack"))
				}
				ORDER BY DESC (?date)
				limit '. $limit . '
				offset '.$offset;
				
			}
			
			
			//$offset += $limit;
			$rows = executeQuery($query);
			/* display the results in an HTML table */
			foreach($rows as $row ) { /* loop for each returned row */
				
				echo "
					<section class=\"films\">
						<h2>" .$row['name'] . "</h2>
								<p>" .$row['abstract'] . "</p>
								<span>Runtime: " . ($row['runtime']/60). " min</span><br>
								<span>Release Date: " .$row['date'] . "</span><br><br>
								<a style=\"color:yellow\" href=\"". $row['link'] ."\">". $row['link'] ."</a><br>
								<details><summary>Production Information</summary>
									Budget: " . $row['budget'] . "<br>
									Cinematography: <a style=\"color:gold\" href=\"". $row['cinematography'] ."\">". $row['cinematography'] ."</a><br>
								</details>
					</section>
					<br><br><div class=\"grad1\"></div><br><br>
						";
			}
			?>
			<center>
				<input type="submit" name="prev" value="<< Prev " form="myform">
				&nbsp;&nbsp;&nbsp;
				<input type="submit" name="next" value=" Next >>" form="myform">
			</center>
		</section>	
		<footer>
			
				Copyright &copy; 2016 . 
				Designed by Mohamad-Mahdi Kassem 
		</footer>
		
	</body>
</html>