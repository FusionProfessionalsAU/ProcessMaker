<?php

namespace ProcessMaker\Core;


class RoutingScreen extends \Derivation
{
    protected $convergent;
    protected $divergent;
    public $gateway = array('PARALLEL', 'PARALLEL-BY-EVALUATION');
    public $isFirst;
    protected $taskSecJoin;

    public function __construct()
    {
        parent::__construct();
        $this->setRegexpTaskTypeToInclude("GATEWAYTOGATEWAY|END-MESSAGE-EVENT|END-EMAIL-EVENT|INTERMEDIATE-CATCH-TIMER-EVENT|INTERMEDIATE-THROW-MESSAGE-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT");
    }

    public function mergeDataDerivation($post, $prepareInformation)
    {
        $aDataMerged = array();
        $flagJumpTask = false;
        foreach ($prepareInformation as $key => $nextTask) {
            $aDataMerged[$key] = $nextTask['NEXT_TASK'];
            unset($aDataMerged[$key]['USER_ASSIGNED']);
            $aDataMerged[$key]['DEL_PRIORITY'] = '';
            foreach ($post as $i => $item) {
                if(isset($post[$i]['SOURCE_UID']) && ($nextTask['NEXT_TASK']['TAS_UID'] === $post[$i]['SOURCE_UID'])){
                    $flagJumpTask = true;
                    if($post[$i]['SOURCE_UID'] === $post[$i]['TAS_UID']){
                        if (isset($post[$i]['USR_UID'])) { // Multiple instances task don't send this key
                            $aDataMerged[$key]['USR_UID'] = $post[$i]['USR_UID'];
                        }
                    } else {
                        $aDataMerged[$key]['NEXT_ROUTING'][] = $post[$i];
                    }
                }
            }
        }
        //If flagJumpTask is false the template does not Jump Intermediate Events
        if(!$flagJumpTask){
            $aDataMerged = $post;
        }
        return $aDataMerged;
    }

    public function prepareRoutingScreen($arrayData)
    {
        $information = $this->prepareInformation($arrayData);
        $response = array();
        $this->taskSecJoin = array();
        foreach ($information as $index => $element) {
            $this->divergent = array();
            $this->convergent = array();
            $this->isFirst = true;
            $x = $this->checkElement($this->node[$element['TAS_UID']]);
            if ($x) {
                $save = false;
                foreach ($response as $task) {
                    if (!in_array($element['ROU_NEXT_TASK'], $task, true)) {
                        $save = true;
                    }
                }
                if ((!$response || $save)) {
                    $response[] = $element;
                }
            }
        }
        if (count($response) > 1) {
            foreach ($response as $index => $task) {
                $delete = false;
                foreach ($this->taskSecJoin as $tj => $type) {
                    if (in_array($tj, $task, true)) {
                        $delete = true;
                    }
                }
                if ($delete) {
                    unset($response[$index]);
                }
            }
        }
        return array_combine(range(1, count($response)), array_values($response));
    }

    public function checkElement($element)
    {
        $outElement = $element['out'];
        foreach ($outElement as $indexO => $outE) {
            if (!$this->isFirst && in_array($outE, $this->gateway)) {
                $this->divergent[$indexO] = $outE;
            }
            if ($outE == 'SEC-JOIN' && strpos($indexO, 'itee') === false) {
                $this->taskSecJoin[$indexO] = $outE;
            }
        }
        if (empty($element['in'])) {
            return true;
        }
        $this->isFirst = false;
        $inElement = $element['in'];
        foreach ($inElement as $indexI => $inE) {
            if ($inE == 'SEC-JOIN' && strpos($indexI, 'itee') !== false) {
                $this->convergent[$indexI] = $inE;
            }
            $this->checkElement($this->node[$indexI]);
        }
        return count($this->convergent) == 0 || count($this->divergent) == 0 || count($this->convergent) == count($this->divergent);
    }

}