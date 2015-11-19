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

player.json & player.pdf

- has the 'player' table schema and sample inputs
