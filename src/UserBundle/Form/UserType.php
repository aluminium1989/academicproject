<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class UserType extends AbstractType
{
    private $roles;

    public function __construct($roles)
    {

        $this->prepareRoles($roles);
    }

    private function prepareRoles($roles)
    {
        $preparedRoles = array();
        array_walk_recursive($roles, function ($val) use (&$preparedRoles) {
            $preparedRoles[str_replace("ROLE_", "", $val)] = $val;
        });

        $this->roles = $preparedRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('username')
            ->add('fullName')
            ->add('password')
            ->add('roles', ChoiceType::class, array('choices'  => $this->roles))
            ->add('projects', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class'    => 'ProjectBundle\Entity\Project',
                'choice_label' => 'label',
                'expanded' => true,
                'multiple' => true
            )) ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_user';
    }


}
