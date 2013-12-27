#!/usr/bin/env php
<?php

use \Toggl\Api\TogglApiClientV8;

function classloader() {
    $autoloadFile = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadFile)) {
        throw new RuntimeException('Install dependencies to run test suite.');
    }
    require_once $autoloadFile;

    $loader = new \Composer\Autoload\ClassLoader();
    $loader->register();
}

function getApiToken()
{
    print "Enter your Api Token (https://new.toggl.com/app/#profile): ";

    if ($fh = fopen('php://stdin','r')) {
        if ($api_token = fscanf($fh, "%s\n")) {
            $api_token = $api_token[0];
        }
        else {
            throw new \Exception("Fatal: Couldn't read api_token from stdin");
        }
    }
    else {
        throw new \Exception("Fatal: Couldn't open stdin for reading.");
    }

    return $api_token;
}

function main() {
    @classloader();
    $api_token = @getApiToken();

    $toggl = TogglApiClientV8::factory(array(
            'authentication_method' => 'token',
            'authentication_key' => $api_token,
        ));

    $workspaces = $toggl->getWorkspaces();
    echo "Workspaces available to this user: {$workspaces}\n";

    foreach($workspaces as $workspace) {
        echo "Listing projects from the '{$workspace}' workspace.\n";
        $projects = $toggl->getWorkspaceProjects($workspace['id'], array('active' => 'both'));
        foreach ($projects as $project) {
            echo " * {$project}\n";
        }

        $createdProject = $toggl->createProject(array(
                'wid' => $workspace['id'],
                'name' => "toggl-php sample project " . time(),
                'is_private' => false,
                'billable' => true,
            ));
        echo " * NEW: {$createdProject}\n";
    }
}

main();