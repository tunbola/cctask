<?php
/**
 *  Test class for com.civicrm.contactpcptab PCP entity
 *
 * @package   CiviCRM
 * @group headless
 */

class api_v3_AddressTest extends CiviUnitTestCase {
    protected $_apiversion = 3;
    protected $_contactID;
    protected $_params;
    protected $_entity;

    public function setUp() {
        $this->_entity = 'PCP';
        parent::setUp();

        $contact = CRM_Core_DAO::createTestObject('CRM_Contact_DAO_Contact');
        $this->_contactID = $contact->id;
        CRM_Core_PseudoConstant::flush();

        $this->_params = array(
            'contact_id' => $this->_contactID,
            'api.ContributionSoft.get' => array('pcp_id'=>'$value.id'),
            'api.ContributionPage.get' => array('id'=>'$value.page_id'),
        );
    }

    public function testContactHasPCP() {
        $result = $this->callAPIAndDocument($this->_entity, 'get', $this->_params, __FUNCTION__, __FILE__);
        $this->assertEquals(1, $result['count']);
        $this->assertNotNull($result['values']);
    }

    public function testAddPCP() {
        $blockParams = $this->pcpBlockParams();
        $pcpBlock = CRM_PCP_BAO_PCP::add($blockParams, TRUE);

        $params = $this->pcpParams();
        $params['pcp_block_id'] = $pcpBlock->id;

        $pcp = CRM_PCP_BAO_PCP::add($params, FALSE);

        $this->assertInstanceOf('CRM_PCP_DAO_PCP', $pcp);
        $this->assertEquals($params['contact_id'], $pcp->contact_id, 'My PCP Test');
        $this->assertEquals($params['status_id'], $pcp->status_id, 1);
        $this->assertEquals($params['title'], $pcp->title, 'Check for title.');
        $this->assertEquals($params['intro_text'], $pcp->intro_text, 'Please contribute now!');
        $this->assertEquals($params['page_text'], $pcp->page_text, 'You better give more.');
        $this->assertEquals($params['donate_link_text'], $pcp->donate_link_text, 'Donate Now');
        $this->assertEquals($params['is_thermometer'], $pcp->is_thermometer, 1);
        $this->assertEquals($params['is_honor_roll'], $pcp->is_honor_roll, 1);
        $this->assertEquals($params['goal_amount'], $pcp->goal_amount, 10000.00);
        $this->assertEquals($params['is_active'], $pcp->is_active, 1);

        // Delete our test object
        $delParams = array('id' => $pcp->id);
        // FIXME: Currently this delete fails with an FK constraint error: DELETE FROM civicrm_contribution_type  WHERE (  civicrm_contribution_type.id = 5 )
        // CRM_Core_DAO::deleteTestObjects( 'CRM_PCP_DAO_PCP', $delParams );
    }

    private function pcpParams() {
        $contribPage = CRM_Core_DAO::createTestObject('CRM_Contribute_DAO_ContributionPage');
        $contribPageId = $contribPage->id;

        $params = array(
            'contact_id' => $this->_contactID,
            'status_id' => '1',
            'title' => 'My PCP Test',
            'intro_text' => 'Please contribute now!',
            'page_text' => 'You better give more.',
            'donate_link_text' => 'Donate Now',
            'page_id' => $contribPageId,
            'is_thermometer' => 1,
            'is_honor_roll' => 1,
            'goal_amount' => 10000.00,
            'is_active' => 1,
        );

        return $params;
    }

    /**
     * Build params.
     */
    private function pcpBlockParams() {
        $contribPage = CRM_Core_DAO::createTestObject('CRM_Contribute_DAO_ContributionPage');
        $contribPageId = $contribPage->id;
        $supporterProfile = CRM_Core_DAO::createTestObject('CRM_Core_DAO_UFGroup');
        $supporterProfileId = $supporterProfile->id;

        $params = array(
            'entity_table' => 'civicrm_contribution_page',
            'entity_id' => $contribPageId,
            'supporter_profile_id' => $supporterProfileId,
            'target_entity_id' => 1,
            'is_approval_needed' => 1,
            'is_tellfriend_enabled' => 1,
            'tellfriend_limit' => 1,
            'link_text' => 'Create your own PCP',
            'is_active' => 1,
        );

        return $params;
    }

    public function tearDown() {
        $this->contactDelete($this->_contactID);
    }

}
