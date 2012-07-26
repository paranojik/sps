Please documentation here for the framework

## Running Tests:

Run the following command from Drupal root replacing http://lsd.dev:9999/ with your URL

php scripts/run-tests.sh --color --verbose --url http://lsd.dev:9999/ SPS
php scripts/run-tests.sh --color --verbose --url http://lsd.dev:9999/ SPSInteractive
php scripts/run-tests.sh --color --verbose --url http://lsd.dev:9999/ SPSIntegration
php scripts/run-tests.sh --color --verbose --url http://lsd.dev:9999/ SPSPostTests


## SPS (Site Preview System)

Primary Namespace is Drupal\\sps
Files are autoloaded using the xautoload module.

