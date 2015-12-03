<?php
class help extends Script
{
	protected static $description = 'Help-Plugin. Returns the help-message and description of a plugin';
	protected static $helpMessage = "'help': Returns the description and helpmessage of every plugin\n"
						  . "'help <PLUGINNAME>': Returns the description and helpmessage of the specified plugin";
	private $helpMessages = array();
	private $plugins = array();

	public function run()
	{
		if(count($this->matches) == 1) {
			//Get a list of all installed plugins
			$plugins = glob(getcwd() . '/scripts/*' , GLOB_ONLYDIR);

			$message = '';
			//Loop through the Plugins
			foreach ($plugins as $plugin) {
				$pluginName = array_pop(explode('/', $plugin));

				require_once $plugin . '/' . $pluginName . '.php';

				//Build the message
				if(!empty($message)) {
					$message .= "-----\n";
				}
				$message .= strtoupper($pluginName) . ":\n";
				$message .= $pluginName::usage() . "\n";
			}

			$this->send($message);
		} else {
			//Display help only for the specified moule
			try {
				$pluginPath = getcwd() . '/scripts/' . $this->matches[1] . '/' . $this->matches[1] . '.php';

				if (!file_exists($pluginPath)) {
					throw new Exception ($pluginPath . ' does not exist');
				} else {
					require_once($pluginPath);
					
					$this->send($this->matches[1]::usage());
				}
			} catch(Exception $e) {
				$this->send('The specified plugin ' . $this->matches[1] . ' does not exist!');
			}

		}
	}
}
