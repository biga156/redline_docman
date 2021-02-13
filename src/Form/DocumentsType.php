<?php

namespace App\Form;

//use App\Form\ExtentedChoiceType;
//use App\Form\Extensions\ExtentedChoicesSubscriber;

use DateTimeZone;
use App\Entity\Tags;
use App\Entity\Users;
use App\Entity\Documents;
//use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DocumentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       // $date = new DateTime('2012-09-01 09:00:00', new DateTimeZone('Europe/Paris'));
        $builder
            
            ->add('label', TextType::class, [
                'required' => true,
                'label' => 'Label (*)'
            ])
            ->add('note' , TextareaType::class, [
                'required' => false,
                'label' => 'Note (facultativ)'
            ])
            ->add('protection')
            ->add('validity', DateType::class, [
                'required' => false,
                'help' => 'In this version it is just an informative feature. ',
                'label' => 'Validity (If you do not change it, it is inactive by default.)',
                'widget' => 'single_text',
                'data' => new \DateTime('2050-01-01'),
                              
            ])
            ->add('alarm', DateTimeType::class, [
                'disabled' => false,
                'help' => 'In this version it is just an informative feature. ',
                'label' => 'Alarm (If you do not change it, it is inactive by default.)',
                'widget' => 'choice',
                'data' => new \DateTime('now-1 days'),
                'view_timezone' => 'Europe/Paris',
            ])
           
            //TODO: this is a file!!
           
               
            ->add('audioNote', FileType:: class, [
                'label'=>'Audio note file (mp3/ogg/wav) ',
                'help'=>'Upload a max 8MB audio file. To record an audio note, you can use our audio recorder too',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '8M',
                       //TODO: Is it a   problem with the default mimetype validation of symfony?
                        /*'mimeTypes' =>[
                           "audio/mpeg",
                           "audio/ogg",
                           "audio/wav",
                           "audio/mp3",
                           "audio/webm",
                           
                       ],
                       'mimeTypesMessage' => 'Please upload a valid audio file (.wav/.ogg/.mp3/.webm)',*/
                    ])

                ],
                
            ])
            //*Multiple selection tags
            //?dynamic search bar?
            ->add('tags', EntityType::class, array(
                'class' => Tags::class,
                'choice_label' => 'label', 
                'multiple'=>true,
                'expanded' => false,
                'label' => 'Keywords',
                'choice_value'=> 'id',
               
            ))
            /*
            NOTE: queryBuilder version
            ->add("tags",  EntityType::class, [
                'class' => Tags::class,
                'query_builder' => function (TagsRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            */

            //*This is an example for use the tags in JSON array
            //* needed: use App\Form\ExtentedChoiceType;
            // * and:   use App\Form\Extensions\ExtentedChoicesSubscriber;

            /*
            ->add('tags',ExtentedChoiceType::class,
            array("multiple"=>true))
            ->addEventSubscriber(new ExtentedChoicesSubscriber())
          */
            /*
            //*This is unnecessary, just an example
            TODO: to be processed in the controller
            ->add('user', EntityType::class, [
                //* query choices from this entity
                'class' => Users::class,
               //*version1: simple selection
               // 'choice_label' => 'username',
               //*version 2: detailed selection: format:  (%d)- %s
                 'choice_label' => function (Users $user) {
                    return sprintf(
                        '(%d)- %s',
                        $user=$this->getUser(),
                        $user->getId(),
                        $user->getUsername()
                     );
                },
                'label' => 'Users',
                'disabled'=>true,
            ])*/;
            
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Documents::class,
        ]);
    }
}
