<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class DisplayProductManufacturer extends Module
{
    /**
     * @var array list of hooks used
     */
    public $hooks = [
        'actionAdminControllerSetMedia',
        'actionAdminProductsListingFieldsModifier',
        'actionAdminProductsListingResultsModifier',
    ];

    /**
     * Name of ModuleAdminController used for configuration
     */
    const MODULE_ADMIN_CONTROLLER = 'AdminDisplayProductManufacturer';

    /**
     * Configuration key used to store toggle for display logo
     */
    const CONFIGURATION_KEY_SHOW_LOGO = 'DISPLAYPRODUCTMANUFACTURER_SHOW_LOGO';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'displayproductmanufacturer';
        $this->tab = 'administration';
        $this->version = '2.0.0';
        $this->author = 'Matt75';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '1.7.7.99', // Because product list should be rebuild
        ];

        parent::__construct();

        $this->displayName = $this->l('Display Manufacturer on Product list');
        $this->description = $this->l('Adds Manufacturer on Product list');
    }

    /**
     * Install Module.
     *
     * @return bool
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook($this->hooks)
            && $this->installTabs()
            && Configuration::updateValue(static::CONFIGURATION_KEY_SHOW_LOGO, false);
    }

    /**
     * Install Tabs
     *
     * @return bool
     */
    public function installTabs()
    {
        if (Tab::getIdFromClassName(static::MODULE_ADMIN_CONTROLLER)) {
            return true;
        }

        $tab = new Tab();
        $tab->class_name = static::MODULE_ADMIN_CONTROLLER;
        $tab->module = $this->name;
        $tab->active = true;
        $tab->id_parent = -1;
        $tab->name = array_fill_keys(
            Language::getIDs(false),
            $this->displayName
        );

        return $tab->add();
    }

    /**
     * Uninstall Module
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallTabs()
            && Configuration::deleteByName(static::CONFIGURATION_KEY_SHOW_LOGO);
    }

    public function uninstallTabs()
    {
        $id_tab = (int) Tab::getIdFromClassName(static::MODULE_ADMIN_CONTROLLER);

        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }

        return true;
    }

    /**
     * Redirect to our ModuleAdminController when click on Configure button
     */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink(static::MODULE_ADMIN_CONTROLLER));
    }

    /**
     * Add CSS to fix Manufacturer logo size in Product List page
     */
    public function hookActionAdminControllerSetMedia()
    {
        if ('AdminProducts' === Tools::getValue('controller')
            && false === Tools::getIsset('addproduct')
            && Configuration::get(static::CONFIGURATION_KEY_SHOW_LOGO)
        ) {
            $this->context->controller->addCSS($this->getPathUri() . 'views/css/displayproductmanufacturer.css', 'all');
        }
    }

    /**
     * Manage the list of product fields available in the Product Catalog page.
     *
     * @param array $params
     */
    public function hookActionAdminProductsListingFieldsModifier(array $params)
    {
        $params['sql_select']['id_manufacturer'] = [
            'table' => 'p',
            'field' => 'id_manufacturer',
            'filtering' => ' %s ',
        ];

        $params['sql_select']['manufacturer_name'] = [
            'table' => 'man',
            'field' => 'name',
            'filtering' => 'LIKE \'%%%s%%\'',
        ];

        $params['sql_table']['man'] = [
            'table' => 'manufacturer',
            'join' => 'LEFT JOIN',
            'on' => 'p.`id_manufacturer` = man.`id_manufacturer`',
        ];
    }

    /**
     * Manage the list of products available in the Product Catalog page.
     *
     * @param array $params
     */
    public function hookActionAdminProductsListingResultsModifier(array $params)
    {
        if (isset($params['products'])
            && Configuration::get(static::CONFIGURATION_KEY_SHOW_LOGO)
        ) {
            foreach ($params['products'] as $key => $product) {
                if ($product['id_manufacturer']) {
                    $params['products'][$key]['manufacturer_image'] = $this->context->link->getMediaLink(_THEME_MANU_DIR_ . $product['id_manufacturer'] . '.jpg');
                }
            }
        }
    }
}
