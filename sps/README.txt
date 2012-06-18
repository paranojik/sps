Please documentation here for the framework

Running Tests:  run the following command from Drupal root:  php scripts/run-tests.sh --color --verbose SPS

SPS (Site Preview System)

Primary Namespace is Drupal\\sps

Plugins
#######

Anything under the Drupal\sps\Plugins\<pluginname>\*.php that implement the plugins interface

- PluginTypeInterface:  Each Plugin Type Class Must implement this interface
- AbstractPluginType:  This is a Abstract class to base the Plugin Type Classes ON
- PluginInterface:  Each Plugin Instance MUST implement this interface
- PluginCollection:  A collection of Plugins
- PluginFactor:  Instantiates plugin objects

## Rules
- Each plugin Type must define a Type Class that implements PluginTypeInterface
- Each Plugin MUST implement PluginInterface
- The plugin type name must must be Drupal\sps\Plugin\Type\<TypeName>
- THe plugins themselves are at Drupal\sps\Plugins\<TypeName>\<PluginName>

Example

Plugin Type name "TestType"

## Plugin Type
Class: Drupal\sps\Plugin\Type\TestType\TestType
File: <module_root>/lib/Drupal/sps/Plugin/Type/TestType/TestType.php
Extends AbstractPluginType

## Plugin Definition for TestType
Interface: Drupal\sps\Plugin\Type\TestType\TestTypeInterface
- File: <module_root>/lib/Drupal/sps/Plugin/Type/TestType/TestTypeInterface.php
Abstract Class: Drupal\sps\Plugin\Type\TestType\AbstractPlugin implements TestTypeInterface and PluginInterface
- File: <module_root>/lib/Drupal/sps/Plugin/Type/TestType/AbstractTestType.php

## Plugin:  TestType1
Class: Drupal\sps\Plugins\TestType\TestType1 extends AbstractTestType
File: <module_root>/lib/Drupal/sps/Plugins/TestType/TestType1.php

## Plugin:  TestType2
Class: Drupal\sps\Plugins\TestType\TestType2 extends AbstractTestType
File: <module_root>/lib/Drupal/sps/Plugins/TestType/TestType2.php



SPS Manager
###########

Class:  SPS\Manager
Purpose:  Control the loading and caching of objects in SPS
