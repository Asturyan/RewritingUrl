<?php
namespace RewritingUrl\Controller\Admin;

use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Security\AccessManager;
use Thelia\Model\RewritingUrlQuery;

use RewritingUrl\Event\RewritingUrlEvent;
use RewritingUrl\Event\RewritingUrlUpdateEvent;
use RewritingUrl\Event\RewritingUrlCreateEvent;
use RewritingUrl\Event\RewritingUrlDeleteEvent;
use RewritingUrl\Form\RewritingUrlCreationForm;
use RewritingUrl\Form\RewritingUrlModificationForm;

class RewritingUrlController extends AbstractCrudController
{
    
    public function __construct()
    {
        parent::__construct(
            'rewritingurl',
            'id',
            'order',

            'admin.rewriting',

            RewritingUrlEvent::REWRITING_CREATE,
            RewritingUrlEvent::REWRITING_UPDATE,
            RewritingUrlEvent::REWRITING_DELETE,
            null, // No visibility toggle
            null // no position change
        );
    }
    
    protected function getCreationForm()
    {
        return new RewritingUrlCreationForm($this->getRequest());
    }

    protected function getUpdateForm()
    {
        return new RewritingUrlModificationForm($this->getRequest());
    }

    protected function getCreationEvent($data)
    {
        $createEvent = new RewritingUrlCreateEvent();

        $createEvent
            ->setUrl($data['url'])
            ->setViewLocale($data["view_locale"])
            ->setView($data['view'])
            ->setViewId($data['view_id'])
            ->setRedirected($data['redirected'])
            ;

        return $createEvent;
    }

    protected function getUpdateEvent($data)
    {
        $changeEvent = new RewritingUrlUpdateEvent($data['id']);

        // Create and dispatch the change event
        $changeEvent
            ->setUrl($data['url'])
            ->setViewLocale($data["view_locale"])
            ->setView($data['view'])
            ->setViewId($data['view_id'])
            ->setRedirected($data['redirected'])
        ;

        return $changeEvent;
    }

    protected function getDeleteEvent()
    {
        return new RewritingUrlDeleteEvent($this->getRequest()->get('rewriting_id'));
    }

    protected function eventContainsObject($event)
    {
        return $event->hasRewritingUrl();
    }

    protected function hydrateObjectForm($object)
    {
        // Prepare the data that will hydrate the form
        $data = array(
            'id'            => $object->getId(),
            'view'          => $object->getView(),
            'view_id'       => $object->getViewId(),
            'url'           => $object->getUrl(),
            'view_locale'   => $object->getViewLocale(),
            'redirected'    => $object->getRedirected()
        );

        // Setup the object form
        return new RewritingUrlModificationForm($this->getRequest(), "form", $data);
    }

    protected function getObjectFromEvent($event)
    {
        return $event->hasRewritingUrl() ? $event->getRewritingUrl() : null;
    }

    protected function getExistingObject()
    {
        $rewriting = RewritingUrlQuery::create()
        ->findOneById($this->getRequest()->get('rewriting_id'));

        return $rewriting;
    }

    protected function getObjectLabel($object)
    {
        return $object->getUrl();
    }

    protected function getObjectId($object)
    {
        return $object->getId();
    }
    
    protected function getEditionArguments()
    {
        return array(
            'rewriting_id' => $this->getRequest()->get('rewriting_id', 0),
        );
    }

    protected function renderListTemplate($currentOrder)
    {
        if (is_null($this->getSession()->get('show_default'))) $this->getSession()->set('show_default', 1);
        return $this->render('rewriting-index', array('order' => $currentOrder, 'show_default' => $this->getSession()->get('show_default')));
    }

    protected function renderEditionTemplate()
    {
        return $this->render('rewriting-edit', $this->getEditionArguments());
    }

    protected function redirectToEditionTemplate()
    {
        $args = $this->getEditionArguments();
        $this->redirect('/admin/module/RewritingUrl/update?rewriting_id='.$args['rewriting_id']);
    }

    protected function redirectToListTemplate()
    {
        $this->redirect('/admin/module/RewritingUrl');
    }

    /**
     * Change values modified directly from the variable list
     *
     * @return Thelia\Core\HttpFoundation\Response the response
     */
    public function changeValuesAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE)) return $response;

        $urls = $this->getRequest()->get('url', array());
        $views = $this->getRequest()->get('view', array());
        $view_ids = $this->getRequest()->get('view_id', array());
        $redirecteds = $this->getRequest()->get('redirected', array());

        // Process all changed variables
        foreach ($urls as $id => $value) {
            $event = new RewritingUrlUpdateEvent($id);
            $event->setUrl($urls[$id])
                ->setRedirected($redirecteds[$id])
                ->setViewId(isset($views_ids[$id])?$view_ids[$id]:NULL)
                ->setView(isset($views[$id])?$views[$id]:NULL)
                ->setViewLocale($this->getCurrentEditionLocale());
                
            $this->dispatch(RewritingUrlEvent::REWRITING_UPDATE, $event);
        }

        $this->redirect('/admin/module/RewritingUrl');
    }
    
    public function showToggleAction()
    {
        $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE);
        $this->checkXmlHttpRequest();
        $this->getSession()->set('show_default', $this->getRequest()->get('show_default') == 'true'?1:0);
        $args = array('show_default' => $this->getSession()->get('show_default'));

        return $this->render('includes/rewriteurl-list-ajax', $args);
    }

}