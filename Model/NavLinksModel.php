<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\PigeonPosseExtraToolsBundle\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\CoreBundle\Helper\Chart\ChartQuery;
use Mautic\CoreBundle\Helper\Chart\LineChart;
use Mautic\CoreBundle\Helper\TemplatingHelper;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Mautic\PageBundle\Model\TrackableModel;
use MauticPlugin\PigeonPosseExtraToolsBundle\Entity\NavLinks;
use MauticPlugin\PigeonPosseExtraToolsBundle\Entity\NavLinksRepository;
use MauticPlugin\PigeonPosseExtraToolsBundle\Event\NavLinksEvent;
use MauticPlugin\PigeonPosseExtraToolsBundle\Form\Type\NavLinksType;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NavLinksModel extends FormModel {

    /**
     * @var ContainerAwareEventDispatcher
     */
    protected $dispatcher;

    /**
     * @var \Mautic\FormBundle\Model\FormModel
     */
    protected $formModel;

    /**
     * @var TrackableModel
     */
    protected $trackableModel;

    /**
     * @var TemplatingHelper
     */
    protected $templating;

    /**
     * @var FieldModel
     */
    protected $leadFieldModel;

    /**
     * @var ContactTracker
     */
    protected $contactTracker;

    /**
     * 
     * @var EntityManager $entityManager
     */
    private static $entityManager;

    /**
     * NavLinksModel constructor.
     */
    public function __construct(
        \Mautic\FormBundle\Model\FormModel $formModel,
        TrackableModel $trackableModel,
        TemplatingHelper $templating,
        EventDispatcherInterface $dispatcher,
        FieldModel $leadFieldModel,
        ContactTracker $contactTracker,
        EntityManager $entityManager
    ) {
        $this->formModel       = $formModel;
        $this->trackableModel  = $trackableModel;
        $this->templating      = $templating;
        $this->dispatcher      = $dispatcher;
        $this->leadFieldModel  = $leadFieldModel;
        $this->contactTracker  = $contactTracker;
        static::$entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function getActionRouteBase() {

        return 'navlinks';

    }

    /**
     * @return string
     */
    public function getPermissionBase() {

        return 'navlinks:items';

    }

    /**
     * {@inheritdoc}
     *
     * @param object                              $entity
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param null                                $action
     * @param array                               $options
     *
     * @throws NotFoundHttpException
     */
    public function createForm(
    	$entity, 
    	$formFactory, 
    	$action = null, 
    	$options = []
    ) {

        if (!$entity instanceof NavLinks) {
            throw new MethodNotAllowedHttpException(['NavLinks']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(
        	NavLinksType::class, 
        	$entity, 
        	$options
        );

    }

    /**
     * {@inheritdoc}
     *
     * @return \MauticPlugin\CustomNavigationLinksBundle\Entity\NavLinksRepository
     */
    public function getRepository() {

    	$repo = $this->em->getRepository( NavLinks::class );

        return $repo;

    }

    /**
     * {@inheritdoc}
     *
     * @param null $id
     *
     * @return NavLinks
     */
    public function getEntity($id = null) {

        if (null === $id) {
            return new NavLinks();
        }

        return parent::getEntity($id);

    }

    /**
     * {@inheritdoc}
     *
     * @param NavLinks      $entity
     * @param bool|false $unlock
     */
    public function saveEntity($entity, $unlock = true) {

        parent::saveEntity($entity, $unlock);
        $this->getRepository()->saveEntity($entity);

    }

    /**
     * {@inheritdoc}
     *
     * @return bool|NavLinksEvent|void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function dispatchEvent(
    	$action, 
    	&$entity, 
    	$isNew = false, 
    	Event $event = null
    ) {

        if (!$entity instanceof NavLinks) {
            throw new MethodNotAllowedHttpException(['NavLinks']);
        }

        switch ($action) {
            case 'pre_save':
                $name = 'mautic.navlinks_pre_save';
                break;
            case 'post_save':
                $name = 'mautic.navlinks_post_save';
                break;
            case 'pre_delete':
                $name = 'mautic.navlinks_pre_delete';
                break;
            case 'post_delete':
                $name = 'mautic.navlinks_post_delete';
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new NavLinksEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }

            $this->dispatcher->dispatch($name, $event);

            return $event;
        } else {
            return null;
        }

    }

    /**
     * Generate custom nav link from DB 
     */
    public function getCustomNavLinks() {

        // Fetch all the publish ustom nav links.
        $menus = $this->getRepository()->getNavLinksByPublished();
        $res = [];

        // return empty array if not exist or does not data
        if ( !isset($menus) || empty($menus) ) return [];


        foreach($menus as $menu){

            $content = [];
            
            $content['iconClass'] 		= $menu->getIcon();
            $content['priority'] 		= $menu->getOrder();
            $content['routeParameters'] = [
            	'url' => $menu->getUrl()
            ];
            
            if($menu->getType() == 'blank'){

                $content['linkAttributes'] = [
                	'target' => '_blank'
                ];
                $content['uri'] =  $menu->getUrl();

            }else{

                $content['route'] = 'mautic_navlinks_iframe';
	            $content['routeParameters']['id'] 	= $menu->getId();
	            $content['routeParameters']['name'] = $menu->getName();

            }

            $res[$menu->getLocation()][trim($menu->getName())] = $content;

        }

        return $res;

    }

    // Update menu config of config.php from DB.
    public function updateMenuConfig() {


        $configFile = dirname(__DIR__).'/Config/config-menu-custom.php';
        $menus 		= $this->getCustomNavLinks();     
        
        // Update config.php file.
        file_put_contents(
        	$configFile,
        	"<?php  \nreturn ".var_export($menus, true).";\n"
        );

    }


}
