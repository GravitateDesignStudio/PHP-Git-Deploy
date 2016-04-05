# Gravitate-Git-Deploy
PHP Script to call on Git Hooks


Usage

```
  <?php

  include('gitdeploy.php');

  $gitdeploy = new GIT_DEPLOY();
  $gitdeploy->allow_browser_access = true; // Change this to false when running from GitHub.  Set to true only for Testing.
  $gitdeploy->secret = 'XXXXXXXXXXXXX'; // GitHub Secret Key or $_GET['secret'] if running from the browser.
  $gitdeploy->remote = 'origin';
  $gitdeploy->branch = 'master';
  $gitdeploy->debug = true; // Remove this line or change to false after Successful Pushes.  This can be set to a file to keep a log.
  $gitdeploy->dry_run = true; // Remove this line or change to false after Testing is complete.
  $gitdeploy->run();

```
