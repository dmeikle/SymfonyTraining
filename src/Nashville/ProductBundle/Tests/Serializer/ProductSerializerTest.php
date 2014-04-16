<?php

namespace Nashville\ProductBundle\Tests\Serializer;

use Nashville\ProductBundle\Serializer\ProductSerializer;
use Nashville\ProductBundle\Entity\Product;

class ProductSerializerTest extends \PHPUnit_Framework_TestCase
{
    
    public function testSerialize() {
        
        $router = $this->getMockBuilder(
            'Symfony\Bundle\FrameworkBundle\Routing\Router'
        )->disableOriginalConstructor()->getMock();
        
        $router->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('/fake-page'));
        
        $productSerializer = new ProductSerializer($router);
        
        $product = new Product();
        $product->setName('foo-test');
        $retval = $productSerializer->serialize($product);
        
        
        $this->assertEquals('foo-test', $retval['name']);
        $this->assertEquals('/fake-page', $retval['url']);
    }
    
}
