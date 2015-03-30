# Gravitate-Git-Deploy
PHP Script to call on Git Hooks


Usage

```
  <?php

  include('gitdeploy.php');

  $deploy = new GRAV_GIT_DEPLOY();
  $deploy->allow_browser_access = true; // Change this to false when running from GitHub.  Set to true only for Testing.
  $deploy->secret = 'XXXXXXXXXXXXX';
  $deploy->remote = 'origin';
  $deploy->branch = 'master';
  $deploy->debug = true; // Remove this line or change to false after Successful Pushes.  This can be set to a file to keep a log.
  $deploy->dry_run = true; // Remove this line or change to false after Testing is complete.
  $deploy->run();

```
