<?php


namespace App\Constraint;


use App\Manager\ChapterManager;

class ChapterConstraint
{
    protected $chapterManager;

    function __construct()
    {
        $this->chapterManager = new ChapterManager();
    }

    public function isAnotherChapterTitle($title)
    {
        if(!$this->chapterManager->istOtherTitleChapter($title)){
            return true;
        }
        return false;
    }

}