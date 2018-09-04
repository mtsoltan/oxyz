<?php

namespace App\Controller;

class FileCtrl extends BaseCtrl
{
    const ALLOWED_URL_SCHEMES = ['http', 'https'];
    const CACHE_LIFETIME = 2592000; // 30 days.
    const NOTMODIFIED_LIFETIME = 60; // 1 minute. Not used at this point.

    protected $mimeMapping = array(
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'html' => 'text/plain',
        'php' => 'text/plain',
        'py' => 'text/plain',
        'mp4' => 'video/mp4',
        'mov' => 'video/quicktime',
        'avi' => 'video/x-msvideo',
        'wmv' => 'video/x-ms-wmv',
        'webm' => 'video/webm', // Can be audio
        'mp3' => 'audio/mpeg',
        'flac' => 'audio/flac',
        // 'ogg' => 'audio/ogg', // Can be video
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpeg' => 'image/jpg',
        'jpg' => 'image/jpg',
        );
    protected $defaultMime = 'application/octet-stream';

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function serve($request, $response, $args) {
        $fileModel = $this->di['model.file'];
        $data = strtoupper($request->getMethod()) == 'POST' ?
            $request->getParsedBody() : array();

        // Validate Field Presence
        $hash = isset($args['file']) ? $args['file'] : false;
        if (!$hash) {
            throw new \App\Exception\NotFound();
        }

        $file = $fileModel->getBySaltedName($hash);
        if (!$file || !$file->state) {
            throw new \App\Exception\NotFound();
        }

        $response = $response
            ->withHeader('Cache-control', 'public, max-age='.self::CACHE_LIFETIME)
            ->withHeader('Expires', gmdate(DATE_RFC1123,time()+self::CACHE_LIFETIME))
            ->withHeader('Vary', 'Accept-Encoding')
            ->withHeader('strict-transport-security', 'max-age=2628000');

        $content = $file->serveFromDisk();
        $response->write($content);
        if (isset($this->mimeMapping[$file->ext])) {
            $type = $this->mimeMapping[$file->ext];
        } else {
            $type = $this->defaultMime;
            $name = urlencode($file->getName());
            $response = $response->withHeader('Content-Disposition', 'attachment; filename*=UTF-8\'\''.$name.'; filename='.$name);
        }

        return $response->withHeader('Content-Type', $type)->withHeader('Content-Length', $file->size);
    }

    /**
     * TODO: Paginate this.
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function all($request, $response, $args) {
        return $this->view->render($response, '@private/file/all.twig', array(
            'files' => $this->di['model.file']->getEnabled()
        ));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function delete($request, $response, $args) {
        $fileModel = $this->di['model.file'];
        $user = $this->di['user'];
        $strings = $this->di['strings'];
        $data = $request->getParsedBody();
        $handler = new \App\Utilities\Handler($this->di, $request);
        $handler->setReferer($this->view_functions->pathFor('file:all'));

        // Validate CSRF
        if (!$this->doSimpleValidation($request, true)) {
            return $handler->respondWithError($strings['notices.invalid_csrf_token'], $response);
        }

        if (!$user->hasPermission('file_edit')) {
            return $handler->respondWithError($strings['notices.insufficient_permission'], $response);
        }

        // Validate Field Presence
        $salted_name = isset($args['file']) ? $args['file'] : false;
        if (!$salted_name) {
            return $handler->respondWithError($this->di['strings.forms']['required.all'], $response);
        }

        $file = $fileModel->getBySaltedName($salted_name);
        if (!$file) {
            return $handler->respondWithError($this->di['strings.forms']['required.all'], $response);
        }

        $file->delete();

        $gone = count($fileModel->getByHash($file->hash)) == 0;
        if($gone) $file->deleteFromDisk();

        return $handler->respondWithJson(array(
            'success' => $strings['notices.action_success']
        ), $response);
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function uploaded($request, $response, $args) {
        $fileModel = $this->di['model.file'];

        $fileIds = json_decode($this->di['utility.encryption']->decrypt($request->getQueryParam('files')));
        $errors = json_decode(urldecode($request->getQueryParam('errors')));

        if ($errors) foreach ($errors as $error) {
            $this->flash->addMessageNow('flash__alert alert-danger', $error);
        }

        $processedFiles = array();
        if ($fileIds) foreach ($fileIds as $fileId) {
            $file = $fileModel->getById($fileId);
            if ($file) $processedFiles[] = $file;
        }

        // Render upload form.
        return $this->view->render($response, '@private/file/uploaded.twig', array(
            'files' => $processedFiles
        ));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function upload($request, $response, $args) {
        return $this->view->render($response, '@private/file/upload.twig');
    }


    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function handleUpload($request, $response, $args) {
        $strings = $this->di['strings'];
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $handler = new \App\Utilities\Handler($this->di, $request);
        $handler->setReferer($this->view_functions->pathFor('file:uploaded'));

        // Validate CSRF
        if (!$this->doSimpleValidation($request, true)) {
            return $handler->respondWithError($strings['notices.invalid_csrf_token'], $response);
        }

        // Validate File Presence
        if (!$files) {
            return $handler->respondWithError($this->di['strings.forms']['required.file'], $response);
        }

        $errors = array();
        $returnedFiles = array();

        if (isset($data['url']) && strlen($data['url'])) { // Handle URL
            try {
                list($size, $binaryData) = $this->di['utility.file']->getDataFromUrl($data['url']);
                $id = $this->di['utility.file']->attemptFileSave($binaryData, $data['url'], $size, $errors);
                if ($id) $returnedFiles[] = $id;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($files) foreach($files as $fileArray) { // Handle Files
            if (!$fileArray) continue;
            foreach($fileArray as $file) {
                if (!$file->file) { // Last file[] input is always empty.
                    // $errors[] = sprintf($strings['notices.file_empty'], $file->getClientFilename());
                    continue;
                }

                $id = $this->di['utility.file']->attemptFileSave(
                    $file->getStream()->getContents(),
                    $file->getClientFilename(),
                    $file->getSize(),
                    $errors);

                if ($id) $returnedFiles[] = $id;
            }
        }

        $paramArray = array('files' => $this->di['utility.encryption']->encrypt(json_encode($returnedFiles)));
        if ($errors) $paramArray['errors'] = json_encode($errors);
        $handler->setSuccessRedirParams($paramArray);

        return $handler->respondWithJson(array(
            'location' => $handler->getHeaderUrl()
        ), $response);
    }
}
