<?php

/**
 * @see       https://github.com/laminas/laminas-progressbar for the canonical source repository
 * @copyright https://github.com/laminas/laminas-progressbar/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-progressbar/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ProgressBar\Upload;

use Laminas\ProgressBar\Exception;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

/**
 * Progress Bar Upload Handler for the APC extension
 */
class ApcProgress extends AbstractUploadHandler
{
    /**
     * @param  string $id
     * @return array|boolean
     * @throws Exception\PhpEnvironmentException
     */
    protected function getUploadProgress($id)
    {
        if (!$this->isApcAvailable()) {
            throw new Exception\PhpEnvironmentException('APC extension is not installed');
        }

        $uploadInfo = apc_fetch(ini_get('apc.rfc1867_prefix') . $id);
        if (!is_array($uploadInfo)) {
            return false;
        }

        $status  = array(
            'total'    => 0,
            'current'  => 0,
            'rate'     => 0,
            'message'  => '',
            'done'     => false
        );
        $status = $uploadInfo + $status;
        if (!empty($status['cancel_upload'])) {
            $status['done'] = true;
            $status['message'] = 'The upload has been canceled';
        }

        return $status;
    }

    /**
     * Checks for the APC extension
     *
     * @return boolean
     */
    public function isApcAvailable()
    {
        return (bool) ini_get('apc.enabled')
            && (bool) ini_get('apc.rfc1867')
            && is_callable('apc_fetch');
    }
}
