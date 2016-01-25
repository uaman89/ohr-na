Для поключения комментариев нужно подключить css файл comments.css /include/css/comments.css

Так же нужно подключить ява скрипты в даной последовательности
/include/js/cms_lib/popup.js
    /include/js/cms_lib/comments.js

для использования достаточно создать обект класса и передать в конструктор айди модуля и итема.

      $this->Comments = new CommentsLayout($this->module, $this->recordId);
       $this->Comments->ShowComments();

