<?php

namespace Avid\CandidateChallenge\Repository;

use Avid\CandidateChallenge\Model\Address;
use Avid\CandidateChallenge\Model\Email;
use Avid\CandidateChallenge\Model\Height;
use Avid\CandidateChallenge\Model\Member;
use Avid\CandidateChallenge\Model\Weight;
use Doctrine\DBAL\Types\Type;

/**
 * @author Kevin Archer <kevin.archer@avidlifemedia.com>
 */
final class DoctrineMemberRepository extends DoctrineRepository implements MemberRepository
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'members';
    const ALIAS = 'member';

    /**
     * @param Member $member
     *
     * @return int Affected rows
     */
    public function add($member)
    {
        $conn = $this->getConnection();
        $conn->connect();
              
        $memberArray = $this->extractData($member);
        $typesArray = $this->getDataTypes();
        return($conn->insert(self::TABLE_NAME, $memberArray, $typesArray));
    }

    /**
     * @param Member $member
     *
     * @return int Affected rows
     */
    public function update($member)
    {
        
        $conn = $this->getConnection();
        $conn->connect();
              
        $memberArray = $this->extractData($member);
        $typesArray = $this->getDataTypes();
        $idArray = array('username'=>$memberArray['username']); //assuming username must be unique
        return($conn->update(self::TABLE_NAME, $memberArray, $idArray, $typesArray));
    }

    /**
     * @param Member $member
     *
     * @return int
     */
    public function remove($member)
    {
        $conn = $this->getConnection();
        $conn->connect();
              
        $memberArray = $this->extractData($member);
        $deleteArray = array('username'=>$memberArray['username']); //assuming username must be unique
        return($conn->delete(self::TABLE_NAME, $deleteArray));
    }

    /**
     * @param string $username
     *
     * @return Member|null
     */
    public function findByUsername($username)
    {
        $conn = $this->getConnection();
        $conn->connect();
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE username = :name';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("name", $username);
        $stmt->execute();
        $result = $stmt->fetch();
        if(empty($result)){
            return null;
        }else{
            return $this->hydrate($result);
        }
        
    }

    /**
     * @param string $keyword
     * @param int $first
     * @param int $max
     *
     * @return Member[]
     */
    public function search($keyword, $first = 0, $max = null)
    {
        $conn = $this->getConnection();
        $conn->connect();
       
        if($max === null){
            $max = -1;  //should have an upper limit defined somewhere
        }
        $sql = 'SELECT * FROM ' . $this->getTableName() . " WHERE username LIKE( :keyword) LIMIT :max OFFSET :first ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("keyword", "%".$keyword."%");
        $stmt->bindValue("first", $first);
        $stmt->bindValue("max", $max);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach($results as $row){
            $member[] = $this->hydrate($row);
          
        }
        if(empty($member)){
            return (int) 0;
        }else{
            return $member;
        }
    }

    /**
     * @param string $keyword
     *
     * @return int
     */
    public function getSearchCount($keyword)
    {
        $conn = $this->getConnection();
        $conn->connect();
        
        //let's try the query builder for this one
        $qb = $this->createQueryBuilder();
        $qb->select('count(m.id)');
        $qb->from('members', 'm');
        $qb->where('m.username LIKE( :keyword)');
        $sql = $qb->getSQL();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("keyword", "%".$keyword."%");
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return (int)$result;
    }

    /**
     * @return int
     */
    public function count()
    {
        $conn = $this->getConnection();
        $conn->connect();
        
        $qb = $this->createQueryBuilder();
        $qb->select('count(m.id)');
        $qb->from('members', 'm');
        $sql = $qb->getSQL();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }

    /**
     * @param int $first
     * @param int $max
     *
     * @return object
     */
    public function findAll($first = 0, $max = null)
    {
        $conn = $this->getConnection();
        $conn->connect();
        
        if($max === null){
            $max = -1;  //should have an upper limit defined
        }
        $sql = 'SELECT * FROM ' . $this->getTableName() . " LIMIT :max OFFSET :first ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("first", $first);
        $stmt->bindValue("max", $max);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $members = array();
        foreach($results as $row){
            $members[] = $this->hydrate($row);
        }
        return $members;
    }

    /**
     * @param array $row
     *
     * @return Member
     */
    protected function hydrate(array $row)
    {
        return new Member(
            $row['username'],
            $row['password'],
            new Address($row['country'], $row['province'], $row['city'], $row['postal_code']),
            new \DateTime($row['date_of_birth']),
            $row['limits'],
            new Height($row['height']),
            new Weight($row['weight']),
            $row['body_type'],
            $row['ethnicity'],
            new Email($row['email'])
        );
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return self::TABLE_NAME;
    }

    /**
     * @return string
     */
    protected function getAlias()
    {
        return self::ALIAS;
    }

    /**
     * @param Member $member
     *
     * @return array
     */
    private function extractData($member)
    {
        return [
            'username' => $member->getUsername(),
            'password' => $member->getPassword(),
            'country' => $member->getAddress()->getCountry(),
            'province' => $member->getAddress()->getProvince(),
            'city' => $member->getAddress()->getCity(),
            'postal_code' => $member->getAddress()->getPostalCode(),
            'date_of_birth' => $member->getDateOfBirth(),
            'limits' => $member->getLimits(),
            'height' => $member->getHeight(),
            'weight' => $member->getWeight(),
            'body_type' => $member->getBodyType(),
            'ethnicity' => $member->getEthnicity(),
            'email' => $member->getEmail(),
        ];
    }

    /**
     * @return array
     */
    private function getDataTypes()
    {
        return [
            Type::STRING,
            Type::STRING,
            Type::STRING,
            Type::STRING,
            Type::STRING,
            Type::STRING,
            Type::DATE,
            Type::STRING,
            Type::STRING,
            Type::STRING,
            Type::STRING,
            Type::STRING,
            Type::STRING,
        ];
    }
}
