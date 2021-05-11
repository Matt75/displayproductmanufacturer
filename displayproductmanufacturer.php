<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
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
     * @var bool
     */
    private $isPrestaShop16;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'displayproductmanufacturer';
        $this->tab = 'administration';
        $this->version = '3.0.0';
        $this->author = 'Matt75';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6.1.0',
            'max' => '1.7.9.99',
        ];
        $this->isPrestaShop16 = (bool) version_compare(_PS_VERSION_, '1.7.0.0', '<=');

        parent::__construct();

        $this->displayName = $this->l('Display Manufacturer on Product list');
        $this->description = $this->l('Adds Manufacturer column on Product list');
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
     * Append custom fields.
     *
     * @param array $params
     */
    public function hookActionAdminProductsListingFieldsModifier(array $params)
    {
        if ($this->isPrestaShop16) {
            // If hook is called in AdminController::processFilter() we have to check existence
            if (isset($params['select'])) {
                $params['select'] .= ', a.id_manufacturer, man.name AS manufacturer_name';
            }

            // If hook is called in AdminController::processFilter() we have to check existence
            if (isset($params['join'])) {
                $params['join'] .= 'LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer AS man ON (a.id_manufacturer = man.id_manufacturer)';
            }

            $params['fields']['manufacturer_name'] = [
                'title' => $this->l('Manufacturer'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'man!name',
                'order_key' => 'man!name',
            ];

            if (Configuration::get(static::CONFIGURATION_KEY_SHOW_LOGO)) {
                $params['fields']['manufacturer_name']['icon'] = true;
                $params['fields']['manufacturer_name']['class'] .= ' column-img-manufacturer';
            }
        } else {
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

            // There no proper way currently to add custom filters
            // This tricks doesn't manage pagination and empty results
            $manufacturer_filter = Tools::getValue('filter_column_name_manufacturer');
            if (!empty($manufacturer_filter) && Validate::isCatalogName($manufacturer_filter)) {
                $params['sql_where'][] .= sprintf('man.name LIKE "%%%s%%"', pSQL($manufacturer_filter));
            }
        }
    }

    /**
     * Set additional data.
     *
     * @param array $params
     */
    public function hookActionAdminProductsListingResultsModifier(array $params)
    {
        if (!Configuration::get(static::CONFIGURATION_KEY_SHOW_LOGO)) {
            return;
        }

        if ($this->isPrestaShop16 && !empty($params['list'])) {
            foreach ($params['list'] as $key => $fields) {
                if (isset($fields['id_manufacturer'], $fields['manufacturer_name'])) {
                    $params['list'][$key]['manufacturer_name'] = [
                        'src' => '../m/' . (int) $fields['id_manufacturer'] . '.jpg',
                        'alt' => Tools::safeOutput($fields['manufacturer_name']),
                    ];
                }
            }
        } elseif (!$this->isPrestaShop16 && isset($params['products'])) {
            foreach ($params['products'] as $key => $product) {
                if ($product['id_manufacturer']) {
                    $params['products'][$key]['manufacturer_image'] = $this->context->link->getMediaLink(_THEME_MANU_DIR_ . $product['id_manufacturer'] . '.jpg');
                }
            }
        }
    }
}
