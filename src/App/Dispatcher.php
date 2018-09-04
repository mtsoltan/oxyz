<?php
namespace App;

use App\Route as R;

class Dispatcher extends Singleton
{
  /** @var \Slim\App $app */
  private $app;

  private $config;

  private $di;

  /**
   * Returns the slim application object
   *
   * @return \Slim\App
   */
  public static function app()
  {
    return self::getInstance()->app;
  }

  public static function config($key)
  {
    return self::getInstance()->config[$key];
  }

  /**
   * return \App\Entity\User
   */
  public static function user()
  {
      $di = self::di();
      return $di['user'];
  }

  /**
   * Returns the container object
   *
   * @return \Slim\Container
   */
  public static function di()
  {
    return self::getInstance()->di;
  }

  private function initConfig()
  {
      $config = ConfigLoader::load();
      $config['templates.path'] = BASE_ROOT . $config['templates.path'];
      $config['site.file_path'] = BASE_ROOT . $config['site.file_path'];
      $config['database.destroy'] = BASE_ROOT . $config['database.destroy'];
      $config['database.initial'] = BASE_ROOT . $config['database.initial'];
      $config['database.default'] = BASE_ROOT . $config['database.default'];
      $config['cookies.session_save_path'] =
      BASE_ROOT . $config['cookies.session_save_path'];
      $this->config = $config;
  }

  private function initDependencyInjection()
  {
    $di = DependencyInjection::get($this->config);
    $this->di = $di;
  }

  private function initApplication()
  {
    $app = new \Slim\App($this->di);

    $routes = array(
        new R\Main($app),
    );

    $this->di['routes'] = $routes;

    $this->app = $app;
  }

  protected function __construct($args)
  {
    $this->initConfig();
    $this->initDependencyInjection();
    $this->initApplication();
  }
}
