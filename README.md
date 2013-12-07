fraisRealHmvc
=============

Yii Framework Widget more independence


Important notice : 
•	It is recommended to use lower case for widget class name, especially if you set 'urlManager'=>array('caseSensitive'=>false) in config , fraisController will try to import the class and fail if your class contains upper cases.

•	If you defined a property refer to noncore yii class , like extension widget
Then it is not enough to import the class in controller or view before widget, you should import it globally via config or view init module (if you are using module) 
Example : 

Yii::import(‘ext.MyClass.MyClass’);
$this->widget(‘ext.mywidget.mywidget’,’prop’=>new MyClass);

Import here will work the first time you create the widget, but when action try to access the widget next time , it will fail, because the next time fraisController will get the widget from widget cache (unserialize) not to recall the controller again … 

So you need to define the import in config , or in init module function


•	Action can not change widget properties ,
widget in YII concept is object that rendered as HTML … not a server side object , so fraisRealHMVC is not an object persistence solution , it will just save widget properties that defined when creating widget using controller->widget or controller->begin/endwidget functions.
•	Due to fraisRealHMVC is not an object persistence solution , it is mechanism allow user to open the page many tabs without fearing of unexpected behavior , simply fraisRealHMVC will save the widget by key depending on its properties (md5(serializez($widget)) , and all requests later will contains widget key as trailer to help fraisController finding the widget file settings.

•	fraisRealHMVC does not save widget in user session scope , it saved in application scope, again … widget key is the widget properties which will not be changed any time.
•	Widget with same properties will be written to cache file one time… the next time will be read only, if there is any changes to widget properties values or widget class defention , this means that there is a new key , new file created , old file will be temp  (thinking for something like garpage collector)
•	
•	I don’t think that there is a performance issue , if you use it , we may use memory also to save widget cache , but hard disk is also good solution, remember that session any many solutions debend on save/read data, also app will not load all objects on each requests (like when you save objects in session , they will be loaded all one time on the first request) … fraisController will load widget properties upon it requested by detecting its hash id in the request trailer.

•	What about security ??? still thinking … 

•	If your application creates a thousand of widgets with defferent properties , and each widget need to work independent on any controller … fraisRealHMVC is good but you may face a performance issue… remember 1000 controller created on one page means that there is 1000 files written (only if they have defferent properties) but if any widget later tried to access it’s actions … just this widget will be loaded not the 1000.

•	widget serialization + writing to cache = 0.0019998550415039 sec  = 1.9 ms

•	widget key generation (md5(serialize))  = 0ms … 1000 widget key generation = 0.064003944396973 sec = 64 ms

•	widget read/unserialize = 0.0039999485015869 sec = 3.9 ms

•	this means your request will be delayed  when loading page first time about 1.9 ms , next time will be delayed only serialization = 0 ms  , for 100 widget about 6ms.

•	When requesting an action and reading the widget request will delayed about 4ms

•	All tests was for ajax state , I don’t think regular action call will deffer , just in using ajax calls it is very recomeneded to use NLSClientScript 



