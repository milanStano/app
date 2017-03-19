<?php
/**
 * Created by PhpStorm.
 * User: minos
 * Date: 31.01.2017
 * Time: 14:48
 */

namespace App\Admin\Model;


use Nette\Database\Context;
use Nette\Utils\Arrays;
use Nette\Utils\DateTime;

class EditorModel extends BaseModel
{


    /**
     * EditorModel constructor.
     */

    private $db;




    public function __construct(Context $db)
    {
        $this->db=$db;
    }

    /**
     * @param $nazov_dokumentu
     * @param $seo_url
     * @param $tag
     * @param $datumOd
     * @param $datumDo
     * @param $obsah
     * @param $user
     * add new document to flack_documents
     */
    public function addDocument($nazov_dokumentu,$seo_url,$tag,$datumOd,$datumDo,$obsah,$user){
        if ($datumOd==""){//nezverejnený dokument
            $dataTimer=array(
                self::TIMER_DATE_TIME_UPDATE=>new DateTime(),
                self::USER_ID=>$user
            );
        }elseif($datumDo==""){//stále zverejnený dokument
            $dataTimer=array(
                self::TIMER_DATE_TIME_START=>DateTime::from($datumOd),
                self::TIMER_DATE_TIME_UPDATE=>new DateTime(),
                self::USER_ID=>$user
            );
        }else{//dokkument ktorý je zverejnený od jedného dátumu do druhého dátumu
        $dataTimer=array(
            self::TIMER_DATE_TIME_START=>DateTime::from($datumOd),
            self::TIMER_DATE_TIME_END=>DateTime::from($datumDo),
            self::TIMER_DATE_TIME_UPDATE=>new DateTime(),
            self::USER_ID=>$user
        );
        }
        $timer=$this->db->table(self::TIMER_NAME_TABLE)->insert($dataTimer);//vytovríme timer tj od kedy do kedy má byť dokument zverejnený
        $dataDocument=array(
            self::DOCUMENT_SEO_URL=>$seo_url,
            self::DOCUMENT_TITLE=>$nazov_dokumentu,
            self::DOCUMENT_CONTENTS=>$obsah,
            self::DOCUMENT_CREATE_DATE_TIME=>new DateTime(),
            self::USER_ID=>$user,
            self::TIMER_ID=>$timer->id_timer,
            self::DOCUMENT_TAGS=>$tag,
            self::DOCUMENT_PRIORITY=>$this->maxPriorityDocument()+1//priorita dokument tj poradia dokumentov ako sa majú zobrazovať


        );
        $this->db->table(self::DOCUMENT_NAME_TABLE)->insert($dataDocument);//vytvoríme nový dokuemnt
    }
    public function updateDocument($nazov_dokumentu,$seo_url,$tag,$datumOd,$datumDo,$obsah,$user,$id_document){
        $dataDocument=array(
            self::DOCUMENT_SEO_URL=>$seo_url,
            self::DOCUMENT_TITLE=>$nazov_dokumentu,
            self::DOCUMENT_CONTENTS=>$obsah,
            self::DOCUMENT_CREATE_DATE_TIME=>new DateTime(),
            self::USER_ID=>$user,
            self::DOCUMENT_TAGS=>$tag,



        );
        $dataTimer=array(
            self::TIMER_DATE_TIME_START=>DateTime::from($datumOd),
            self::TIMER_DATE_TIME_END=>DateTime::from($datumDo),
            self::TIMER_DATE_TIME_UPDATE=>new DateTime(),
            self::USER_ID=>$user
        );
        $data=$this->db->table(self::DOCUMENT_NAME_TABLE)->where(self::DOCUMENT_ID,$id_document)->update($dataDocument);
       $this->db->table(self::TIMER_NAME_TABLE)->where(self::TIMER_ID,$data[self::DOCUMENT_TIMER]);
    }
    /**
     * @return \Nette\Database\ResultSet
     * info on document
     */
    public function returnDocument(){

      return  $this->db->query('select '.
          self::DOCUMENT_NAME_TABLE.'.'.self::DOCUMENT_TITLE.' ,'.
          self::DOCUMENT_NAME_TABLE.'.'.self::DOCUMENT_SEO_URL.' ,'.
          self::DOCUMENT_NAME_TABLE.'.'.self::USER_ID.' AS creator,'.
          self::DOCUMENT_NAME_TABLE.'.'.self::DOCUMENT_CREATE_DATE_TIME.' ,'.
          self::DOCUMENT_NAME_TABLE.'.'.self::DOCUMENT_UPDATE.' ,'.
          self::DOCUMENT_NAME_TABLE.'.'.self::DOCUMENT_ID.' ,'.
          self::USER_TABLE_NAME.'.'.self::USER_NAME.' ,'.
          self::USER_TABLE_NAME.'.'.self::USER_SURNAME.' ,'.
          self::TIMER_NAME_TABLE.'.'.self::USER_ID.' as editTimer ,'.
          self::TIMER_NAME_TABLE.'.'.self::TIMER_DATE_TIME_START.' ,'.
          self::TIMER_NAME_TABLE.'.'.self::TIMER_DATE_TIME_END.''

          .' from '.self::DOCUMENT_NAME_TABLE.' join '.self::USER_TABLE_NAME.' using('.self::USER_ID.') join '.self::TIMER_NAME_TABLE.
          ' using('.self::TIMER_ID.') ');




    }
    public  function removeDocument($id){
        $data=$this->db->table(self::DOCUMENT_NAME_TABLE)->where(self::DOCUMENT_ID,$id)->select(self::TIMER_ID)->fetch();
        $data=$data[self::TIMER_ID];

   $this->db->table(self::DOCUMENT_NAME_TABLE)->where(self::DOCUMENT_ID,$id)->delete();

        $this->db->table(self::TIMER_NAME_TABLE)->where(self::TIMER_ID,$data)->delete();

    }
    /*
     * return max priority from document
     */
    private function maxPriorityDocument(){
        return $this->db->table(self::DOCUMENT_NAME_TABLE)->max(self::DOCUMENT_PRIORITY);
    }

    /**
     * @param $url checked url
     * return true if url is free else return false !
     */
    public function isFreeUrl($url){
        if($this->db->table(self::DOCUMENT_NAME_TABLE)->where(self::DOCUMENT_SEO_URL,$url)->count('*')==0)
        return true;
        return false;
        
    }

    /**
     * @param $name chcecked document name
     * return true if name is free else return false
     */
    public function isFreeNameDocument($name){
        if($this->db->table(self::DOCUMENT_NAME_TABLE)->where(self::DOCUMENT_TITLE,$name)->count('*')==0)
            return true;
        return false;
    }

    /**
     * @param $seo
     * Delete document
     */
    public  function deleteDocument($seo){
        $this->db->table(self::DOCUMENT_NAME_TABLE)->where(self::DOCUMENT_SEO_URL,$seo)->delete();

    }

    public function editDocument(){

    }
}