# ckeditor_plugin

Ckeditor_plugin

Drupal 7 module.

This Drupal module can be used to add text manipulation options to the ckeditor module.
In this module a class is created with the detault functions.
The main functions are:
	- public function validatePluginAction($node)
	- public function executePluginAction($node)
	
	These 2 functions will be executed in de module file with the hooks
	- hook_node_validation
	- hook_node_presave
 
See module ckeditor_plugin_createNode for more info how to create/ use the plugin for text manipulation
options in de body text.

