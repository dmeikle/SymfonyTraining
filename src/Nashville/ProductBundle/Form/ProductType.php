<?php
 
namespace Nashville\ProductBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView ;
use Symfony\Component\Form\FormInterface;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text',
                array('label' => 'My name'))
            ->add('description', 'textarea')
            ->add('price', 'number')
            //->add('choice', 'choice')
            ->add('save', 'submit');
    }
 
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nashville\ProductBundle\Entity\Product',
            'attr' => array('novalidate'=>'novalidate')
        ));
    }
 
    public function getName()
    {
        return 'product';
    }
    
    public function finishView(FormView $view, FormInterface $form, array $options) {
        $view['name']->vars['attr'] = array('class' => 'test');
        $view['name']->vars['help'] = array('class' => 'gimme your name');
    }
    
 
} 