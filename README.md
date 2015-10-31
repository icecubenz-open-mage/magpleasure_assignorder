# Magpleasure_Assignorder

Assign Order to Customer - is a tool that allows you to assign any order to any customer.

# Features

* Ability to assign guest order to any registered customer
* Ability to assign any order from one customer to another
* Ability to assign multiple orders to customer using mass action
* Ability to view a history of assigned guest orders
* Ability to rollback the assignment to any history point
* Order Assignment email notification to customer and administrator


## Differences from upstream

* The Adminhtml router, layout and controllers modified to work with [SUPEE-6788](http://magento.com/security/patches/supee-6788-technical-details).

* `Magpleasure_Common` and `Magpleasure_Info` _helper_ modules has been removed. Required code has been cherry-picked and introduced directly into this module.  
These were removed as the introduce a lot of code and several libraries that were unused, they also needed patching for SUPEE-6788 - outside of the context of support from Magpleasure I have deemed it more efficient to remove these rather than update them.


## Credits, license and copyright

This extension was originally developed by Magpleasure.

In July 2015 they stopped development and released this extension, and many others, free of charge. http://www.magpleasure.com/blog/super-freebie-download-the-3k-package-of-magento-extensions-and-themes-for-free.html

> Conditions of using a product from the package
>
> After you download the package, you’ll be able to use all the extensions and themes in it without any time limit. It means that you DON’T have to prolong the Support & Update License after the 6-month term of using a product.
>
> We also give you the freedom to fix bugs, add features, and change the code as you want.
>
> But keep in mind that we will not update the extensions and themes anymore and will not provide support for it. This sale is just a farewell gift to all our customers before closing the modules.


## Community or Enterprise Edition?

Prior to forking the code I inspected the code of both the CE and EE versions of this extension from upstream and there are no differences other than the version specified in the license - as such this fork should work with both versions (PRs welcome if not!)
