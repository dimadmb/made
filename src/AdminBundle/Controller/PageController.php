<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BaseBundle\Entity\Page;
use BaseBundle\Entity\Image;
use BaseBundle\Form\PageType;

/**
 * Page controller.
 *
 * @Route("/page")
 */
class PageController extends Controller
{
    /**
     * Lists all Page entities.
     *
     * @Route("/", name="page_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('BaseBundle:Page')->findAll();

        return $this->render('page/index.html.twig', array(
            'pages' => $pages,
        ));
    }

    /**
     * Creates a new Page entity.
     *
     * @Route("/new", name="page_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm('BaseBundle\Form\PageType', $page);
        $form->handleRequest($request);

		
		$images = $request->request->get("image");

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
			$localUrl = trim($page->getLocalUrl())."";
			$parentUrl = ($page->getParent() == null) ? "" : $page->getParent()->getFullUrl();
			$fullUrl = ($parentUrl == "" || $parentUrl == "/" ) ? $localUrl : $parentUrl.'/'.$localUrl;
			$page->setParentUrl($parentUrl);
			$page->setFullUrl($fullUrl);
			$page->setLocalUrl($localUrl);
			
			
			if ($images)
			{
				foreach($images as $id => $title)
				{
					$image = $em->getRepository('BaseBundle:Image')->findOneById($id);
					if($image == null) continue;
					$image->setTitle($title);
					$page->addFile($image);
					$em->persist($image);
				}	
			}

			$em->persist($page);
            $em->flush();

            return $this->redirectToRoute('page_show', array('id' => $page->getId()));
			
	
			
        }

        return $this->render('page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Page entity.
     *
     * @Route("/{id}", name="page_show")
     * @Method("GET")
     */
    public function showAction(Page $page)
    {
        $deleteForm = $this->createDeleteForm($page);

        return $this->render('page/show.html.twig', array(
            'page' => $page,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     * @Route("/{id}/edit", name="page_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Page $page)
    {
        $deleteForm = $this->createDeleteForm($page);
        $editForm = $this->createForm('BaseBundle\Form\PageType', $page);
        $editForm->handleRequest($request);
		
		$images = $request->request->get("image");

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
			
			$localUrl = trim($page->getLocalUrl())."";
			$parentUrl = ($page->getParent() == null) ? "" : $page->getParent()->getFullUrl();
			$fullUrl = ($parentUrl == "" || $parentUrl == "/" ) ? $localUrl : $parentUrl.'/'.$localUrl;
			$page->setParentUrl($parentUrl);
			$page->setFullUrl($fullUrl);
			$page->setLocalUrl($localUrl);			
			
			if ($images)
			{
				foreach($images as $id => $title)
				{
					$image = $em->getRepository('BaseBundle:Image')->findOneById($id);
					if($image == null) continue;
					$image->setTitle($title);
					$page->addFile($image);
					$em->persist($image);
				}	
			}
            $em->persist($page);
            $em->flush();

            return $this->redirectToRoute('page_edit', array('id' => $page->getId()));
        }

        return $this->render('page/edit.html.twig', array(
            'page' => $page,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Page entity.
     *
     * @Route("/{id}", name="page_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();
        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Creates a form to delete a Page entity.
     *
     * @param Page $page The Page entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Page $page)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('page_delete', array('id' => $page->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}