<?php

class Application_View_Helper_NarrowSearch extends Zend_View_Helper_Abstract
{

    public function bugList()
    {
        $model = new Bug();
        $html  = "<ul>\n";
        foreach ($model->fetchActive() as $bug) {
            $html .= sprintf(
                "<li><b>%s</b>: %s</li>\n",
                $this->view->escape($bug->id),
                $this->view->escape($bug->summary)
            );
        }
        $html .= "</ul>\n";
        return $html;
    }
}