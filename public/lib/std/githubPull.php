<?php
/**
  * This script is for easily deploying updates to Github repos to your local server. It will automatically git clone or
  * git pull in your repo directory every time an update is pushed to your $BRANCH (configured below).
  *
  * INSTRUCTIONS:
  * 1. Edit the variables below
  * 2. Upload this script to your server somewhere it can be publicly accessed
  * 3. Make sure the apache user owns this script (e.g., sudo chown www-data:www-data webhook.php)
  * 4. (optional) If the repo already exists on the server, make sure the same apache user from step 3 also owns that
  *    directory (i.e., sudo chown -R www-data:www-data)
  * 5. Go into your Github Repo > Settings > Service Hooks > WebHook URLs and add the public URL
  *    (e.g., http://example.com/webhook.php)
  *
**/

// Set Variables
$LOCAL_ROOT         = dirname(dirname(dirname(dirname(__FILE__))));
$LOCAL_REPO_NAME    = "REPO_NAME";
$LOCAL_REPO         = "{$LOCAL_ROOT}/{$LOCAL_REPO_NAME}";
$REMOTE_REPO        = "https://github.com/TDXDigital/TPS.git";
$IDENT_FILE         = "./id_rsa";
$BRANCH             = "master";
if(!file_exists("githubConfig.php")){
    throw new \Exception("git webhook is not configured");
}
require "githubConfig.php";
# use this gitignored file to override repoName or other config params
if($LOCAL_REPO_NAME == "REPO_NAME"){
    throw new \Exception("git webhook is not configured");
}

// Only respond to POST requests from Github

if( file_exists($LOCAL_REPO) ) {

  // If there is already a repo, just run a git pull to grab the latest changes
  print shell_exec("cd {$LOCAL_REPO} && git pull -i {$IDENT_FILE}");

  print("done " . mktime());
} else {

  // If the repo does not exist, then clone it into the parent directory
  print shell_exec("cd {$LOCAL_ROOT} && git clone {$REMOTE_REPO} -i {$IDENT_FILE}");

  print("done " . mktime());
}
