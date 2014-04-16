<?php


namespace Nashville\ProductBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LanguageListener
{
    private $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function onKernelRequest(GetResponseEvent $event) {
      
       // $this->logger->error('error>> I just got the logger call inside onKernelRequest');
        
        $request = $event->getRequest();
        $language = $request->query->get('language');
        if(strlen($language) > 0) {
            $this->logger->info('info>> language changed to ' . $language);
        }
        
    }
    
}
