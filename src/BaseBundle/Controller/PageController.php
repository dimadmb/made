<?php

namespace BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class PageController extends Controller
{
	 /**
	 * @Template()
     */
    public function indexAction()
    {
		return  array_merge($this->pageAction(""));
	}



	/**
	 * @Template()
     */
    public function pageAction($url)
    {
		$repository = $this->getDoctrine()->getRepository('BaseBundle:Page');
		$page = $repository->findOneByFullUrl($url);
		if ($page == null) {
			throw $this->createNotFoundException("Страница $url не найдена.");
		}
		
		
		$html = $page->getBody();
/*
		$html = preg_replace_callback(
			'/{\{(.*)\}}/U',
			function ($m) {
				
				//return htmlspecialchars_decode($m[1],ENT_QUOTES );
				
				eval("\$temp = ".htmlspecialchars_decode($m[1],ENT_QUOTES ). " ;");
				
				//return $temp;
				$ret = $this->forward($temp[0],isset($temp[1])?$temp[1]:[])->getContent();
				return $ret;
			},
			$html
		);
*/

		$html = htmlspecialchars_decode($html,ENT_QUOTES );
		$template =  $this->container->get('twig')->createTemplate($html);
		$html = $template->render([]);
		
		$page->html = $html;
		
		$parameters = [];
		$parameters['page'] = $page;
		if("" != $page->getBannerImg())
		{
			$parameters['bannerImg'] = $page->getBannerImg();
		}
		if("" != $page->getBannerHtml())
		{
			$parameters['bannerHtml'] = $page->getBannerHtml();
		}
		
		
		return $parameters;
    }
	

	
}
