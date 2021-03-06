<?php

namespace MediaWiki\Extension\LDAPProvider\Tests;

use MediaWiki\Extension\LDAPProvider\UserDomainStore;
use MediaWikiTestCase;
use MediaWiki\MediaWikiServices;

/**
 * @group Database
 */
class UserDomainStoreTest extends MediaWikiTestCase {

	protected function setUp() : void {
		$this->tablesUsed[] = 'ldap_domains';
		parent::setUp();

		$this->db->insert( 'ldap_domains', [
			'domain' => 'SOMEDOMAIN',
			'user_id' => self::getTestSysop()->getUser()->getId()
		] );
	}

	/**
	 * @covers MediaWiki\Extension\LDAPProvider\UserDomainStore::getDomainForUser
	 */
	public function testGetDomainForUser() {
		$store = new UserDomainStore(
			MediaWikiServices::getInstance()->getDBLoadBalancer()
		);
		$domain = $store->getDomainForUser( self::getTestSysop()->getUser() );

		$this->assertEquals(
			'SOMEDOMAIN', $domain, 'Should deliver the domain'
		);
	}

	/**
	 * @covers MediaWiki\Extension\LDAPProvider\UserDomainStore::setDomainForUser
	 */
	public function testSetDomainForUser() {
		$store = new UserDomainStore(
			MediaWikiServices::getInstance()->getDBLoadBalancer()
		);
		$store->setDomainForUser(
			self::getTestUser()->getUser(), 'ANOTHERDOMAIN'
		);
		$this->assertSelect(
			'ldap_domains',
			[ 'domain' ],
			[ 'user_id' => self::getTestUser()->getUser()->getId() ],
			[
				[ 'ANOTHERDOMAIN' ]
			]
		);
	}
}
