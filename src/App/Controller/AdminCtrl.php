<?php

namespace App\Controller;

class AdminCtrl extends BaseCtrl
{
    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function reset($request, $response, $args) {
        $this->di->db->reset();
        ob_start();
        var_dump($this->di->db);
        $result = ob_get_clean();
        return $this->view->render($response, '@private/admin/reset.twig', array(
            'result' => $result
        ));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function handleSql($request, $response, $args) {
        $db = $this->di->db;
        $input = $request->getParsedBody()['sql'];

        if (!$this->doSimpleValidation($request, true)) {
            $this->flash->addMessageNow('flash__alert alert-danger', $this->di['strings']['notices.invalid_csrf_token']);
            return $this->view->render($response, '@private/admin/sql.twig');
        }
        ob_start();
        var_dump($db->query($input)->fetchAll($db::FETCH_ASSOC));
        $result = ob_get_clean();
        return $this->view->render($response, '@private/admin/sql.twig', array(
            'result' => $result,
            'input' => $input));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function sql($request, $response, $args) {
        return $this->view->render($response, '@private/admin/sql.twig');
    }
}
