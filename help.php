<?php
class help extends Script
{
	protected $description = 'Help-Plugin. Returns the help-message and description of a plugin';
	protected $helpMessage = "'help': Returns the description and helpmessage of every plugin\n"
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

				//Instanciate the plugin, to get the needed informations
				$instance = new $pluginName($this->message, $this->matches, $this->waConnection);

				//Build the message
				if(!empty($message)) {
					$message .= "-----\n";
				}
				$message .= strtoupper($pluginName) . ":\n";
				$message .= $instance->usage() . "\n";

				$instance->__destruct();
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
				}
			} catch(Exception $e) {
				$this->send('The specified plugin ' . $this->matches[1] . ' does not exist!');
			}

			//Instanciate the Object,so that we can get the $helpMessage and $description
			$instance = new $this->matches[1]($this->message, $this->matches, $this->waConnection);

			$this->send($instance->usage());
		}
	}
}
