<?php

namespace HtProfileImage;

use HtProfileImage\Form\GenderForm;
use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__."/config/module.config.php";
    }

    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $serviceManager = $app->getServiceManager();
        $eventManager = $app->getEventManager();
        $sharedManager = $eventManager->getSharedManager();
        if ($serviceManager->get("HtProfileImage\ModuleOptions")->getEnableGender()) {
            $sharedManager->attach('ZfcUser\Form\Register', 'init', function ($e) {
                $form = $e->getTarget();
                $genderForm = new GenderForm();
                $form->add($genderForm->get('gender'));
            }, 1000);             
        }   
    }


    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'HtProfileImage\ModuleOptions' => 'HtProfileImage\Factory\ModuleOptionsFactory',
                'HtProfileImage\StorageModel' => 'HtProfileImage\Factory\StorageModelFactory',
                'HtProfileImage\ImageUploadService' => 'HtProfileImage\Factory\ImageUploadServiceFactory',
                'HtProfileImage\StorageResizerProvider' => 'HtProfileImage\Factory\StorageResizerProviderFactory',
            )
        );
    }
    

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'htProfileImage' => function ($sm) {
                    $serviceLocator = $sm->getServiceLocator();
                    $htProfileImage = new View\Helper\ProfileImage($serviceLocator->get('HtProfileImage\ModuleOptions'));
                    $htProfileImage->setUserMapper($serviceLocator->get('zfcuser_user_mapper'));
                    $htProfileImage->setStorageModel($serviceLocator->get('HtProfileImage\StorageModel'));
                    return $htProfileImage;
                }            
            )
        );
    }
}
