<?php

namespace Avid\CandidateChallenge\Model;

/**
 * @covers \Avid\CandidateChallenge\Model\Member
 *
 * @uses \Avid\CandidateChallenge\Model\Address
 * @uses \Avid\CandidateChallenge\Model\Height
 * @uses \Avid\CandidateChallenge\Model\Weight
 * @uses \Avid\CandidateChallenge\Model\Email
 *
 * @author Kevin Archer <kevin.archer@avidlifemedia.com>
 */
final class MemberTest extends \PHPUnit_Framework_TestCase
{
    protected $member;
    
    protected function setUp()
    {
       parent::setUp();
        
        $username = 'testuser';
        $password = 'testpass';
        $address = new Address('CA', 'ON', 'Toronto', 'M4C1B6');
        $dateOfBirth = new \DateTime('1990-01-01');
        $limits = new Limits('Anything Goes');
        $height = new Height('5\'5');
        $weight = new Weight('200');
        $bodyType = new BodyType('Muscular');
        $ethnicity = new Ethnicity('Asian');
        $email =  new  Email('looking@forlove.com');
         
        $this->member = new Member($username, $password, $address, $dateOfBirth, $limits, $height, $weight, $bodyType, $ethnicity, $email);
    }
    
    protected function tearDown()
    {
        unset($this->member);
    }
    
    public function testTrueIsTrue()
    {
        $var = true;
        $this->assertTrue($var);
    }
    
    public function testGetAddress(){
        $address = new Address('CA', 'ON', 'Toronto', 'M4C1B6');
        $this->assertEquals($address, $this->member->getAddress());
    }
    
    public function testGetBodyType()
    {
        $bodyType = new BodyType('Muscular');
        $this->assertEquals($bodyType, $this->member->getBodyType());
    }

    public function testGetDateOfBirth()
    {
        $dateOfBirth = new \DateTime('1990-01-01');
        $this->assertEquals($dateOfBirth, $this->member->getDateOfBirth());
    }

    public function testGetEmail()
    {
        $email =  new  Email('looking@forlove.com');
        $this->assertEquals($email, $this->member->getEmail());
    }

    public function testGetEthnicity()
    {
        $ethnicity = new Ethnicity('Asian');
        $this->assertEquals($ethnicity, $this->member->getEthnicity());
    }
    
    public function testGetHeight()
    {
        $height = new Height('5\'5');
        $this->assertEquals($height, $this->member->getHeight());
    }
    
    public function testGetLimits()
    {
        $limits = new Limits('Anything Goes');
        $this->assertEquals($limits, $this->member->getLimits());
    }

     public function testGetPassword()
    {
        $password = 'testpass';
        $this->assertEquals($password, $this->member->getPassword());
    }

    public function testGetUsername()
    {
        $username = 'testuser';
        $this->assertEquals($username, $this->member->getUsername());
    }

    public function testGetWeight()
    {
        $weight = new Weight('200');
        $this->assertEquals($weight, $this->member->getWeight());
    }

    public function testGetAge()
    {
        $dateOfBirth = new \DateTime('1990-01-01');
        $now = new \DateTime();
        $age =  $now->diff($dateOfBirth)->y;
        $this->assertEquals($age, $this->member->getAge());
      
    }
    
    
}
