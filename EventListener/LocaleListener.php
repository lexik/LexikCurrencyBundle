<?php

namespace Lexik\Bundle\CurrencyBundle\EventListener;

use Lexik\Bundle\CurrencyBundle\Currency\FormatterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var \Lexik\Bundle\CurrencyBundle\Currency\FormatterInterface
     */
    private $formatter;

    /**
     * @param FormatterInterface $formatter
     */
    public function __construct(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(
                array('setCurrencyFormatterLocale', 17) // must be registered before the default Locale listener
            ),
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function setCurrencyFormatterLocale(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        $this->formatter->setLocale($request->getLocale());
    }
}
