<?php


namespace App\EventListener;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;

final class MenuBuilderListener
{
    public function addMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $child = $menu->getChild('Votes')->addChild('Sodage', [
            'label' => 'Sondage',
            'route' => 'app_admin_vote_stat',
        ])->setExtras([
            'icon' => 'fa fa-bar-chart', // html is also supported
        ]);
        
    }
}