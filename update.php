<html>
<head>
	<!-- Styling for the ouput from php -->
	<style> 
		table {border: 1px solid white;
			border-collapse: collapse;
			margin-left: auto;
			margin-right: auto;
			background-color: rgba(183, 140, 209, 0.4);
			color: white; 
		}
		
		tr, td {border: 1px solid white;
			border-collapse: collapse;}
		
		body {background-image: url("soccer_background.png");
			background-size: cover;
			background-position: relative;
		}
		
		p {text-align: center;
			color: red;
			font-weight: bold;
			font-size: 40px;}
		
		.title {text-align: center;
			color: white;
			font-weight: bold;
			font-size: 40px;
			margin-top: 100px;
		} 

		.header {font-weight: bold;}

		a {text-align: center;
			color: white;
			font-size: 25px;
		}
	</style>	
</head>
<body>
</html>
</body>

<?php
	/* Connecting to database*/
    $dbhost = 'dbase.cs.jhu.edu:3306';
    $dbuser = '19_amalik36';
    $dbpass = 'abcdefg';
    $dbname = 'cs415_fall_19_amalik36';
    $conn = mysql_connect($dbhost, $dbuser, $dbpass);

    if (!$conn) {
        die('Error connecting to mySQL');
    } else {
        mysql_select_db($dbname, $conn);
    }


    echo "<a href='index.html' title='#'>Go Back</a>";

    /* Find player information or statistics from a user entered name*/
    if (isset($_POST['button1'])) {
        $player_name = $_POST['player'];
	$request = $_POST['req'];
        $findplayer = "SELECT * FROM Athletes WHERE full_name = '$player_name'";
        $playerexists = mysql_query($findplayer);				//check that the player name entered actually exists by placing a query looking for all the data from Players relation with the given name
        $row = mysql_fetch_array($playerexists);
        if ($row == FALSE) {							//If no data could be found, invalid name
            echo "<p>Invalid Player Name</p>";
	} else {
		if ($request == "Player Info") {				//Query to get columns of data on the player's information and the country he is from
			$findInfo = "SELECT full_name, age, nationality, club, position, 
				region, population, area, gdp, literacy, birthrate 
				FROM Athletes, Countries 
				WHERE full_name = '$player_name' AND Athletes.nationality = Countries.country";
			$getInfo = mysql_query($findInfo);			//Create a table with the attribute names as column headers and a title for the output table
			echo "<p class='title'>Player Information</p>";
                        echo "<table>\n";
                        echo "<tr class='header'>
                                <td>Name</td>
                                <td>Age</td>
                                <td>Club</td>
				<td>Position</td>
				<td>Nationality</td>
				<td>Region</td>
                                <td>Population</td>
                                <td>Country Area (sq. miles)</td>
                                <td>Country GDP</td>
				<td>Country Literacy Rate</td>
				<td>Country Birthrate</td>			
                                </tr>\n";
			//For each row of data, fetch the information and place it in the results table
                        while ($infoRow = mysql_fetch_array($getInfo)) {
                                   printf("<tr>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
			    <td>%s</td>
			    <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
			    <td>%s</td>
			    <td>%s</td>
                            </tr>\n",
                                $infoRow["full_name"],
                                $infoRow["age"],
                                $infoRow["club"],
				$infoRow["position"],
                                $infoRow["nationality"],
				$infoRow["region"],
                                $infoRow["population"],
                                $infoRow["area"],
                                $infoRow["gdp"],
                                $infoRow["literacy"],
				$infoRow["birthrate"]);

			}
			echo "</table>\n";
		} else if ($request == "Player Statistics") {
			$findInfo = "SELECT full_name, minutes_played, goals_overall, assists_overall, yellow_cards, red_cards FROM Athletes WHERE full_name = '$player_name'";
                        $getInfo = mysql_query($findInfo);
			echo "<p class='title'>Player Statistics</p>";
			echo "<table>\n";
                        echo "<tr class='header'>
                                <td>Name</td>
                                <td>Minutes Played</td>
                                <td>Goals</td>
                                <td>Assists</td>
				<td>Yellow Cards</td>
				<td>Red Cards</td>
                                </tr>\n";
                                
                        while ($infoRow = mysql_fetch_array($getInfo)) {
                                   printf("<tr>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
			    <td>%s</td>
			    <td>%s</td>
                            </tr>\n", 
                                $infoRow["full_name"], 
                                $infoRow["minutes_played"],  
                                $infoRow["goals_overall"], 
				$infoRow["assists_overall"],
				$infoRow["yellow_cards"],
				$infoRow["red_cards"]);
                        }
                        echo "</table>\n";

		} else {
			echo "<p> Invalid Request.";
		}	
	}
    }

    /* Find information on a match given the home and away team*/
    if (isset($_POST['button2'])) {
    	    $home_team = $_POST['home'];
            $away_team = $_POST['away'];
   
            $findGame = "SELECT * FROM Matches WHERE home_team = '$home_team' AND away_team = '$away_team'";
            $gameExists = mysql_query($findGame);					//Check that there exists a match involving the two teams
            $row = mysql_fetch_array($gameExists);
            if ($row == FALSE) {
                echo "<p>Invalid Teams</p>"; 
	    } else {
                $findInfo = "SELECT * FROM Matches WHERE home_team = '$home_team' AND away_team = '$away_team'";
		$getInfo = mysql_query($findInfo);		//query to get all information for this particular match
		
								//Create table with attribute names as columns and a title for table		
		echo "<p class='title'>Match Information</p>";
		echo "<table>\n";
                echo "<tr class='header'>
                        <td>Game Date</td>
                        <td>Location</td>
                        <td>Attendance</td>
                        <td>Home Team</td>
                        <td>Away Team</td>
                        <td>Winner</td>
                        </tr>\n";
		
		//Loop through the match data, and calculate the winner by finding which team has a higher goal count
                while ($infoRow = mysql_fetch_array($getInfo)) {
			$winner = $infoRow["home_team"];
			if ($infoRow["away_team_goal_count"] > $infoRow["home_team_goal_count"]) {
				$winner = $infoRow["away_team"];
			}
			printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
			<td>%s</td>
			<td>%s</td>
                        </tr>\n",
                                $infoRow["game_date"],
                                $infoRow["stadium_name"],
                                $infoRow["attendance"],
                                $infoRow["home_team"],
				$infoRow["away_team"],
				$winner);
                        }					//Output the match information to the table of results
		echo "</table>\n";
	    }
    }
    /* Find information on a team and a roster for the team*/
    if (isset($_POST['button3'])) {
	    $team_name = $_POST['team'];
	    $findTeam = "SELECT * FROM Teams WHERE team_name = '$team_name'";
            $teamExists = mysql_query($findTeam);					//checking that team entered actually exists
            $row = mysql_fetch_array($teamExists);
            if ($row == FALSE) {
                echo "<p>Invalid Team</p>";
	    } else {
                $findInfo = "SELECT * FROM Teams WHERE team_name = '$team_name'";
                $getInfo = mysql_query($findInfo);					//If team name not invalid, create a table for results
		echo "<p class='title'>Club Information</p>";
                echo "<table>\n";
                echo "<tr class='header'>
                        <td>Name</td>
                        <td>Matches</td>
                        <td>Wins</td>
			<td>Losses</td>
			<td>Draws</td>
                        <td>Points Per Game</td>
			<td>Total Goals</td>
                        </tr>\n";
											//From the loop, output the information about that team (Should be 1 row)
                while ($infoRow = mysql_fetch_array($getInfo)) {
                        printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
			<td>%s</td>
			<td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
			<td>%s</td>
                        </tr>\n",
                                $infoRow["team_name"],
                                $infoRow["matches_played"],
                                $infoRow["wins"],
				$infoRow["draws"],
				$infoRow["losses"],
                                $infoRow["points_per_game"],
				$infoRow["goals_scored"]);
		}        
		echo "</table>\n";
	    
		//Find all the players in the team by querying by club name
	    	$findPlayers = "SELECT full_name, position FROM Athletes WHERE club = '$team_name' GROUP BY full_name ASC";
		$getPlayers = mysql_query($findPlayers);
		echo "<p class='title'>Meet The Players</p>";
	    	echo "<table border=1>\n";
	    	echo "<tr class='header'>
		   	<td>Player Name</td>
		   	<td>Position</td>
			</tr>\n";
										//Create table of results using the attributes gathererd from query, and loop through all players information and output results
		while ($getRow = mysql_fetch_array($getPlayers)) {
			printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        </tr>\n",
                        $getRow["full_name"],
			$getRow["position"]);
		}
	    	echo "</table>\n";
	    }
	    
    }
    /* Find the information on a stadium or list of all stadiums*/
    if (isset($_POST['button4'])) {
	    $stadium_name = $_POST['sname'];
	    if ($stadium_name == "select all") {
	    	$findStadiums = "SELECT * FROM Stadiums";
		$getInfo = mysql_query($findStadiums);		//check stadium exists, if select all chosen then output information for all stadiums in league
		echo "<p class='title'>All Stadiums</p>";
		echo "<table>\n";
                echo "<tr class='header'>
                        <td>Team Name</td>
                        <td>Stadium Name</td>
                        <td>Latitude Degrees</td>
                        <td>Latitude Minutes</td>
                        <td>Latitude Seconds</td>
			<td>Longitude Degrees</td>
			<td>Longitude Minutes</td>
                        <td>Longitude Seconds</td>
                        </tr>\n";
								//Create table of results and put in each stadiums information from the loop
                while ($infoRow = mysql_fetch_array($getInfo)) {
			printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
			<td>%s</td>
			<td>%s</td>
                        <td>%s</td>
                        </tr>\n",
                                $infoRow["team_name"],
                                $infoRow["stadium_name"],
                                $infoRow["latd"],
                                $infoRow["latm"],
				$infoRow["lats"],
				$infoRow["lond"],
                                $infoRow["lonm"],
                                $infoRow["lons"]);
                        }
                        echo "</table>\n"; 
	    } else {											//if specific stadium chosen, query to find the information for only that one
		$club_name = "SELECT team_name FROM Stadiums WHERE stadium_name = '$stadium_name'";    
		$findInfo = "SELECT * FROM Stadiums WHERE stadium_name = '$stadium_name'";
                $getInfo = mysql_query($findInfo);

		echo "<p class='title'>Stadium Information</p>";
                echo "<table>\n";
                echo "<tr class='header'>
                        <td>Team Name</td>
                        <td>Stadium Name</td>
                        <td>Latitude Degrees</td>
                        <td>Latitude Minutes</td>
                        <td>Latitude Seconds</td>
			<td>Longitude Degrees</td>
			<td>Longitude Minutes</td>
                        <td>Longitude Seconds</td>
                        </tr>\n";
											//Create table of results, header and input 1 row for stadium
                while ($infoRow = mysql_fetch_array($getInfo)) {
			printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
			<td>%s</td>
			<td>%s</td>
                        <td>%s</td>
                        </tr>\n",
                                $infoRow["team_name"],
                                $infoRow["stadium_name"],
                                $infoRow["latd"],
                                $infoRow["latm"],
				$infoRow["lats"],
				$infoRow["lond"],
                                $infoRow["lonm"],
                                $infoRow["lons"]);
                        }
                        echo "</table>\n";
	    						//Query to find all the players that play at that stadium and order alphabetically
			$findPlayers = "SELECT full_name, position, age, nationality FROM Athletes, (SELECT team_name FROM Stadiums WHERE stadium_name='$stadium_name') AS A WHERE A.team_name = Athletes.club GROUP BY full_name ASC";
                $getPlayers = mysql_query($findPlayers);
		
		echo "<p class='title'>Player Information</p>";
                echo "<table>\n";
                echo "<tr class='header'>
                        <td>Player Name</td>				
                        <td>Position</td>
			<td>Age</td>
			<td>Nationality</td>
                        </tr>\n";
									//Create table of results and put in each players information from loop
                while ($getRow = mysql_fetch_array($getPlayers)) {
                        printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
			<td>%s</td>
			<td>%s</td>
                        </tr>\n",
                         $getRow["full_name"],
			 $getRow["position"],
			 $getRow["age"],
                         $getRow["nationality"]);
                }
            echo "</table>\n";
	    
	    }
	    
    }

    /* Find information of country and information of all players of that country in the league*/
    if (isset($_POST['button5'])) {
        $country_name = $_POST['country_name'];
        $findCountry = "SELECT * FROM Countries WHERE country = '$country_name'";
        $countryExists = mysql_query($findCountry);				//Checking for a valid country entered
        $row = mysql_fetch_array($countryExists);
        if ($row == FALSE) {
            echo "<p>Invalid Country</p>";
        }
        else {
	    $getInfo = mysql_query($findCountry);
										//Create table for results and header
	    echo "<p class='title'>Country Information</p>";
            echo "<table border=1>\n";
            echo "<tr class='header'>
                    <td>Country</td>
                    <td>Region</td>
                    <td>Population</td>
                    <td>Area (sq. miles)</td>
                    <td>GDP (per capita)</td>
                    <td>Literacy Rate</td>
		    <td>Birthrate</td>
		    <td>Deathrate</td>
                    </tr>\n";
										//Loop to enter the country and its information, should just be 1 in this case
                while ($infoRow = mysql_fetch_array($getInfo)) {
                    printf("<tr>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
		    <td>%s</td>
		    <td>%s</td>
                    </tr>\n",
                            $infoRow["country"],
                            $infoRow["region"],
                            $infoRow["population"],
                            $infoRow["area"],
                            $infoRow["gdp"],
                            $infoRow["literacy"],
			    $infoRow["birthrate"],
		    	    $infoRow["deathrate"]);
                }
	    echo "</table>\n";
	
		
		//query to find information on the players in the premier league from this entered country*/
            $findPlayers = "SELECT full_name, position, club FROM Athletes WHERE nationality = '$country_name' GROUP BY full_name ASC";
            $getPlayers = mysql_query($findPlayers);

	    echo "<p class='title'>Players</p>";
	    echo "<table>\n";

                echo "<tr class='header'>
                        <td>Player Name</td>
			<td>Position</td>
			<td>Club</td>		
                        </tr>\n";
									//Create table of results, header and loop to put in each players information
                while ($getRow = mysql_fetch_array($getPlayers)) {
                        printf("<tr>
                        <td>%s</td>
			<td>%s</td>
			<td>%s</td>
                        </tr>\n",
                         $getRow["full_name"],
			 $getRow["position"],
		 	 $getRow["club"]);
                }
	    echo "</table>\n";
	 
	}
    }
	
    // Creating a hypothetical ranking of players by position, nothing to compare goalkeeper so not ranked
    if (isset($_POST['button6'])) {
	$player_type = $_POST['playertype'];
	if ($player_type != "Forward" && $player_type != "Midfielder" && $player_type != "Defender" && $player_type != "Goalkeeper") {
		echo "Invalid Player Position";		//Check that position entered is valid
	}
	else {
		if ($player_type == "Midfielder" || $player_type == "Forward") {
		$findInfo = "SELECT full_name, club, minutes_played, goals_overall, assists_overall, yellow_cards, red_cards 
			FROM Athletes WHERE position = '$player_type' ORDER BY goals_overall DESC, assists_overall DESC";
                $getInfo = mysql_query($findInfo);			//query to get information of midfielders/forwards, first sorting by goals and then by assists
		echo "<p class='title'>Player Information</p>";
			echo "<table>\n";
                        echo "<tr class='header'>
				<td>Name</td>
				<td>Team</td>
                                <td>Minutes Played</td>
                                <td>Goals</td>
                                <td>Assists</td>
                                <td>Yellow Cards</td>
                                <td>Red Cards</td>
                                </tr>\n";
										//Create table of results with attributes as columns and title, then use while loop to fill in each player and their information in rank order
                        while ($infoRow = mysql_fetch_array($getInfo)) {
                                   printf("<tr>
			    <td>%s</td>
			    <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            </tr>\n",
				$infoRow["full_name"],
				$infoRow["club"],
                                $infoRow["minutes_played"],
                                $infoRow["goals_overall"],
                                $infoRow["assists_overall"],
                                $infoRow["yellow_cards"],
                                $infoRow["red_cards"]);
			}
		echo "</table>\n";
	  	}
	 
		else if ($player_type == "Defender") {
                	$findInfo = "SELECT full_name, club, minutes_played, goals_overall, assists_overall, yellow_cards, red_cards 
				FROM Athletes WHERE position = '$player_type' ORDER BY red_cards ASC, yellow_cards ASC";
			$getInfo = mysql_query($findInfo);				//Same as above, but sort ascending by red cards and then yellow cards
			echo "<p class='title'>Player Information</p>";
                        echo "<table>\n";
                        echo "<tr class='header'>
				<td>Name</td>
                                <td>Team</td>
                                <td>Minutes Played</td>
                                <td>Goals</td>
                                <td>Assists</td>
                                <td>Yellow Cards</td>
                                <td>Red Cards</td>
                                </tr>\n";

                        while ($infoRow = mysql_fetch_array($getInfo)) {
                                   printf("<tr>
				    <td>%s</td>
					<td>%s</td>
					<td>%s</td>
                            		<td>%s</td>
                            	<td>%s</td>
                            	<td>%s</td>
                            	<td>%s</td>
				</tr>\n",
				$infoRow["full_name"],
                                $infoRow["club"],
                                $infoRow["minutes_played"],
                                $infoRow["goals_overall"],
                                $infoRow["assists_overall"],
                                $infoRow["yellow_cards"],
                                $infoRow["red_cards"]);
			}
                echo "</table>\n";
          }
		else if ($player_type == "Goalkeeper") {
                $findInfo = "SELECT full_name, club, minutes_played, goals_overall, assists_overall, yellow_cards, red_cards FROM Athletes WHERE position = '$player_type' ORDER BY red_cards ASC, yellow_cards ASC";
                        $getInfo = mysql_query($findInfo);			//Same functionality as that for defender
	       		echo "<p class='title'>Player Information</p>";
			echo "<table>\n";
                        echo "<tr class='header'>
                                <td>Name</td>
                                <td>Team</td>
                                <td>Minutes Played</td>
                                <td>Goals</td>
                                <td>Assists</td>
                                <td>Yellow Cards</td>
                                <td>Red Cards</td>
                                </tr>\n";

                        while ($infoRow = mysql_fetch_array($getInfo)) {
                                   printf("<tr>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            </tr>\n",
                                $infoRow["full_name"],
                                $infoRow["club"],
                                $infoRow["minutes_played"],
                                $infoRow["goals_overall"],
                                $infoRow["assists_overall"],
                                $infoRow["yellow_cards"],
                                $infoRow["red_cards"]);
                        }
                echo "</table>\n";
	
		}
	}
    }
 

    /* Comparing two players entered by the user*/
    if (isset($_POST['button7'])) {
        $player_name1 = $_POST['player1'];
        $player_name2 = $_POST['player2'];

        $findplayer1 = "SELECT * FROM Athletes WHERE full_name = '$player_name1'";
        $playerexists1 = mysql_query($findplayer1);						//Making sure both players exist by searching for their names
	$row1 = mysql_fetch_array($playerexists1);
	$findplayer2 = "SELECT * FROM Athletes WHERE full_name = '$player_name2'";
        $playerexists2 = mysql_query($findplayer2);
        $row2 = mysql_fetch_array($playerexists2);
        if ($row1 == FALSE || $row2 == FALSE ) {
            echo "<p>Invalid Player Name</p>";							//Invalid if either player doesn't exist
	} else {
		$findInfo = "SELECT full_name, minutes_played, goals_overall, assists_overall, yellow_cards, red_cards FROM Athletes WHERE full_name = '$player_name1' OR full_name = '$player_name2'";
                        $getInfo = mysql_query($findInfo);				//Query to get distinct information on the two player names
			echo "<p class='title'>How they stacked up</p>";
			echo "<table>\n";
                        echo "<tr class='header'>
                                <td>Name</td>
                                <td>Minutes Played</td>					
                                <td>Goals</td>
                                <td>Assists</td>
                                <td>Yellow Cards</td>
                                <td>Red Cards</td>
                                </tr>\n";
											//Create table of results and title, fill each players information in from the loop
                        while ($infoRow = mysql_fetch_array($getInfo)) {
                                   printf("<tr>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            </tr>\n",
                                $infoRow["full_name"],
                                $infoRow["minutes_played"],
                                $infoRow["goals_overall"],
                                $infoRow["assists_overall"],
                                $infoRow["yellow_cards"],
                                $infoRow["red_cards"]);
                        }
                        echo "</table>\n";

	}
    } 
	
    /** Find the breakdown of players and their countries for a given team*/
    if (isset($_POST['button8'])) {
            $team_name = $_POST["team_name"];
            $findTeam = "SELECT * FROM Teams WHERE team_name = '$team_name'";
            $teamExists = mysql_query($findTeam);			//Checking that team exists from executing this query
            $row = mysql_fetch_array($teamExists);
            if ($row == FALSE) {
                echo "<p>Invalid Team</p>";
            } else {
                $findInfo = "SELECT nationality, COUNT(B.A) FROM (SELECT nationality, full_name AS A FROM Athletes WHERE club = '$team_name') AS B GROUP BY nationality";
                $getInfo = mysql_query($findInfo);			//query gets the nationalities in the team and does a count on the players' names to get a count
                echo "<p class='title'>Club Nationalities</p>";
                echo "<table>\n";
                echo "<tr class='header'>
                     	<td>Nationality</td>
                        <td>Number of Players In League</td>
                        </tr>\n";
									//Create table with appropriate attributes and title, then while loop fills each nationalities information
		while ($infoRow = mysql_fetch_array($getInfo)) {
                        printf("<tr>
                        <td>%s</td>
			<td>%s</td>
			</tr>\n",
                                $infoRow["nationality"],
                                $infoRow["COUNT(B.A)"]);
                }
                echo "</table>\n";

            }

    }

    /* Finding the number of players with more goals than user input*/
    if (isset($_POST['button9'])) {
            $goals_name = $_POST["goals"];
            $findPlayers = "SELECT full_name, club, goals_overall FROM Athletes WHERE goals_overall  > '$goals_name' ORDER BY goals_overall DESC";
            $playersExists = mysql_query($findPlayers);			//Check that valid user input for goals
            $row = mysql_fetch_array($playersExists);
            if ($row == FALSE) {
                echo "<p>Invalid Goals Number</p>";
            } else {
		$findInfo = "SELECT full_name, club, goals_overall FROM Athletes WHERE goals_overall  > '$goals_name' ORDER BY goals_overall DESC";    
		$getInfo = mysql_query($findInfo);			//query gets players with goals and sorts by rank (most goals on top)
                echo "<p class='title'>Top Scorers</p>";
                echo "<table>\n";
                echo "<tr class='header'>			
                        <td>Name</td>
			<td>Club</td>
			<td>Goals</td>
                        </tr>\n";
									//Create table with attribute names and title, then while loop fills in each players information
                while ($infoRow = mysql_fetch_array($getInfo)) {
                        printf("<tr>
                        <td>%s</td>
			<td>%s</td>
			<td>%s</td>
                        </tr>\n",
                                $infoRow["full_name"],
				$infoRow["club"],
				$infoRow["goals_overall"]);
                }
                echo "</table>\n";

            }

    }

    /* Finding the list of players with more assists than user input*/
    if (isset($_POST['button10'])) {
            $assists_name = $_POST["assists"];
            $findPlayers = "SELECT full_name, club, assists_overall FROM Athletes WHERE assists_overall  > '$assists_name' ORDER BY assists_overall DESC";
            $playersExists = mysql_query($findPlayers);		//Check that there exists players with more assists than this
            $row = mysql_fetch_array($playersExists);
            if ($row == FALSE) {
                echo "<p>Invalid Assists Number</p>";
            } else {
                $findInfo = "SELECT full_name, club, assists_overall FROM Athletes WHERE assists_overall  > '$assists_name' ORDER BY assists_overall DESC";
                $getInfo = mysql_query($findInfo);			//query finds all the players with assists and ordered by rank
                echo "<p class='title'>Top Assisters</p>";
                echo "<table>\n";
                echo "<tr class='header'>
                        <td>Name</td>					
                        <td>Club</td>
                        <td>Assists</td>
                        </tr>\n";
									//Create table for results and header, then while loop to put in player information
                while ($infoRow = mysql_fetch_array($getInfo)) {
                        printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        </tr>\n",
                                $infoRow["full_name"],
                                $infoRow["club"],
                                $infoRow["assists_overall"]);
                }
                echo "</table>\n";

            }

    }

    /* Function to see how certain country factors affect the number of players in the league*/
    if (isset($_POST['button11'])) {
	    $factor_name = $_POST["factor"];
	    if ($factor_name != "Literacy Rate" && $factor_name != "Birthrate" && $factor_name != "Deathrate") {
		    echo "<p>Invalid Factor</p>";		//invalid type if not one of these 3
	    } 

	    else if ($factor_name == "Literacy Rate") {
                $findInfo = "SELECT nationality, literacy, COUNT(A.B) AS count FROM (SELECT nationality, full_name AS B FROM Athletes) AS A, Countries WHERE Countries.country = A.nationality GROUP BY nationality ORDER BY literacy DESC";
                $getInfo = mysql_query($findInfo);			//query to find the number of players and literacy rate of each country of players
                echo "<p class='title'>Literacy Rate Effect</p>";
                echo "<table>\n";
                echo "<tr class='header'>
                        <td>Country</td>
                        <td>Literacy Rate(%)</td>
                        <td>Count</td>
                        </tr>\n";					//Create table and header for results from query

                while ($infoRow = mysql_fetch_array($getInfo)) {	//Fill result table with information from array 
                        printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        </tr>\n",
                                $infoRow["nationality"],
                                $infoRow["literacy"],
                                $infoRow["count"]);
                }
                echo "</table>\n";

	    } else if ($factor_name == "Birthrate") {			//Same procedure for birthrate
		 $findInfo = "SELECT nationality, birthrate, COUNT(A.B) AS count FROM (SELECT nationality, full_name AS B FROM Athletes) AS A, Countries WHERE Countries.country = A.nationality GROUP BY nationality ORDER BY birthrate DESC";
                $getInfo = mysql_query($findInfo);
                echo "<p class='title'>Birthrate Effect</p>";
                echo "<table>\n";
                echo "<tr class='header'>
                        <td>Country</td>
                        <td>Birthrate(%)</td>
                        <td>Count</td>
                        </tr>\n";

                while ($infoRow = mysql_fetch_array($getInfo)) {
                        printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        </tr>\n",
                                $infoRow["nationality"],
                                $infoRow["birthrate"],
                                $infoRow["count"]);
                }
                echo "</table>\n";

	    } else {							//Same procedure for deathrate
		 $findInfo = "SELECT nationality, deathrate, COUNT(A.B) AS count FROM (SELECT nationality, full_name AS B FROM Athletes) AS A, Countries WHERE Countries.country = A.nationality GROUP BY nationality ORDER BY deathrate DESC";
                $getInfo = mysql_query($findInfo);
                echo "<p class='title'>Deathrate Effect</p>";
                echo "<table>\n";
                echo "<tr class='header'>
                        <td>Country</td>
                        <td>Deathrate(%)</td>
                        <td>Count</td>
                        </tr>\n";

                while ($infoRow = mysql_fetch_array($getInfo)) {
                        printf("<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        </tr>\n",
                                $infoRow["nationality"],
                                $infoRow["deathrate"],
                                $infoRow["count"]);
                }
                echo "</table>\n";

	    }

    }

    /* This function is for finding the top player from a country */
    if (isset($_POST['button12'])) {
            $country_name = $_POST["cname"];		//get value from html file
            $findCountry = "SELECT * FROM Countries WHERE country = '$country_name'";
            $countryExists = mysql_query($findCountry);

	    $findPlayer = "SELECT * FROM Athletes WHERE nationality = '$country_name'";
	    $playersExists = mysql_query($findPlayer);		//runs query
	    $playerRow = mysql_fetch_array($playersExists);	//result of query
	    
	    $row = mysql_fetch_array($countryExists);
            if ($row == FALSE) {				//if country name does not exist in database, invalid
                echo "<p>Invalid Country</p>";
	    } else if ($playerRow == FALSE) {			//if no player from given country
	    	echo "<p class='title'>No Players From This Country</p>";
	    } else {
		    $findInfo = "SELECT nationality, full_name, club, goals_overall, assists_overall FROM Athletes WHERE nationality = '$country_name' 
			    ORDER BY goals_overall DESC, assists_overall DESC LIMIT 1";		//query to find the player from the given country with the highest goals and assists combined
                	$getInfo = mysql_query($findInfo);

		

			echo "<p class='title'>Top Player</p>";		//Create a title for results and table format
                	echo "<table>\n";
                	echo "<tr class='header'>
                        <td>Nationality</td>
                        <td>Name</td>
			<td>Club</td>
			<td>Goals</td>
                        <td>Assists</td>
			</tr>\n";
		

               		 while ($infoRow = mysql_fetch_array($getInfo)) {	//For all players, list the attributes below from the array of players	
                        printf("<tr>					
                        <td>%s</td>
                        <td>%s</td>
			<td>%s</td>
			<td>%s</td>
                        <td>%s</td>
                        </tr>\n",
                                $infoRow["nationality"],
				$infoRow["full_name"],
				$infoRow["club"],
                                $infoRow["goals_overall"],
                                $infoRow["assists_overall"]);
                	}
			echo "</table>\n";
		}


    }

?>

