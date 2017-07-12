<?php
/**
 * This file is part of the BEAR.Package package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Error;

use BEAR\AppMeta\AbstractAppMeta;
use BEAR\Sunday\Extension\Router\RouterMatch;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class ErrorLogger
{
    /**
     * @var
     */
    private $logger;

    /**
     * @var AbstractAppMeta
     */
    private $appMeta;

    public function __construct(LoggerInterface $logger, AbstractAppMeta $appMeta)
    {
        $this->logger = $logger;
        $this->appMeta = $appMeta;
    }

    /**
     * @param \Exception  $e
     * @param RouterMatch $request
     *
     * @return string logref
     */
    public function __invoke(\Exception $e, RouterMatch $request)
    {
        $level = $e->getCode() >= 500 ? Logger::ERROR : Logger::DEBUG;
        $logRef = new LogRef($e);
        $message = sprintf('req:"%s" code:%s e:%s(%s) logref:%s', (string) $request, $e->getCode(), get_class($e), $e->getMessage(), (string) $logRef);
        $this->logger->log($level, $message);
        $logRef->log($e, $request, $this->appMeta);

        return (string) $logRef;
    }
}