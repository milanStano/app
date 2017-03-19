<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
        $router[] = new Route('index.php', 'Front:Homepage:default', Route::ONE_WAY);

        $router[] = $adminRouter = new RouteList('Admin');
        $adminRouter[] = new Route('admin/[<locale=sk sk|en|cz>/]<presenter>/<action>[/<id>][/<do>]', 'Homepage:default');
        $router[] = $frontRouter = new RouteList('Front');
        $frontRouter[] = new Route('', 'Homepage:default');
        $frontRouter[] = new Route('<id>', 'Homepage:stranka');



        return $router;
	}

}
