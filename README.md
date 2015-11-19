# SpinResults
Spin Results End point

php/UserInput.php

- Run this file. This has the input (PlayerID, SaltValue, CoinsBet and CoinsWon)
- This sends a CURL request to SpinResults.php end point server (using localhost server - change as required)
- Gets a JSON response from the end point and prints it out

php/SpinResults.php

- End point that accepts a POST request of (PlayerID, SaltValue, CoinsBet and CoinsWon)
- Validates player data (hash, coinsBet and coinsWon ) using PlayerID obtained from 'player' table in local MySQL DB (testdb)
- If valid, updates the 'player' table with newly calculated information
- Creates and sends a JSON response 
- This file has been tested for invalid inputs for all fields
  - PlayerID - outputs PlayerID or credentials invalid
  - Salt Value - outputs PlayerID or credentials invalid
  - CoinsBet or CoinsWon - outputs Credit error

- With valid inputs 
  - outputs the JSON response with {PlayerID, Name, Credits, LifeTimeSpins, LifeTimeAverageReturns} 

Sample Output : 
JSON Response : {"PlayerID":"213145","Name":"Player 1","Credits":7334,"LifeTimeSpins":329,"LifeTimeAverageReturns":22.29179331307}

player.json & player.pdf

- has the 'player' table schema and sample inputs
