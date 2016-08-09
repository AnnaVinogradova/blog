Symfony Blog
========================


Installation
--------------

  * Install dependencies;

  * $ php app/console doctrine:database:create

  * $ php app/console doctrine:migrations:migrate

  * $ php app/console doctrine:fixtures:load

  * $ php app/console assets:install web

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