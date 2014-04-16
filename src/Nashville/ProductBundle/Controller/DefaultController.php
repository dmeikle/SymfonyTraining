<?php

namespace Nashville\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nashville\ProductBundle\Entity\Product;
use Nashville\ProductBundle\Form\ProductType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nashville\ProductBundle\Serializer\ProductSerializer;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ProductBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function newAction() {
        return $this->render('ProductBundle:Default:new.html.twig', array('form' => $this->drawForm()->createView()));
    }
    
    public function saveAction(Request $request) {
        
        $form = $this->drawForm();
        $form->handleRequest($request);
        $validator = $this->get('validator');
    
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());//could also use $product if it was in scope
            $em->flush();
            
            return $this->redirect($this->generateUrl('product_homepage'));//product_homepage is the key in the local routing.yml
        }
       
        return $this->render('ProductBundle:Default:new.html.twig', array('form' => $form->createView()));
    }
    
    private function drawForm() {
        $product = new Product();
        $product->setDescription('this is a test');
        $form = $this->createForm(new ProductType(), $product);
          // $form = $this->createFormBuilder(null,
                // array('attr' => array('novalidate'=>'novalidate'),//turn off HTML5 validation
                // 'data_class' => 'Nashville\ProductBundle\Entity\Product'))
            // ->add('name', 'text')
            // ->add('description', 'textarea')
            // ->add('price', 'number')
            // ->add('save', 'submit')
            // ->getForm();
          
          // or:
          // $form = $this->createFormBuilder($product)
            // ->add('name', 'text')
            // ->add('description', 'textarea')
            // ->add('price', 'number', array('required' => false))
            // ->add('save', 'submit')
            // ->getForm();
            
        return $form;        
    }
    
    public function listAction() {
        $repository = $this->getDoctrine()->getRepository('ProductBundle:Product');
   
        $products = $repository->findAll();
        
       return  $this->render('ProductBundle:Default:list.html.twig',array('products' => $products));
    }
    
    public function listJsonAction() {
        $repository = $this->getDoctrine()->getRepository('ProductBundle:Product');
   
        $products = $repository->findAll();
        
        $data = array();
        //$serializer = new ProductSerializer($this->container->get('router'));
        $serializer = $this->container->get('my_product_serializer');
        foreach($products as $product) {
            $data[] = $serializer->serialize($product);    
        }
        
        $response = new JsonResponse($data);
        
        return $response;
    }
    
    public function listAlphaAscAction() {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ProductBundle:Product');
   
        $products = $repository->findAllAlphabetically();
        
       return  $this->render('ProductBundle:Default:list.html.twig',array('products' => $products));
    }
    
    public function listAlphaDescAction() {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ProductBundle:Product');
   
        $products = $repository->createQueryBuilder('p')
            ->orderBy('p.name','DESC')
            ->getQuery()
            ->execute();
        
       return  $this->render('ProductBundle:Default:list.html.twig',array('products' => $products));
    }
    
    public function viewAction($id) {
        
        $product = $this->getDoctrine()
        ->getRepository('ProductBundle:Product')
        ->findOneById($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return $this->render('ProductBundle:Default:view.html.twig',array('product' => $product));
    }
    
    public function removeAction(Request $request) {
         $product = $this->getDoctrine()
            ->getRepository('ProductBundle:Product')
            ->findOneById($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        
        return $this->redirect($this->generateUrl('products_list'));
    }
}
