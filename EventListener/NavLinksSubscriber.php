<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\PigeonPosseExtraToolsBundle\EventListener;

use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\CoreBundle\Model\AuditLogModel;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\CoreBundle\Helper\IpLookupHelper;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Helper\TokenHelper;
use Mautic\AssetBundle\Helper\TokenHelper as AssetTokenHelper;
use Mautic\PageBundle\Helper\TokenHelper as PageTokenHelper;
use Mautic\PageBundle\Entity\Trackable;
use Mautic\PageBundle\Model\TrackableModel;
use MauticPlugin\PigeonPosseExtraToolsBundle\Event\NavLinksEvent;
use MauticPlugin\PigeonPosseExtraToolsBundle\Model\NavLinksModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class NavLinksSubscriber implements EventSubscriberInterface {
   
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var IpLookupHelper
     */
    private $ipHelper;

    /**
     * @var AuditLogModel
     */
    private $auditLogModel;

    /**
     * @var TrackableModel
     */
    private $trackableModel;

    /**
     * @var PageTokenHelper
     */
    private $pageTokenHelper;

    /**
     * @var AssetTokenHelper
     */
    private $assetTokenHelper;

    /**
     * @var NavLinksModel
     */
    private $navlinksModel;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        RouterInterface $router,
        IpLookupHelper $ipLookupHelper,
        AuditLogModel $auditLogModel,
        TrackableModel $trackableModel,
        PageTokenHelper $pageTokenHelper,
        AssetTokenHelper $assetTokenHelper,
        NavLinksModel $navlinksModel,
        RequestStack $requestStack
    ) {
        $this->router           = $router;
        $this->ipHelper         = $ipLookupHelper;
        $this->auditLogModel    = $auditLogModel;
        $this->trackableModel   = $trackableModel;
        $this->pageTokenHelper  = $pageTokenHelper;
        $this->assetTokenHelper = $assetTokenHelper;
        $this->navlinksModel    = $navlinksModel;
        $this->requestStack     = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {

        return [
            KernelEvents::REQUEST          		=> ['onKernelRequest', 0],
            'mautic.navlinks_post_save'         => ['onNavLinksPostSave', 0],
            'mautic.navlinks_post_delete'       => ['onNavLinksDelete', 0],
            'mautic.navlinks_token_replacement' => ['onTokenReplacement', 0],
        ];

    }

    /*
     * Check and hijack the form's generate link if the ID has mf- in it
     */
    public function onKernelRequest(GetResponseEvent $event) {

        if ($event->isMasterRequest()) {
            // get the current event request
            $request    = $event->getRequest();
            $requestUri = $request->getRequestUri();

            $formGenerateUrl = $this->router->generate('mautic_form_generateform');

            if (false !== strpos($requestUri, $formGenerateUrl)) {
                $id = InputHelper::_($this->requestStack->getCurrentRequest()->get('id'));
                if (0 === strpos($id, 'mf-')) {
                    $mfId             = str_replace('mf-', '', $id);
                    $focusGenerateUrl = $this->router->generate('mautic_navlinks_action', ['id' => $mfId]);

                    $event->setResponse(new RedirectResponse($focusGenerateUrl));
                }
            }
        }
    }

    /**
     * Add an entry to the audit log.
     */
    public function onNavLinksPostSave(NavLinksEvent $event) {

        $entity = $event->getNavLinks();
        $this->navlinksModel->updateMenuConfig();
        if ($details = $event->getChanges()) {
            $log = [
                'bundle'    => 'navlinks',
                'object'    => 'navlinks',
                'objectId'  => $entity->getId(),
                'action'    => ($event->isNew()) ? 'create' : 'update',
                'details'   => $details,
                'ipAddress' => $this->ipHelper->getIpAddressFromRequest(),
            ];
            
            $this->auditLogModel->writeToLog($log);
            
        }

    }

    /**
     * Add a delete entry to the audit log.
     */
    public function onNavLinksDelete(NavLinksEvent $event) {

        $entity = $event->getNavLinks();

        $log    = [
            'bundle'    => 'navlinks',
            'object'    => 'navlinks',
            'objectId'  => $entity->deletedId,
            'action'    => 'delete',
            'details'   => ['name' => $entity->getName()],
            'ipAddress' => $this->ipHelper->getIpAddressFromRequest(),
        ];

        $this->auditLogModel->writeToLog($log);
        $this->navlinksModel->updateMenuConfig();

    }

    public function onTokenReplacement(
    	MauticEvents\TokenReplacementEvent $event
    ) {

        /** @var Lead $lead */
        $lead         = $event->getLead();
        $content      = $event->getContent();
        $clickthrough = $event->getClickthrough();

        if ($content) {
            $tokens = array_merge(
                $this->pageTokenHelper->findPageTokens($content, $clickthrough),
                $this->assetTokenHelper->findAssetTokens($content, $clickthrough)
            );

            if ($lead && $lead->getId()) {
                $tokens = array_merge($tokens, TokenHelper::findLeadTokens($content, $lead->getProfileFields()));
            }

            list($content, $trackables) = $this->trackableModel->parseContentForTrackables(
                $content,
                $tokens,
                'navlinks',
                $clickthrough['navlinks_id']
            );

            $navlinks = $this->navlinksModel->getEntity($clickthrough['navlinks_id']);

            /**
             * @var string
             * @var Trackable $trackable
             */
            foreach ($trackables as $token => $trackable) {
                $tokens[$token] = $this->trackableModel->generateTrackableUrl($trackable, $clickthrough, false, $focus->getUtmTags());
            }

            $content = str_replace(array_keys($tokens), array_values($tokens), $content);

            $event->setContent($content);
        }

    }

}
