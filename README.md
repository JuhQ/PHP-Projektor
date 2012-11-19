This is a simple PHP project starter kit which I use as a base for new projects.
It's not complete, nor it will not be complete. Please feel free to fork.

This project follows a simple naming syntax, which allows for quick code iteration and development.

You can start playing around with this by looking at the view folder.
View/pages contain the pages that are visible to the outside world, view/classes are ui classes for those pages and view/templates contain templates for the pages.

New page can be created as easily as adding example.php to view/pages with code:
```php
<?php
class example extends uiModule {}
```

uiModule will automatically call uiExample.php from view/classes folder, which will contain your basic view code.

uiExample.php example code:

```php
<?php
class uiExample {
    public function view() {
        echo 'Hello world';
    }
}
```

uiExample.php will call example.html from view/templates folder.


After this it's just matter of navigating to example.com/example and you should see your content ;)



Utilizes other open source projects, like jQuery, Bootstrap and MongoHelper.
Requires PHP 5.3

Favicon from http://www.iconfinder.com/icondetails/63112/128/cupcake_rainbow_icon

2012
Juha Tauriainen juha@bin.fi