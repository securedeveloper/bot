<?php
	error_reporting(0);
	$input = @$_REQUEST["text"];
	$no_commands = [
	"I'm afraid I don't understand. I'm sorry!",
	"Salutations!",
	"<br><b>Please type 'help' for Help!</b>",
	];

	function solveMath($cmd){
		$res = null;
		if(preg_match('/(\d+)(?:\s*)([\+\-\*\/])(?:\s*)(\d+)/', $cmd, $matches) !== FALSE){
			$operator = $matches[2];
			switch($operator){
				case '+':
					$res = $matches[1] + $matches[3];
					break;
				case '-':
					$res = $matches[1] - $matches[3];
					break;
				case '*':
					$res = $matches[1] * $matches[3];
					break;
				case '/':
					$res = $matches[1] / $matches[3];
					break;
			}
			return $res;
		}
	}

	function help(){
		return "<h2>YourBot HelpMenu</h2>
		<p>
		You can communicate with Bot with these command.
		<ul>
			<li>Introduction</li>
			<li>Age</li>
			<li>Time</li>
			<li>/currency How much in USD is 1000 EUR?</li>
			<li>/calculate 1+1</li>
		</ul>
		<br />
		</p>";
	}

	function isJson($string) {
		return ((is_string($string) &&
            (is_object(json_decode($string)) ||
            is_array(json_decode($string))))) ? true : false;
	}

	function getCurrencyRates($currency){
		$url = "https://api.fixer.io/latest?base={$currency}";
		return file_get_contents($url);
	}

	function responseQuery($cmd1){
		$cmd = strtolower($cmd1);
		global $no_commands;
		$res["response"] = "";
		$mode = "";
		if(strpos($cmd,"/calculate") !== FALSE)
			$mode = "math";
		else if(strpos($cmd,"/currency") !== FALSE)
			$mode = "currency";
		else if($cmd === "you" || (strpos($cmd,"intro") !== FALSE) || (strpos($cmd,"yourself") !== FALSE) || (strpos($cmd,"about you") !== FALSE))
			$mode = "intro";
		else if((strpos($cmd,"time") !== FALSE) && (strpos($cmd,"now") !== FALSE) || $cmd == "time")
			$mode = "time";
		else if((strpos($cmd,"your") !== FALSE) && (strpos($cmd,"age") !== FALSE) || $cmd == "age")
			$mode = "age";
		else if(trim($cmd."") == "help")
			$mode = "help";
		switch ($mode) {
			case 'intro':
				$res["response"] = "I am a simple Bot.Bots are a lot like other members of your Slack team, except they're not real people.<br> You can add bots to your team to automate all sorts of tasks.";
				break;
			case 'math':
				$result = solveMath($cmd);
				if($result == null){
					$res["response"] = 'Invalid characters were assigned in the math function!';
				}else{
					$res["response"] = $result;
				}
				break;
			case 'help':
				$res['response'] = help();
				break;
			case 'currency':
				$curr_arr = explode(" ",$cmd);
				$tcurr = strtoupper(substr($curr_arr[7],0,3)."");
				$rates = getCurrencyRates($tcurr);
				$amount = intVal($curr_arr[6]);
				$curr = strtoupper($curr_arr[4]."");
				if(isJson($rates))
				{
					$rates = json_decode($rates);
					$rates = $rates->rates;
					$rate_amount = floatVal($rates->$curr);
					$converted_amount = $rate_amount*$amount;
					$response = "{$amount} in {$tcurr} = {$converted_amount} in {$curr}";
					$res["response"] = $response;
				}
				else
				{
					$res["response"] = "Please follow this command for currency conversion<br /><u>/currency How much in USD is 1000 EUR?</u><br /><b>Note:</b>Use three letters for currency<br>Maintain order";
				}
				break;
			case 'time':
				$res["response"] = date('Y-m-d H:i:s A');
				break;
			case 'age':
				$res["response"] = "Robot don't have age factor, we have time";
				break;
			default:
				$res["response"] = $no_commands[array_rand($no_commands)];
			break;
		}
		echo json_encode($res);
	}
	responseQuery($input);
?>
