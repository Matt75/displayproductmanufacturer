# Display product manufacturer

This is a demo module but can be used safely on your PrestaShop instance

## About

Adds manufacturer column on product list

## Known issue on PrestaShop 1.7
There no proper way to add custom filters currently.
I made a trick to make the filter field works, but it doesn't support the pagination and doesn't manage empty results.
This issue will be fixed when the AdminProductController will be refactored to use the Grid component likes others Symfony based controllers.

Upgrade to PrestaShop 8 to fix this issue.

## Screenshots

### PrestaShop 8

![Product list with Manufacturer name](https://github.com/Matt75/displayproductmanufacturer/assets/5262628/5ede2a46-65fa-478b-a79d-848ac8dff2ce)
![Module configuration](https://github.com/Matt75/displayproductmanufacturer/assets/5262628/be1c3ed1-49b1-4de6-b1d9-5c36e32abaf6)
![Product list with Manufacturer logo](https://github.com/Matt75/displayproductmanufacturer/assets/5262628/bb693ede-136c-4c2d-a290-23406fac0bef)


### PrestaShop 1.7

![Product list with Manufacturer name](https://user-images.githubusercontent.com/5262628/58573778-ba7f5100-823e-11e9-9d85-6f08784a1c2a.png)
![Module configuration](https://user-images.githubusercontent.com/5262628/58557976-f22ad080-821f-11e9-9525-cdc35aa230ca.png)
![Product list with Manufacturer logo](https://user-images.githubusercontent.com/5262628/58573774-b81cf700-823e-11e9-81b1-f9cab0f887b4.png)

### PrestaShop 1.6

![Product list with Manufacturer name](https://user-images.githubusercontent.com/5262628/58557959-e9d29580-821f-11e9-89d5-ffa7ffd30d5a.png)
![Module configuration](https://user-images.githubusercontent.com/5262628/58557976-f22ad080-821f-11e9-9525-cdc35aa230ca.png)
![Product list with Manufacturer logo](https://user-images.githubusercontent.com/5262628/58557965-ee974980-821f-11e9-9ee9-a172c79d04a1.png)

## Reporting issues

You can report issues with this module in this repository. [Click here to report an issue][report-issue]. 

## Contributing

PrestaShop modules are open source extensions to the PrestaShop e-commerce solution. Everyone is welcome and even encouraged to contribute with their own improvements.

### Requirements

Contributors **must** follow the following rules:

* **Make your Pull Request on the "dev" branch**, NOT the "master" branch.
* Do not update the module's version number.
* Follow [the coding standards][1].

### Process in details

Contributors wishing to edit a module's files should follow the following process:

1. Create your GitHub account, if you do not have one already.
2. Fork this project to your GitHub account.
3. Clone your fork to your local machine in the ```/modules``` directory of your PrestaShop installation.
4. Create a branch in your local clone of the module for your changes.
5. Change the files in your branch. Be sure to follow the [coding standards][1]!
6. Push your changed branch to your fork in your GitHub account.
7. Create a pull request for your changes **on the _'dev'_ branch** of the module's project. Be sure to follow the [contribution guidelines][2] in your pull request. If you need help to make a pull request, read the [GitHub help page about creating pull requests][3].
8. Wait for one of the core developers either to include your change in the codebase, or to comment on possible improvements you should make to your code.

That's it: you have contributed to this open source project! Congratulations!

## License

This module is released under the [Academic Free License 3.0][AFL-3.0] 

[report-issue]: https://github.com/Matt75/displayproductmanufacturer/issues/new
[1]: https://devdocs.prestashop.com/1.7/development/coding-standards/
[2]: https://devdocs.prestashop.com/1.7/contribute/contribution-guidelines/
[3]: https://help.github.com/articles/using-pull-requests
[AFL-3.0]: https://opensource.org/licenses/AFL-3.0
