<?php

/**
 * Description of ckeditor_plugin_ckeditorPlugin
 *
 * @author Gerd Ratsch
 */

class ckeditor_plugin_ckeditorPlugin {
    public $contentList = array();
    public function __construct() {
        $this->startString = '<strong>itemB</strong>.';
        $this->endString = '<strong>itemE</strong>.';
        $this->grouprelationname = array();
        $this->label = 'Default';
        $this->command = '';
        $this->icon = '';
        $this->toolbar = 'default';
        $this->validationcheck = FALSE;
    }
    public function validatePluginAction($node){
        $body = $this->getBodyContent($node);
        $this->getInfoDeclaredClasses();
        $contentList = $this;
        foreach ($this->contentList as $className => $classObject) {
            $classObject->checkPosition($body,$contentList); 
            $this->validatePlugin($contentList,$node);
        }
    }
    public function executePluginAction($node){
        $body = $this->getBodyContent($node);
        if($body !== ''){
            $this->getInfoDeclaredClasses();
            $contentList = $this;
            foreach ($this->contentList as $className => $classObject) {
                $list = $classObject->buildPositionList($body);
                $classObject->createContentList($list,$body);
                $classObject->getContent($body );
            }
            foreach ($this->contentList as $className => $classObject) {
                $classObject->pluginAction($contentList,$node);
            }
            foreach ($this->contentList as $className => $classObject) {
                $classObject->stringReplace($body);
            }
        }
        $this->replaceBodyContent($node, $body);
        return $node;
    }
    public function getBodyContent(&$node){
        $languages = key($node->body);
        $body='';
        if($languages !== ''){
                $body = $node->body[$languages][0]['value'];
        }
        return $body;
    }
    public function replaceBodyContent(&$node, $body){
        $languages = (key($node->body))? key($node->body) : 'und';
        $node->body[$languages]['0']['value']=$body;
    }

    public function getInfoDeclaredClasses(){
        $classTotalList  = get_declared_classes ();
        foreach ($classTotalList as $key => $value) {
            if(get_parent_class($value) == 'ckeditor_plugin_ckeditorPlugin' ){
                $this->contentList[$value] = new $value;
            }
        }
    }
    public function checkPosition(&$body, &$contentList){
        $searchText = $this->startString;
        if(strpos($body, $searchText)>0){
            $this->validationcheck = TRUE;
            if(count($this->grouprelationname) > 0 ){
                $this->checkGroupRelations($body, $contentList);
            }
        }
        
    }
    public function checkGroupRelations(&$body, &$contentList){
        $checkcodeList='';
        foreach ($this->grouprelationname as $keyRelation => $classnameRelations) {
        $objectClass = $contentList->contentList[$classnameRelations];
            if($objectClass->validationcheck == FALSE){
                $objectClass->checkPosition($body,$contentList);
                if($objectClass->validationcheck == FALSE){$checkcodeList.=' "'.$classnameRelations.'" ';}
            }
        }
        $objectClass='';
        if($checkcodeList != ''){
            $message='For the code '.$this->command.' we also need '.$checkcodeList.' in the body to compleet the action.';
            form_set_error($name = 'check '.$this->command.' is added.',$message , $limit_validation_errors = NULL);
        }
    }
    public function buildPositionList(&$body){
        $startBlock = $this->startString;
        $endBlock = $this->endString;
        $list = array();
        if($startBlock !=''){$this->getPosition($body,$startBlock,$list);}
        if($endBlock !=''){$this->getPosition($body,$endBlock,$list);}
        ksort($list);

        return $list;

    }
    
    public function getPosition(&$body,$string,&$list){
        $startPos = strpos($body, $string);
        while($startPos > -1){
            $list[$startPos]=$string;
            $startPos = strpos($body, $string,$startPos+1);
        }
    }
    public function createContentList($list, $body){
        $lastAddedString='';
        $row=0;
        foreach ($list as $key => $value) {
            if($lastAddedString != $value){
                $lastAddedString= $value;
            }else{
                $message='It is not allowed to select a '.$this->label.' in a '.$this->label.'.';
                form_set_error($name = 'check '.$this->label.' selection.',$message , $limit_validation_errors = NULL);
            }
            if($value == $this->startString){
                $this->contentList[$row]['startString'] = $value;
                $this->contentList[$row]['startpos'] = $key;
            }
            if($value == $this->endString){
                $this->contentList[$row]['endString'] = $value;
                $this->contentList[$row]['endpos'] = $key;
                $row += 1;
            }
        }
        
    }
    public function getContent(&$body ){
        foreach ($this->contentList as $rowkey => $itemInfo) {
            if(array_key_exists('startpos', $itemInfo)){
                $startString = $itemInfo['startString'];
                $startPosition = $itemInfo['startpos'] + strlen($startString);
                $length = $itemInfo['endpos'] - $startPosition;
                $content = substr($body, $startPosition , $length);
                $this->contentList[$rowkey]['content'] = $content; 
                $this->validationcheck = TRUE;
            }
        }
        
    }
    public function stringReplace(&$body){
        $ObjectContent = $this->contentList;
        foreach ($ObjectContent as $keyContent => $itemContent) {
            $content = (array_key_exists('content', $itemContent)) ? $itemContent['content'] : '';
            $start = (array_key_exists('startString', $itemContent)) ? $itemContent['startString'] : '';
            $end = (array_key_exists('endString', $itemContent)) ? $itemContent['endString'] : '';
            $searchText = $start.$content.$end;
            $replace = (array_key_exists('replace', $itemContent)) ? $itemContent['replace']: '';
            $body = str_replace($searchText, $replace, $body);
        }        
    }
    public function validatePlugin(&$contentList,&$node){
        
    }
    public function pluginAction(&$contentList,&$node){
        
    }
}

