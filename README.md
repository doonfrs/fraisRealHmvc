fraisRealHmvc
=============

Yii Framework Widget more independence

###IDEA:
in YII framework you cannot use widgets as action provider independently, you need to add an action in controller with dispatch the request to this widget, simply you cannot access the widget directly, this means that you may use the widget as a shared view( with some functionality ) or if you put action in this widget you need to modify each controller to make a route between this controller and the widget action…

###Why? 
To enhance YII framework and make “widgets as action provider” more independent.

###More details:
It is really confusing in php before YII how can I call a widget and get result?
One of these solutions b using persistence objects and this is not really recommended and has many disadvantages…
To move forward…
Then let say, I have widget for CRUD management, and I need to render many instances in the same view, each instance has different settings (different model), how can I make a call for specific one of them?

One of these widgets want to call the add/edit form with its settings (the same model), how will this happen?
When widget gets rendered, they not get memorized. So I can call the main widget which does not have settings, or I need to pass the settings another time in the request. And this is not really what we want… 
Let’s take the models, what are models? They are group of instances of a model class with different settings. You set settings for this model instance and save it on the hard disk…
And you create instance of ready model with its settings.
Now we can use this approach in widgets… create new instance of widget in a file , set the settings, render the created instance without  passing the settings, then yes… you can call the widget without passing the whole parameters somehow … 
http://localhost/ index.php?wg=mywg&id=10&cmd=update
But still not helpful, I want to create 100 widget with different settings, and I may need to changes widget settings dynamically, model cannot be compared to widgets here.

###So what do I want?
1)	You create the widget as you used to do. 
2)	Widgets get cached in a file by YII and get a key based on its settings.
3)	You trail all your actions with the widget key 
4)	YII detect if this is a call for controller action of it is trailed by a key (ends with dot and a text)
5)	YII bring the widget from widget cache
6)	YII call widget action.


###Current YII Approach:





###New Approach:





 
###Where will you save the widgets cache?
Here : protected/runtime/widgetscache/

###How do you cache the widget?
1)	Your controller should extend the fraisController class
2)	I overrided the controller widget function.
3)	I serialize the widget (not all properties get serialized , I clone , clean-up then serialize)
4)	crc32 the serialized data to make a key
5)	Save, check before if key already exists then skip saving.

###What are the changes in my widget class? 
1)	Extends the fraidWidget
2)	All widget internal call should passed to function getWidgetActionURL
Sample in widget view:
$this->createAbsoluteUrl($this->getWidgetActionURL(path.to.my.widget.action))
$this->createAbsoluteUrl($this->getWidgetActionURL(‘ext.fraiscrud.widgets.fraiscrud.update’))

3)	You can access you widget in the action using $widget = $this->getOwner(); , then getOwner will return the widget if this action called directly.



###STEP BY STEP:
1)	Import ext. fraisRealHmvc. Components.*
2)	Make your controller extends fraisController
3)	Create your widget class and remember to extend fraisWidget
4)	Create your action that extends fraisAction
5)	Add your action to widget action array
6)	When you want to call the action in your widget view remember to call it like this
$this-> getWidgetActionURL(‘ext.mywidget.mywidgetclass.myaction’)
7)	In your action you can access the parent widget using $this->getOwner()
8)	render the widget as usual $this->widget(‘mywidget’,array(settings));
9)	now you can render your widget many time with different settings , no need to make different routes in your controller actions, no need to pass settings every time , just in the action use $this->getOwner() and you will get the owner widget instance of your action with its all properties.



###Any full example?
Yes, please check my fraisCRUD fully completed CRUD management based on YiiRealHMVC





###Quick Notes:
•	It is recommended to use lower case for widget class name, especially if you set 'urlManager'=>array('caseSensitive'=>false) in config , fraisController will try to import the class and fail if your class contains upper cases.

•	If you defined a property refer to noncore yii class , like extension widget
Then it is not enough to import the class in controller or view before widget, you should import it globally via config or view init module (if you are using module) 
Example: 

Yii::import(‘ext.MyClass.MyClass’);
$this->widget(‘ext.mywidget.mywidget’,’prop’=>new MyClass);

Import here will work the first time you create the widget, but when action try to access the widget next time , it will fail, because the next time fraisController will get the widget from widget cache (unserialize) not to recall the controller again … 

So you need to define the import in config , or in init module function


•	Action cannot change widget properties, 
fraisRealHMVC is not an object persistence solution , it will just save widget properties that defined when creating widget using controller->widget or controller->begin/endwidget functions.
•	Due to fraisRealHMVC is not an object persistence solution , it is mechanism allow user to open the page many tabs without fearing of unexpected behavior , simply fraisRealHMVC will save the widget by key depending on its properties (md5(serializez($widget)) , and all requests later will contains widget key as trailer to help fraisController finding the widget file settings.

•	fraisRealHMVC does not save widget in user session scope , it saved in application scope, again … widget key is the widget properties which will not be changed any time.
•	Widget with same properties will be written to cache file one time… the next time will be read only, if there is any changes to widget properties values or widget class detention , this means that there is a new key , new file created , old file will be temp  (thinking for something like garbage collector)
•	
•	I don’t think that there is a performance issue, we may use memory also to save widget cache, but hard disk is also good solution, remember that session any many solutions depends on hard drive read/write, also app will not load all objects on each requests (like when you save objects in session, they will be loaded all one time on the first request) … fraisController will load widget properties upon it requested by detecting its hash id in the request trailer.

•	What about security??? still thinking … 

•	If your application creates a thousand of widgets with different properties, and each widget need to work independent on any controller … fraisRealHMVC is good but you may face a performance issue… remember 1000 controller created on one page means that there is 1000 files written (only if they have different properties) but if any widget later tried to access it’s actions … just this widget will be loaded not the 1000.

•	widget serialization + writing to cache = 0.0019998550415039 sec  = 1.9 ms

•	widget key generation (md5(serialize))  = 0ms … 1000 widget key generation = 0.064003944396973 sec = 64 ms

•	widget read/unserialize = 0.0039999485015869 sec = 3.9 ms

•	this means your request will be delayed  when loading page first time about 1.9 ms , next time will be delayed only serialization = 0 ms  , for 100 widget about 6ms.

•	When requesting an action and reading the widget request will delayed about 4ms

•	All tests was for Ajax state , I don’t think regular action call will differ , just in using Ajax calls it is very recommended to use NLSClientScript 


