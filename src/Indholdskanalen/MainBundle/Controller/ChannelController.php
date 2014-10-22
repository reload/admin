<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

use Indholdskanalen\MainBundle\Entity\Channel;
use Indholdskanalen\MainBundle\Entity\ChannelSlideOrder;

/**
 * @Route("/api/channel")
 */
class ChannelController extends Controller {
  /**
   * Save a (new) channel.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ChannelSaveAction(Request $request) {
    // Get posted channel information from the request.
    $post = json_decode($request->getContent());

    $doctrine = $this->getDoctrine();
    $em = $this->getDoctrine()->getManager();

    if ($post->id) {
      // Load current slide.
      $channel = $doctrine->getRepository('IndholdskanalenMainBundle:Channel')
        ->findOneById($post->id);

      // If channel is not found, return Not Found
      if (!$channel) {
        $response = new Response();
        $response->setStatusCode(404);

        return $response;
      }
    }
    else {
      // This is a new slide.
      $channel = new Channel();
	    $em->persist($channel);
    }

    // Update fields.
    if (isset($post->title)) {
      $channel->setTitle($post->title);
    }
    if (isset($post->orientation)) {
      $channel->setOrientation($post->orientation);
    }
    if (isset($post->created_at)) {
      $channel->setCreatedAt($post->created_at);
    }

    // Remove screens.
    foreach($channel->getScreens() as $screen) {
      if (!in_array($screen, $post->screens)) {
        $channel->removeScreen($screen);
      }
    }

    // Add screens.
    foreach($post->screens as $screen) {
      $screen = $doctrine->getRepository('IndholdskanalenMainBundle:Screen')
        ->findOneById($screen->id);
      if ($screen) {
        if (!$channel->getScreens()->contains($screen)) {
          $channel->addScreen($screen);
        }
      }
    }

    // Get all slide ids from POST.
    $postSlideIds = array();
    foreach($post->slides as $slide) {
      $postSlideIds[] = $slide->id;
    }

    // Remove slides.
    foreach($channel->getChannelSlideOrders() as $channelSlideOrder) {
      $slide = $channelSlideOrder->getSlide();

      if (!in_array($slide->getId(), $postSlideIds)) {
	      $channel->removeChannelSlideOrder($channelSlideOrder);
      }
    }

    // Add slides and update sort order.
    $sortOrder = 0;
    foreach($postSlideIds as $slideId) {
      $slide = $doctrine->getRepository('IndholdskanalenMainBundle:Slide')->findOneById($slideId);

      $channelSlideOrder = $doctrine->getRepository('IndholdskanalenMainBundle:ChannelSlideOrder')->findOneBy(
        array(
          'channel' => $channel,
          'slide' => $slide,
        )
      );
      if (!$channelSlideOrder) {
        // New ChannelSLideOrder
	      $channelSlideOrder = new ChannelSlideOrder();
        $channelSlideOrder->setChannel($channel);
        $channelSlideOrder->setSlide($slide);
	      $em->persist($channelSlideOrder);

	      // Associate Order to Channel
	      $channel->addChannelSlideOrder($channelSlideOrder);
      }

      $channelSlideOrder->setSortOrder($sortOrder);
      $sortOrder++;
    }

    // Save the entity.
    $em->flush();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($channel) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($channel, 'json');

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }

  /**
   * Get channel with $id.
   *
   * @Route("/{id}")
   * @Method("GET")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ChannelGetAction($id) {
    $channel = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Channel')
      ->findOneById($id);

    $serializer = $this->get('jms_serializer');

    // Create response.
    $response = new Response();
    if ($channel) {
      $response->headers->set('Content-Type', 'application/json');
      $jsonContent = $serializer->serialize($channel, 'json', SerializationContext::create()->setGroups(array('api')));
      $response->setContent($jsonContent);
    }
    else {
      $response->setStatusCode(404);
    }

    return $response;
  }
}
