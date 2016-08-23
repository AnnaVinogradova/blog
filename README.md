Symfony Blog
========================


Installation
--------------

  * Install dependencies;

  * $ php app/console doctrine:database:create

  * $ php app/console doctrine:migrations:migrate

  * $ php app/console doctrine:fixtures:load

  * $ php app/console assets:install web

  * $ php app/console fos:elastica:populate

  * $ php app/console gos:websocket:server

Credentials
-------------
  
 + admin
 
        login:admin
        password:123456

 + user
 
        login:username
        password:password

 for more information open About Page

Tasks
-------------

##1

* 2 roles (user \ admin)
* login
* register
* admin part
	* CRUD posts
	* CRUD users
* user part
	* CRUD posts

Admin can everything, user can edit only posts.
Everything does under GIT CVS.
For login/register part can use any Bundles (using composer)

##2

* реализовать todo список (crud) для каждого пользователя (пользователь может делать все с элементами списка)
* добавить новую роль "решатель", который может управлять todo списком пользователя, к которому он привязан (может быть привязан к нескольким пользователям), но не может редактировать посты юзера
* админ может менять все у всех, привязывать решателя к юзеру
* пользователь может создать request на добавление решателя,
* админ может просматривать requestы от юзеров и подтверждать\отколенять предложение между юзером и решателем

##3

* Write 2-3 unit-tests and functional tests
* Write functional to add marker (lat, lng) on google maps:
	* user or his resolver can add marker (tag) on google maps with some text (window popup)
	* user and his resovler can see all added markers on map in menu after login

##4

* Every user has own wall, which contains:
  * all posts tab
  * own posts tab

* Users can add posts on every wall (Post can contain one image)

* Owner can delete posts from his wall

##5

* Add friends functional:
  * user can send request to other user to add friend
  * user can approve/decline friend-invitation
  * only friends can see and write on wall of user

##6

* add text editor for posts of user (something like this http://ckeditor.com)
* keep whole structure of post (styles, fonts, etc)

##7

* create game "bulls and cows" for two logged users (friends only), rules of game here: (https://ru.wikipedia.org/wiki/%D0%91%D1%8B%D0%BA%D0%B8_%D0%B8_%D0%BA%D0%BE%D1%80%D0%BE%D0%B2%D1%8B)
* user can invite (request) friend to play to game
* friend can approve\decline request to play
* when friend approve request to play he can visit link with game (user can visit for game too)
* game can be created, started and finished

##8

* create search for users (nickname) using elasticsearch