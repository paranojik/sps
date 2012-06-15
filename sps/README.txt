Please documentation here for the framework

Running Tests:  run the following command from Drupal root:  php scripts/run-tests.sh --color --verbose SPS

SPS (Site Preview System)

Primary Namespace is Drupal\\sps

Plugins
#######
Anything under the Drupal\\sps\Plugins\<pluginname>\*.php that uses the SPS\Plugin\PluginInterface will be used as plugins


SPS Manager
###########

Class:  SPS\Manager
Purpose:  Control the loading and caching of objects in SPS
