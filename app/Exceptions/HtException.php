<?php
/**
 * 自定义异常
 * @authors RockyHuas (1169078896@qq.com)
 * @date    2019-03-05 09:39:41
 * @version $Id$
 */

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class XsdException extends HttpException
{
     public function __construct($statusCode, string $message = null, \Exception $previous = null, array $headers = array(), ?int $code = 0)
     {
         $code = blank($code) ? $code : $statusCode;
         parent::__construct($statusCode, $message, $previous, $headers, $code);
     }
}