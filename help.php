<?php
class help extends Script
{
	private $helpMessages = array();
	private $plugins = array();

	public function run()
	{
		if(count($this->matches) == 1) {
			//Get a list of all installed plugins
			$plugins = glob(getcwd() . '/scripts/*' , GLOB_ONLYDIR);

			//Loop through the Plugins
			foreach ($plugins as $plugin) {
				$pluginName = array_pop(explode('/', $plugin));

				require_once $plugin . '/' . $pluginName . '.php';

				//Instanciate the plugin, to get the needed informations
				$instance = new $pluginName($this->message, $this->matches, $this->waConnection);

				if(empty($instace->description)) {
					$this->plugins[$pluginName] = 'No description provided';
				} else {
					$this->plugins[$pluginName] = $instance->description;
				}

				if(empty($instance->helpMessage)) {
					//Should we send the regex?
					$this->helpMessages[$pluginName] = 'No help-message provided :(';
				} else {
					$this->helpMessages[$pluginName] = $instance->helpMessage;
				}

				$instance->__destruct();
			}

			//Build the final message
			$message = '';
			foreach ($this->plugins as $key => $value) {
				if(!empty($message)) {
					$message .= "-----\n";
				}
				$message .= $key . ":\n" . $value . "\n";
				$message .= $this->helpMessages[$key] . "\n";
			}
			$this->send($message);
		} else {
			//Display help only for the specified moule
			require_once getcwd() . '/scripts/' . $this->matches[1] . '/' . $this->matches[1] . '.php';

			//Instanciate the Object,so that we can get the $helpMessage and $description
			$instance = new $this->matches[1]($this->message, $this->matches, $this->waConnection);

			if(empty($instace->description)) {
				$message = "No description provided\n";
			} else {
				$message = $instance->description . "\n";
			}

			$message .= "----------\n";

			if(empty($instance->helpMessage)) {
				//Should we send the regex?
				$message .= 'No help-message provided :(';
			} else {
				$message .= $instance->helpMessage;
			}
			$this->send($message);
		}
	}
}
