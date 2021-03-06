<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Amazon\Settings\Tabs;

use Ess\M2ePro\Block\Adminhtml\Amazon\Settings\Tabs;
use Magento\Framework\Message\MessageInterface;

/**
 * Class \Ess\M2ePro\Block\Adminhtml\Amazon\Settings\Tabs\Synchronization
 */
class Synchronization extends \Ess\M2ePro\Block\Adminhtml\Settings\Tabs\AbstractTab
{
    /**
     * @var int
     */
    private $inspectorMode;

    //########################################

    protected function _prepareForm()
    {
        // ---------------------------------------
        $instructionsMode = $this->getHelper('Module')->getConfig()->getGroupValue(
            '/cron/task/amazon/listing/product/process_instructions/',
            'mode'
        );
        // ---------------------------------------

        // ---------------------------------------
        $this->inspectorMode = (int)$this->getHelper('Module')->getConfig()->getGroupValue(
            '/listing/product/inspector/',
            'mode'
        );
        // ---------------------------------------

        $form = $this->_formFactory->create(
            [
                'data' => [
                    'enctype' => 'multipart/form-data',
                    'method' => 'post'
                ]
            ]
        );

        $fieldset = $form->addFieldset(
            'amazon_synchronization_templates',
            [
                'legend' => $this->__('M2E Pro Listings Synchronization'),
                'collapsable' => false,
            ]
        );

        $fieldset->addField(
            'instructions_mode',
            self::SELECT,
            [
                'name'        => 'instructions_mode',
                'label'       => $this->__('Enabled'),
                'values' => [
                    0 => $this->__('No'),
                    1 => $this->__('Yes')
                ],
                'value' => $instructionsMode,
                'tooltip' => $this->__(
                    '<p>This synchronization includes import of changes made on Amazon channel as well
                    as the ability to enable/disable the data synchronization managed by the
                    Synchronization Policy Rules.</p><br>
                    <p>However, it does not exclude the ability to manually manage Items in Listings using the
                    available List, Revise, Relist or Stop Action options.</p>'
                )
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        parent::_prepareForm();
    }

    protected function _toHtml()
    {
        $js = "require([
                'M2ePro/Plugin/ProgressBar',
                'M2ePro/Plugin/AreaWrapper',
                'M2ePro/SynchProgress',
                'M2ePro/Synchronization'
            ], function() {

            SynchProgressBarObj = new ProgressBar('synchronization_progress_bar');
            SynchWrapperObj = new AreaWrapper('synchronization_content_container');

            SynchronizationProgressObj = new SynchProgress(SynchProgressBarObj, SynchWrapperObj );
            SynchronizationObj = new Synchronization(SynchronizationProgressObj);";

        $js .= '})';

        $this->js->addOnReadyJs($js);

        $this->jsTranslator->addTranslations(
            [
                'Synchronization Settings have been saved.' => 'Synchronization Settings have been saved.',
                'Running All Enabled Tasks' => 'Running All Enabled Tasks',
                'Revise All' => 'Revise All'
            ]
        );

        $this->jsUrl->addUrls([
            Tabs::TAB_ID_SYNCHRONIZATION => $this->getUrl('*/amazon_synchronization/save'),
            'synch_formSubmit' => $this->getUrl('*/amazon_synchronization/save'),
            'logViewUrl' => $this->getUrl('*/amazon_synchronization_log/index', ['back'=>$this->getHelper('Data')
                ->makeBackUrlParam('*/amazon_synchronization/index')]),
        ]);

        return '<div id="synchronization_progress_bar"></div>
                <div id="synchronization_content_container">'.parent::_toHtml();
    }

    //########################################

    protected function getGlobalNotice()
    {
        return '';
    }

    //########################################
}
