Please documentation here for the framework

Running Tests:  run the following command from Drupal root:  php scripts/run-tests.sh --color --verbose SPS

SPS (Site Preview System)

Primary Namespace is Drupal\\sps
Files are autoloaded using the xautoload module.
We

Plugins
#######


# Define Plugin Types

hook_sps_plugin_types
- returns an array of plugins keyed by key
- elements
-- class:  Class used for the plugin type
    (Defaults to Drupal\sps\Plugin\PluginType)
-- plugin_class:  The class that the plugin will use
     (Defaults to Drupal\sps\Plugin\Plugin)
-- interface:  The interface that the plugin will use.
     Defaults to Drupal\sps\Plugin\PluginInterface
     This is used for validation in class loading.
-- defaults: an array of default values to use for the plugin def.
