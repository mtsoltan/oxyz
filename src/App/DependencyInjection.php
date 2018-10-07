<?php
namespace App;

class DependencyInjection
{
    public static function get($config, $args = array()) {
        if (!$args) {
          $args = array(
              'settings' => array(
                  'addContentLengthHeader' => !($config['mode'] == 'development'),
                  'displayErrorDetails' => ($config['mode'] == 'development'),
                  'determineRouteBeforeAppMiddleware' => true,
                  'tracy' => [
                      'showPhpInfoPanel' => 0,
                      'showSlimRouterPanel' => 1,
                      'showSlimEnvironmentPanel' => 1,
                      'showSlimRequestPanel' => 1,
                      'showSlimResponsePanel' => 1,
                      'showSlimContainer' => 1,
                      'showTwigPanel' => 1,
                      'showProfilerPanel' => 1,
                      'showVendorVersionsPanel' => 0,
                      'showIncludedFiles' => 1,
                      'showConsolePanel' => 0,
                      'showXDebugHelper' => 1,
                      'configs' => [
                          'XDebugHelperIDEKey' => 'app',
                          'ConsoleNoLogin' => 0,
                          'ConsoleAccounts' => [
                              'vagrant' => '9756da65fe52aefb7651f526629b9a04cf6119172caa3bfe027fd2a2409fce4f' // vagrant:vagrant
                          ],
                          'ConsoleHashAlgorithm' => 'sha256',
                          'ConsoleHomeDirectory' => '/code',
                          'ConsoleTerminalJs' => '/static/functions/jquery.terminal.js',
                          'ConsoleTerminalCss' => '/static/styles/jquery.terminal.css',
                          'ProfilerPanel' => [
                              'show' => [
                                  'memoryUsageChart' => 1,
                                  'shortProfiles' => false,
                                  'timeLines' => true
                              ]
                          ]
                      ]
                  ]
              )
          );
        }

        $di = new \Slim\Container($args);

        $di['obLevel'] = ob_get_level();
        $di['config'] = $config;

        $di['user'] = null;
        $di['ip'] = $di['environment']['REMOTE_ADDR'];
        $di['session.id'] = '';

        $di = self::setUtilities($di);
        $di = self::setModels($di);

        $di['db'] = function ($di) {
            try {
                return new \App\Database($di, $di['config']['mode'] == 'development');
            } catch (\PDOException $e) {
                if($di['config']['mode'] == 'development') throw $e;
                throw new \App\Exception\DatabaseException('Could not open database.');
            }
        };

        $di['flash'] = function () {
            return new \Slim\Flash\Messages();
        };

        if ($di['config']['mode'] == 'development') {
            $di['twig_profile'] = function () {
                return new \Twig_Profiler_Profile();
            };
        }

        $di['view'] = function ($di) {
            $dir = $di['config']['templates.path'];
            header('X-Powered-By: '. $di['config']['site.site_url']);
            $di['templates.path'] = $dir;

            $dirs = array();
            $dh  = opendir($dir);

            if (!$dh) throw new \Exception('Unable to open templates path');

            while (false !== ($filename = readdir($dh))) {
                $fullPath = "$dir/$filename";
                if (is_dir($fullPath) && $filename[0] != '.') {
                    $dirs[$filename] = $fullPath;
                }
            }

            $config = array(
                'cache' => BASE_ROOT . $di['config']['templates.cache_path'],
            );

            if ($di['config']['mode'] == 'development') {
                $config['debug'] = true;
                $config['strict_variables'] = true;
                $config['auto_reload'] = true;
            }

            $view = new \Slim\Views\Twig($dirs, $config);

            $view->addExtension(new \App\TwigExtension($di['utility.view']));

            if ($di['config']['mode'] == 'development') {
                $view->addExtension(new \Twig_Extension_Debug());
                $view->addExtension(new \Twig_Extension_Profiler($di['twig_profile']));
            }

            $view->getEnvironment()->addGlobal('di', $di);

            $constants = get_defined_constants();
            foreach ($constants as $name => $value) {
                $view->getEnvironment()->addGlobal($name, $value);
            }

            return $view;
        };

        $di['notFoundHandler'] = function () {
            // delegate to the error handler
            throw new \App\Exception\NotFound();
        };

        if ($config['mode'] != 'development') {
            $di['errorHandler'] = function ($di) {
                $ctrl = new \App\Controller\ErrorCtrl($di);
                return array($ctrl, 'handleException');
            };
        } else unset($di['errorHandler']);

        $di['access'] = function ($di) {
            return function() use ($di) {
                return new \App\Middleware\AccessMiddleware($di);
            };
        };

        return $di;
    }

  private static function setUtilities($di) {
    $di['utility.encryption'] = function ($di) {
      return new \App\Utilities\Encryption($di);
    };

    $di['utility.assets'] = function ($di) {
        return new \App\Utilities\Assets($di);
    };
    $di['utility.view'] = function ($di) {
        return new \App\Utilities\View($di);
    };
    $di['utility.time'] = function ($di) {
        return new \App\Utilities\Time($di);
    };
    $di['utility.string'] = function ($di) {
        return new \App\Utilities\StringUtils($di);
    };
    $di['utility.file'] = function ($di) {
        return new \App\Utilities\File($di);
    };
    return $di;
  }

  private static function setModels($di) {
      $di['model.login_attempt'] = function($di) {
          return new \App\Model\LoginAttempt($di, function(...$c) {
              return new \App\Entity\Entity(...$c);
          });
      };
      $di['model.session'] = function($di) {
          return new \App\Model\Session($di, function(...$c) {
              return new \App\Entity\Entity(...$c);
          });
      };
      $di['model.user'] = function($di) {
          return new \App\Model\User($di, function(...$c) {
              return new \App\Entity\User(...$c);
          });
      };
      $di['model.product'] = function($di) {
          return new \App\Model\Product($di, function(...$c) {
              return new \App\Entity\Product(...$c);
          });
      };
      $di['model.customer'] = function($di) {
          return new \App\Model\Customer($di, function(...$c) {
              return new \App\Entity\Customer(...$c);
          });
      };
      $di['model.order'] = function($di) {
          return new \App\Model\Order($di, function(...$c) {
              return new \App\Entity\Order(...$c);
          });
      };
      $di['model.keystore'] = function($di) {
          return new \App\Model\Keystore($di, function(...$c) {
              return new \App\Entity\Keystore(...$c);
          });
      };
      $di['model.file'] = function($di) {
          return new \App\Model\File($di, function(...$c) {
              return new \App\Entity\File(...$c);
          });
      };
      $di['model.fiancial'] = function($di) {
          return new \App\Model\Financial($di, function(...$c) {
              return new \App\Entity\Financial(...$c);
          });
      };
      $di['model.session_data'] = function($di) {
          return new \App\Model\SessionData($di, function(...$c) {
              return new \App\Entity\Entity(...$c);
          });
      };
      return $di;
  }
}
