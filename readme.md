# *Node* a Zend-Framework-Module

## What does this module do?
*Node* is a module, which has grown by the time to support more features than initially planned.
It brings a layer into your Zend-Framework application to create url-rewrites (route) for each of your contents.

The module ships with 3 types of nodes: MVC-Nodes, Content-Nodes and Redirect-Nodes.

**MVC-Nodes** can be created from the backend (requires ZfcAdmin) of the module. The configuration-form allows you
to specify the url-path this node should be available at. In dropdowns you select the controller (all controllers in
your application are retrieved from the service-manager) and the action within this controller. Optionally you can provide
further parameters which will be passed 1:1 into the router. Also optionally you can provide an original-url-path if
the specified controller, action and parameters are already assigned to a certain route. Whenever this route will be
output, e.g. in your templates, it will be replaced by the new node-based-url, which you just created.

So MVC-Nodes allow you to achieve the following: route the url http://yourdomain.com/news/ to a potential News-Controller,
to the Index-Action with the parameter *Sort=ASC*.

**Redirect-Nodes** are an additional feature, which wasn't planned initially, but can be very useful. Just like MVC-Nodes
you can create these kind of nodes within the backend by configuring it with the help of a form. You specify the url-path
for this node and the target url (can be external), where the node should redirect to. You may also specify the http-status-code
which will be used upon redirection.

So Redirect-Nodes allow you to do the following: create a redirect from http://yourdomain.com/facebook/ to https://facebook.de/your-name/

**Content-Nodes** are the initial idea of the module. They are nodes, which can't be created manually from the backend, since they always
should be related to a content (e.g. a blog-post). Whenever you create a specific content, a node should be created - whenever you delete
this content, the node should be deleted as well. Since this Node-Module doesn't know the internals of the other modules which delivers
the content, you are responsible for creating a third module, which ties the module, which allows managing a certain content, with the Node-Module.

So Content-Nodes are basically the same as MVC-Nodes. However you can't create or delete them manually as this task has to be implemented
within the code itself. The following task can be achieved with a Content-Node: when calling http://yourdomain.com/blog/why-i-am-great/
internally execute the View-Action within a BlogController with the parameter *id=5*.

## Additional features
There are some other features, which can be dis-/enabled in the config-file.

* **Enable meta-fields**. If enabled, the add/edit-forms in the node backend allow you to specify a meta-description, meta-keywords and robots-settings
	for your seo-concerns.
* **Enable access-counter**. If enabled, whenever a node-route gets called a counter for this node is incremented. Also the timestamp of the last access
    of this node is being saved. These information is presented within the node-overview in the backend.
* **Caching**. The nodes are stored within a database. The first time you call the page, all nodes are fetched, processed and put into the router of your
    application. While doing that, the router-config is cached, so in the next request it's not necessary to fetch all nodes again.
* **Translatable**. All strings within the module are wrapped with the `translate()`-method from the translator-instance.
* **Rich usage of events**. This module triggers events at certain points, which gives you the chance of extending nodes with additional fields for example.
    Think of nodes as a central place to store information, that is relevant for all your content. Maybe you want to have a different header-image for each
    of your nodes - no problem. Through the triggered events you can easily modify the backend-forms or hook into the process of saving the nodes into the
    database.
    
## Future plans
* This module is currently written for ZF2, however I want to make it compatible with ZF3. Therefore I will probably create another branch.
* The composer.json of this module doesn't really list the correct requirements. If you install the whole ZF2,  everything should be fine. However in fact you only
  need certain components.
* Maybe I will put some efforts in translating this module into german. Now it's english, however you can easily call your own translations, if available.