<?php
namespace App\Route;

use App\Controller\AdminCtrl;
use App\Controller\SessionCtrl;
use App\Controller\FileCtrl;
use App\Controller\MainCtrl;
use App\Controller\OrderCtrl;
use App\Controller\UserCtrl;

class Main extends Base
{
    /* For preflight, please use $this->options('path', Controller::class.':method')->setName('controller:preflight'); */
    protected function addRoutes()
    {
        $app = $this->app;

        $app->group('', function () {
            $this->get('/', MainCtrl::class.':index')->setName('main:index');
            $this->get('/products', MainCtrl::class.':products')->setName('main:products');
            $this->get('/contact', MainCtrl::class.':contact')->setName('main:contact');
            $this->get('/{file:[0-9a-f]+}.{ext:[0-9a-z]+}', FileCtrl::class.':serve')->setName('file:serve');
            $this->post('/{file:[0-9a-f]+}.{ext:[0-9a-z]+}', FileCtrl::class.':serve')->setName('file:handleServe');

            // Public Routes
            $this->group('', function () {
                $this->get('/login', SessionCtrl::class.':login')->setName('session:login');
                $this->post('/login', SessionCtrl::class.':handleLogin')->setName('session:handleLogin');
                $this->post('/reset', SessionCtrl::class.':handleResetPassword')->setName('session:handleResetPassword');
            })->add($this->access()->ensureLoggedOut());

            $this->group('/order', function () {
                $this->get('/service/{product:[0-9]+}', OrderCtrl::class.':add')->setName('order:add');
                $this->post('/service/{product:[0-9]+}', OrderCtrl::class.':handleAdd')->setName('order:handleAdd');
                $this->get('/thank_you', OrderCtrl::class.':added')->setName('order:added');
            });

            // Private Routes
            $this->group('', function () {
                $this->get('/logout', SessionCtrl::class.':logout')->setName('session:logout');
                $this->get('/dashboard', MainCtrl::class.':dashboard')->setName('main:dashboard');
            })->add($this->access()->ensureLoggedIn());

            // Admin Routes
            $this->group('/admin', function () {
                $this->get('/users', UserCtrl::class.':all')->setName('user:all'); // 0% Allows searching and filtering users.
                $this->get('/user/edit/{user:[0-9]+}', UserCtrl::class.':edit')->setName('user:edit'); // 0%
            })->add($this->access()->requirePermission('user_edit'));

            $this->group('/admin', function () {
                $this->get('/files', FileCtrl::class.':all')->setName('file:all');
                $this->get('/upload', FileCtrl::class.':upload')->setName('file:upload');
                $this->post('/upload', FileCtrl::class.':handleUpload')->setName('file:handleUpload');
                $this->get('/uploaded', FileCtrl::class.':uploaded')->setName('file:uploaded');
                $this->post('/file/delete/{file:[0-9a-f]+}', FileCtrl::class.':delete')->setName('file:delete'); // 0%
            })->add($this->access()->requirePermission('file_edit'));

            $this->group('/admin', function () {
                $this->get('/user/add', UserCtrl::class.':add')->setName('user:add');
                $this->post('/user/add', UserCtrl::class.':handleAdd')->setName('user:handleAdd');
            })->add($this->access()->requirePermission('user_add'));

            $this->group('/root', function () {
                $this->get('/reset', AdminCtrl::class.':reset')->setName('admin:reset');
                $this->get('/sql', AdminCtrl::class.':sql')->setName('admin:sql');
                $this->post('/sql', AdminCtrl::class.':handleSql')->setName('admin:handleSql');
            });//->add($this->access()->requirePermission('root'));
        });
    }
}
