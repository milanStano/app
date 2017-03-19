<?php

namespace App\Admin\Presenters;

use Nette;
use App\Model;


class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
	    if (!$this->user->isLoggedIn()){
	        $this->redirect("Sign:in");
        }

		$this->template->anyVariable = 'any value';
	}



}
