<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;

use Kkos2\KkOs2DisplayIntegrationBundle\Slides\ColorfulMessagesFeedData;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\Mock\MockColorfulMessagesData;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ColorfulMessageSisCron implements EventSubscriberInterface {

  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;

  /**
   * @var int $numberOfEvents
   */
  private $numberOfEvents;

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public static function getSubscribedEvents()
  {
    return [
      'os2displayslidetools.sis_cron.kk_color_messages_sis_cron' => [
        ['getSlideData'],
      ]
    ];
  }

  public function getSlideData(SlidesInSlideEvent $event)
  {
    $slide = $event->getSlidesInSlide();

    // Make sure that only one subslide pr. slide is set. The value is
    // for the user, but the colorful slides don't support more than one, so
    // enforce it here.
    $slide->setOption('sis_items_pr_slide', 1);
    $numEvents = $slide->getOption('sis_total_items', 12);
    $url = $slide->getOption('datafeed_url', '');
    if ($slide->getOption('datafeed_display')) {
      $url .= '?display=' . $slide->getOption('datafeed_display');
    }

    $data = new ColorfulMessagesFeedData($this->logger, $url, $numEvents);
    $slide->setSubslides($data->getColorfulMessages());
  }

  public function getMockSlideData(SlidesInSlideEvent $event)
  {
    $slide = $event->getSlidesInSlide();

    // Make sure that only one subslide pr. slide is set. The value is
    // for the user, but the colorful slides don't support more than one, so
    // enforce it here.
    $slide->setOption('sis_items_pr_slide', 1);
    $numEvents = $slide->getOption('sis_total_items', 12);

    $mockData = new MockColorfulMessagesData($numEvents);
    $slide->setSubslides($mockData->getColorfulMessages());
  }

}