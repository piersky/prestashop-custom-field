<?php
/**
 * After install clean prestashop cache
 */

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use Symfony\Component\Form\Extension\Core\Type\TextType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ps_customerfields extends Module
{
    public function __construct()
    {
        $this->name = 'ps_customerfields';
        $this->version = '1.0.0';
        $this->author = 'Pier Luigi Papeschi';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->getTranslator()->trans(
            'Customer Fields',
            [],
            'Modules.ps_customerfields.Admin'
        );

        $this->description =
            $this->getTranslator()->trans(
                'Customer Fields',
                [],
                'Modules.ps_customerfields.Admin'
            );

        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];
    }
    /**
     * This function is required in order to make module compatible with new translation system.
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * Install module and register hooks to allow grid modification.
     *
     * @see https://devdocs.prestashop.com/1.7/modules/concepts/hooks/use-hooks-on-modern-pages/
     *
     * @return bool
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('additionalCustomerFormFields') &&
            $this->registerHook('actionCustomerFormBuilderModifier') &&
            $this->registerHook('actionAfterCreateCustomerFormHandler') &&
            $this->registerHook('actionAfterUpdateCustomerFormHandler') &&
            $this->alterCustomerTable()
        ;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallAlterCustomerTable();
    }

    /**
     * Alter customer table, add module fields
     *
     * @return bool true if success or already done.
     */
    protected function alterCustomerTable()
    {
        $sql = 'ALTER TABLE `' . pSQL(_DB_PREFIX_) . 'customer` ADD `sdi` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL';
        // CLEAN_INSTALATION 1/2  (if you want to delete all data after an installation)
        // comment:
        Db::getInstance()->execute($sql);
        return true;
        // and uncomment:
        // return Db::getInstance()->execute($sql);
    }

    /**
     * Uninstalls sample tables required for demonstration.
     *
     * @return bool
     */
    private function uninstallAlterCustomerTable()
    {
        // CLEAN_INSTALATION 2/2 (if you want to delete all data after an installation)
        // uncomment:
        // $sql = 'ALTER TABLE `' . pSQL(_DB_PREFIX_) . 'customer` DROP `sdi`';
        // return Db::getInstance()->execute($sql);
        // 
        // and comment:
        return true;
    }


    /**
     * Hook allows to modify Customers form and add additional form fields as well as modify or add new data to the forms.
     * FRONT_END
     * @param array $params
     */
    public function hookAdditionalCustomerFormFields($params)
    {
        return [
            (new FormField)
                ->setName('sdi')
                ->setType('text')
                ->setRequired(false)
                ->setLabel($this->l('SDI'))
        ];
    }


    /**
     * Hook allows to modify Customers form and add additional form fields as well as modify or add new data to the forms.
     * BACK_END
     * @param array $params
     */
    public function hookActionCustomerFormBuilderModifier(array $params)
    {
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];
        $formBuilder->add('sdi', TextType::class, [
            'label' => $this->getTranslator()->trans('SDI', [], 'Modules.ps_customerfields.Admin'),
            'required' => false,
        ]);
        
        $customer = new Customer($params['id']);
        $params['data']['sdi'] = $customer->sdi;
        
        $formBuilder->setData($params['data']);

    }


    /**
     * Hook allows to modify Customers form and add additional form fields as well as modify or add new data to the forms.
     *
     * @param array $params
     *
     * @throws CustomerException
     */
    public function hookActionAfterUpdateCustomerFormHandler(array $params)
    {
        $this->updateCustomerSdi($params);
    }

    /**
     * Hook allows to modify Customers form and add additional form fields as well as modify or add new data to the forms.
     *
     * @param array $params
     *
     * @throws CustomerException
     */
    public function hookActionAfterCreateCustomerFormHandler(array $params)
    {
        $this->updateCustomerSdi($params);
    }

    /**
     * Update / Create 
     * 
     * @param array $params
     *
     * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
     */
    private function updateCustomerSdi(array $params)
    {
        $customerId = (int)$params['id'];
        /** @var array $customerFormData */
        $customerFormData = $params['form_data'];
        $sdi = $customerFormData['sdi'];
        
        try {

            $customer = new Customer($customerId);
            $customer->sdi= $sdi;
            $customer->update();

        } catch (ReviewerException $exception) {
            throw new \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException($exception);
        }
    }
}
