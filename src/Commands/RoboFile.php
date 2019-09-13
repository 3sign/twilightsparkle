<?php

namespace TwilightSparkle\Commands;

use \Robo\Robo;
use Symfony\Component\Yaml\Yaml;


/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  protected function getSettings() {
    $settingsfile = $_SERVER['HOME'] . '/.twilightsparkle.yml';
    if(!file_exists($settingsfile)) {
      $this->taskFilesystemStack()
        ->touch($settingsfile)
        ->run();
    }
    return Yaml::parseFile($settingsfile);
  }

  function install() {
    $settings = $this->getSettings();
    $settings['workspace'] = $this->ask('What is the absolute path to your workspace? example: /Users/example/workspace');

    $yaml = Yaml::dump($settings);
    $this->taskWriteToFile($_SERVER['HOME'] . '/.twilightsparkle.yml')
      ->text($yaml)
      ->run();

  }

  function generateDrupal8 () {
    $settings = $this->getSettings();
    if (!isset($settings['workspace'])) {
      $this->install();
      $settings = $this->getSettings();
    }

    $working_root = $settings['workspace'];

    $project_name = $this->ask('What is your project name?');
    $working_dir = $working_root . '/' . $project_name;

    // create drupal project
    $this->taskComposerCreateProject()
      ->source('drupal-composer/drupal-project:8.x-dev')
      ->dir($working_root)
      ->target($project_name)
      ->option('no-interaction')
      ->run();

    // add codebasehq repo
    $this->taskComposerConfig()
      ->dir($working_dir)
      ->repository('spike', 'git@codebasehq.com:3sign/3sign/spike.git', 'git')
      ->run();

    $this->taskComposerConfig()
      ->dir($working_dir)
      ->set('scripts.spike', "robo --load-from vendor/3sign/spike --ansi < /dev/tty")
      ->run();

    // add spike
    $this->taskComposerRequire()
      ->dir($working_dir)
      ->dependency('3sign/spike')->run();
  }
}
