Simple-PHP-Paypal-API
=====================

Simple PHP Paypal Interaction Class

This was written for doing simple api calls to Paypal as there were so many complicated solutions out there.  The usage in this is very basic; however hopefully it will help someone else just get things working. 

Just draw the owl.

##Usage
```php
$paypal = new Paypal();
$response = $paypal->request('YOUR OPERATION', $ArrayToBeAddedToRequest);
```;