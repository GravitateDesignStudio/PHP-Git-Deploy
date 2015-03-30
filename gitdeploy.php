<?php

/*
*
*	SEE USAGE OR Change Settings at the bottom of the page.
*
*/

class GRAV_GIT_DEPLOY
{
	// setting this to false will REQUIRE that the call needs to originate and verify from github.
	// This requires you to set the Secret in the GitHub Webhook.
	var $allow_direct_access = false;

	// secret key either in the GitHub Webhook or in the $_GET['secret'] if $allow_direct_access is set to false.
	// make sure it is url safe. Ex no & or = etc.
	var $secret = '';

	// Remote of the Repo that you want to Pull from
	var $remote = 'origin';

	// Branch of the Repo that you want to Pull from
	var $branch = 'master';


	// The Url you want to redirect after the call has finished.
	// Used to hide the fact that there is a Git Deploy file.
	var $redirect_url = '/';

	// Git Command.  Sometimes you need to use the bin library.  Ex /usr/bin/git
	var $git_command = 'git';

	// Just Test, but don't actually change any files.  You should have the Debug file Set to make this
	var $dry_run = false;

	 /**
	 * boolean|string
     * Specify a file to write logs to  OR  Leave empty to not log
     * @values  false 			=  No Debug
     *			true  			=  This will echo the debug info on the page
     *			[file_name]		=  Name of the file you wish to add the Debug info to. Ex. 'gitdeploy_8e9k4j4b6s7k8.log'
     **/
	var $debug = false;

    /**
     * Runs and Calls the Git Commands
     * @return void
     **/
	function run()
	{
		if(!empty($this->debug))
		{
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors', 1);
		}

		$this->log('File was initialized');

		if(!empty($this->secret) && !empty($_SERVER['HTTP_X_HUB_SIGNATURE']))
		{
			$this->log('Called by GitHub');

		    $hubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
		    list($algo, $hash) = explode('=', $hubSignature, 2);
		    $payload = file_get_contents('php://input');
		    $payloadHash = hash_hmac($algo, $payload, $this->secret);

		    if($hash === $payloadHash)
		    {
		        $is_secure = true;
		        $this->log('Secret was secure');
		    }
		    else
		    {
		    	$this->log('Secret Failed Payload Hash');
		    }
		}
		else if(!empty($_GET['secret']) && $_GET['secret'] === $this->secret)
		{
			if($this->allow_direct_access)
			{
				$is_secure = true;
				$this->log('Call was secure');
			}
			else
			{
				$this->log('Direct Access is not allowed');
			}
		}
		else
		{
			$this->log('Missing or Incorrect Secret');
		}

		///////////////////////////////////////////////
		// Run the Git Commands
		///////////////////////////////////////////////

		if(!empty($is_secure))
		{

			if(!empty($this->debug))
			{
				exec($this->git_command." --version 2>&1", $out);
				$this->log("Checking Git Version: ".implode("\n", $out));
			}

			$out = '';

			$git_dir = exec($this->git_command." rev-parse --show-toplevel");

			if(file_exists($git_dir.'/.git/FETCH_HEAD'))
			{
				if(is_writable($git_dir.'/.git/FETCH_HEAD'))
				{
					chdir($git_dir);
					$this->log("Changed Directory to ".getcwd());

					$commands = array();
					$commands[] = $this->git_command." fetch ".$this->remote." --verbose";

					if(empty($this->dry_run))
					{
						$commands[] = $this->git_command." reset --hard ".$this->remote."/".$this->branch;
					}

					foreach($commands as $command)
					{
						exec($command." 2>&1", $out);
						$this->log(implode("\n", $out));
					}

					if(!empty($this->dry_run))
					{
						$this->log('Finished Dry Run');
					}
				}
				else
				{
					$this->log('PHP doesnt have permissions to update git');
				}
			}
			else
			{
				$this->log('Cant find git Directory');
			}

			$this->log('End Call');
		}

		///////////////////////////////////////////////
		///////////////////////////////////////////////

		if(empty($payload) && $this->debug !== true)  // Only Redirect if not coming from Github
		{
			$this->log('Redirecting to '.$this->redirect_url);
			header("Location: ".$this->redirect_url);
		}
		exit;
	}

	private function log($data)
	{
		if(!empty($this->debug))
		{
			if(is_string($this->debug))
			{
				file_put_contents($this->debug, "\n".date('m-d-y g:i:sa').' - '.$data, FILE_APPEND);
			}
			else
			{
				echo '<br>'.date('m-d-y g:i:sa').' - '.$data;
			}
		}
	}
}

/* Usage */
$deploy = new GRAV_GIT_DEPLOY();
$deploy->allow_direct_access = true; // Change this to false when running from GitHub.  Set to true only to test from the browser.
$deploy->secret = 'XXXXXXXXXXXXX';
$deploy->remote = 'origin';
$deploy->branch = 'master';
$deploy->debug = true; // Remove this line or change to false after Successful Pushes.
$deploy->dry_run = true; // Remove this line or change to false after Testing is complete.
$deploy->run();

