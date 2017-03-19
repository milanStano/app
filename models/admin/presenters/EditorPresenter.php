<?php
/**
 * Created by PhpStorm.
 * User: minos
 * Date: 31.01.2017
 * Time: 10:52
 */

namespace App\Admin\Presenters;


use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;


class EditorPresenter extends BasePresenter
{

    /** @var \App\Admin\Model\EditorModel @inject */
    public $editor;
    public function handleRemove($id){
        $this->editor->removeDocument($id);
    }
    protected function createComponentEditSite()
    {
        if (!$this->user->isLoggedIn() or !$this->user->isInRole(self::ADMIN_ROLE)){
            $this->redirect('Sign:in');
        }
        $form = new Form();
     $form->addGroup("");
        $form->addText('nazov_dokumentu', 'Názov dokumentu');
        $form->addTextArea('seo_url');
        $form->addTextArea('tag');
        $form->addText('datumOd');
        $form->addText('datumDo');
        $form->addTextArea('obsah');




        $form->addSubmit('odoslat', 'Uložiť zmeny')
        ->setAttribute('class','btn btn-light-green');


        $form->onSuccess[] = [$this, 'addDocument'];

        return $form;
    }
    public function renderPridatPodStranku(){
        if (!$this->user->isLoggedIn() or !$this->user->isInRole(self::ADMIN_ROLE)){
          $this->redirect('Sign:in');
        }
    }
    public function addDocument(Form $form, $vaules){
        if (!$this->user->isLoggedIn() or !$this->user->isInRole(self::ADMIN_ROLE)){
            $this->redirect('Sign:in');
        }

        try {
            if ($vaules->nazov_dokumentu==""){
                $this->flashMessage('Názov dokumentu je prázdny','alert alert-danger');
                return;
            }
            if ($vaules->seo_url =="")//ak použivateľ nevytovril url adresu vygenerujeme ju z návzvu dokumentu !
            {
                if ($vaules->nazov_dokumentu==""){
                    $vaules->seo_url = $this->handleSeo($vaules->nazov_dokumentu);
                }else{
                    $this->flashMessage('Názov dokumentu povinný','alert alert-danger');
                    return;
                }

            } elseif ($this->handleSeoIsOk($vaules->seo_url)==false)//kontrola platnosti seo url adresy
            {
                $this->flashMessage('Url/seo adresa nie je platná prosím zadajte inú adresu alebo ju vygenerujte z názvu','alert alert-danger');
                return;
            }
        if ($this->editor->isFreeUrl($vaules->seo_url)){//kontorlujeme či je voľná zadaná url adresa
        $this->editor->addDocument($vaules->nazov_dokumentu,$vaules->seo_url,$vaules->tag,$vaules->datumOd,$vaules->datumDo,$vaules->obsah,$this->user->getId());
            $this->flashMessage('Stránka bola úspešene vytovrená','fa fa-info-circle message alert alert-success');
        }else{//ak nie je informujeme o tom užívateľa

                $this->flashMessage('Pozor dokument s takouto url adresou už existuje','alert alert-danger');


        }

        }

        catch (\PDOException $E){
            $E->getMessage();
            if ($E->getCode()==23000){
                $this->flashMessage('Url s týmto názvom už existuje !','alert alert-danger');
            }else{
            $this->flashMessage('Dokument sa nepodarilo vytvoriť skúste to prosím neskôr, ak problém pretrváva kontaktuje správcu !','alert alert-danger');
            }
        }

    }
    public function handleSeo($nazov){
        if (!$this->user->isLoggedIn() or !$this->user->isInRole(self::ADMIN_ROLE)){
            $this->redirect('Sign:in');
        }
            if ($this->isAjax()){
                $this->payload->seo=Strings::webalize($nazov);
                $this->sendPayload();
            }
            else{
                return Strings::webalize($nazov);
            }

        return Strings::webalize($nazov);
    }
    public function handleSeoIsOk($nazov){
        if (!$this->user->isLoggedIn() or !$this->user->isInRole(self::ADMIN_ROLE)){
            $this->redirect('Sign:in');
        }
        if ($this->isAjax()){
            if ($nazov==Strings::webalize($nazov)){
                $this->payload->ok=true;
            }else{
                $this->payload->ok=false;
            }
            $this->sendPayload();
        }
        else{
            if ($nazov==Strings::webalize($nazov)){
               return true;
            }else{
                return false;
            }
        }

        return Strings::webalize($nazov);
    }
    public function renderDefault(){
        if (!$this->user->isLoggedIn() or !$this->user->isInRole(self::ADMIN_ROLE)){
            $this->redirect('Sign:in');
        }
           $this->template->podstranky=$this->editor->returnDocument();

    }

}