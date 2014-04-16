<?php

namespace Nashville\ProductBundle\Serializer;

use Nashville\ProductBundle\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;


class ProductSerializer
{
    
    private $router;
    
    
    public function __construct(Router $router = null)
    {
        $this->router = $router;
    }
    
    /**
     * @return array()
     */
    public function serialize(Product $product) {
       
        return array(
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'url' => $this->router->generate('products_view', 
                array(
                    'id' => $product->getId()
                )
             )
        );        
    }
    
    public function deserialize(array $product) {
        return new Product($product);
    }
}
