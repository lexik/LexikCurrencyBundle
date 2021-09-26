<?php

namespace Lexik\Bundle\CurrencyBundle\EventListener;

use Lexik\Bundle\CurrencyBundle\Currency\FormatterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class LocaleListener implements EventSubscriberInterface
{
    public function __construct(private FormatterInterface $formatter)
    {
    }

    /**
     * @return array<string, array<int[]|string[]>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                // must be registered before the default Locale listener
                ['setCurrencyFormatterLocale', 17]
            ],
        ];
    }

    public function setCurrencyFormatterLocale(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        $this->formatter->setLocale($request->getLocale());
    }
}
