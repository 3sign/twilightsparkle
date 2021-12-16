<?php

namespace TwilightSparkle\Commands;

use Robo\Tasks;
use Symfony\Component\Yaml\Yaml;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends Tasks {

  /**
   * Get the settings from the global settings file.
   *
   * @return array
   *   The settings.
   */
  protected function getSettings() {
    $settingsfile = $_SERVER['HOME'] . '/.twilightsparkle.yml';

    if (!file_exists($settingsfile)) {
      $this->taskFilesystemStack()
        ->touch($settingsfile)
        ->run();
    }
    return Yaml::parseFile($settingsfile);
  }

  /**
   * Install Twilight Sparkle into the system.
   */
  public function install() {
    $settings = $this->getSettings();
    $settings['workspace'] = $this->ask('What is the absolute path to your workspace? example: /Users/example/workspace');

    $yaml = Yaml::dump($settings);
    $this->taskWriteToFile($_SERVER['HOME'] . '/.twilightsparkle.yml')
      ->text($yaml)
      ->run();

  }

  /**
   * Generate a new drupal 8 website.
   */
  public function generateDrupal8() {
    $working_root = $this->getRoot();

    $project_name = $this->ask('What is your project name?');
    $working_dir = $working_root . '/' . $project_name;

    // Create drupal project.
    $this->taskComposerCreateProject()
      ->source('drupal-composer/drupal-project:8.x-dev')
      ->dir($working_root)
      ->target($project_name)
      ->option('no-interaction')
      ->run();

    $this->installSpike($working_dir);
  }

  /**
   * Generate a new Drupal 9 website.
   */
  public function generateDrupal9($project_name = '') {
    $working_root = $this->getRoot();

    if (empty($project_name)) {
      $project_name = $this->ask('What is your project name?');
    }

    $working_dir = $working_root . '/' . $project_name;

    // Create drupal project.
    $this->taskComposerCreateProject()
      ->source('drupal-composer/drupal-project:9.x-dev')
      ->dir($working_root)
      ->target($project_name)
      ->option('no-interaction')
      ->run();

    $this->installSpike($working_dir);

  }

  /**
   * Generate a new Drupal sandbox website.
   */
  public function generateSandbox() {
    $working_root = $this->getRoot();

    $project_name = $this->ask('What is your project name?');
    $php_version = $this->askDefault('What php version do you want to use?', '8.1');
    $mysql_version = $this->askDefault('What mysql version do you want to use?', '8.0');
    $working_dir = $working_root . '/' . $project_name;

    // Create drupal project.
    $this->generateDrupal9($project_name);

    $this->execCommand('touch .env', $working_dir);

    $command = ['composer spike site:setup', '--', $project_name, $php_version, $mysql_version, '--force'];
    $this->execCommand(implode(' ', $command), $working_dir);
    $command = ['composer spike site:install', '--', $project_name, 'en demo_umami --sandbox'];
    $this->execCommand(implode(' ', $command), $working_dir);

  }

  /**
   * Get the workspace from global settings.
   *
   * @return string
   *   Workspace location.
   */
  private function getRoot() {
    $settings = $this->getSettings();
    if (!isset($settings['workspace'])) {
      $this->install();
      $settings = $this->getSettings();
    }

    return $settings['workspace'];
  }

  /**
   * Install Spike into a new project.
   *
   * @param string $working_dir
   *   The working directory of the new project.
   */
  private function installSpike($working_dir) {
    // Add codebasehq repo.
    $this->taskComposerConfig()
      ->dir($working_dir)
      ->repository('spike', 'git@codebasehq.com:3sign/3sign/spike.git', 'git')
      ->run();

    $this->taskComposerConfig()
      ->dir($working_dir)
      ->set('scripts.spike', "robo --load-from vendor/3sign/spike --ansi < /dev/tty")
      ->run();

    // Add spike.
    $this->taskComposerRequire()
      ->dir($working_dir)
      ->dependency('3sign/spike')->run();
  }

  protected function execCommand($command, $dir) {
    $cd = 'cd ' .  $dir   . ' && ';
    $this->say($cd . $command);
    $output = shell_exec($cd . $command);
    $this->say($output);
    return $output;
  }

}
