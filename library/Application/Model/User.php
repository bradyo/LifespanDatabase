<?php

/**
 * @Entity
 * @Table(name="user")
 */
class Application_Model_User 
{
    /**
     * The number of seconds since last activity that the user should be
     * considered "online".
     */
    const LAST_SEEN_LIMIT = 600;

    const ROLE_GUEST = 'guest';
    const ROLE_MEMBER = 'member';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_ADMIN = 'admin';

    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_DELETED = 'deleted';
    
    /**
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(name="username", type="string", length="32")
     */
    private $username;
 
    /**
     * @Column(name="name", type="string", length="64")
     */
    private $name;
    
    /**
     * @Column(name="email", type="string", length=128)
     */
    private $email;
    
    /**
     * @Column(name="role", type="string", length=32)
     */
    private $role;
    
    /**
     * @Column(name="status", type="string", length=32)
     */
    private $status;

    /**
     * Algorithm used to compute password hash (i.e. sha1, md5, etc).
     * @Column(name="password_algorithm", type="string", length=32)
     */
    private $passwordAlgorithm;
    
    /**
     * Salt string added to password hash computation.
     * @Column(name="password_salt", type="string", length=40)
     */
    private $passwordSalt;
    
    /**
     * Password hash computed as $passwordAlgorithm($passwordString . $passwordSalt).
     * @Column(name="password_hash", type="string", length=40)
     */
    private $passwordHash;
    
    /**
     * PHP <a href="http://php.net/manual/en/timezones.php">Timezone identifier</a>.
     * @Column(name="timezone", type="string", length=32)
     */
    private $timezone;
    
    /**
     * PHP <a href="http://www.php.net/manual/en/class.locale.php">Locale identifier</a>.
     * @Column(name="locale", type="string", length="32")
     */
    private $locale;
        
    
    public function __construct() {
        $this->role = self::ROLE_GUEST;
        $this->status = self::STATUS_ACTIVE;
        $this->timezone = 'America/Los_Angeles';
        $this->locale = 'en_US';
    }
    
    
    /**
     * =======================================================================
     * Getters and Setters
     * =======================================================================
     */
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getPasswordAlgorithm() {
        return $this->passwordAlgorithm;
    }

    public function setPasswordAlgorithm($passwordAlgorithm) {
        $this->passwordAlgorithm = $passwordAlgorithm;
    }

    public function getPasswordSalt() {
        return $this->passwordSalt;
    }

    public function setPasswordSalt($passwordSalt) {
        $this->passwordSalt = $passwordSalt;
    }

    public function getPasswordHash() {
        return $this->passwordHash;
    }

    public function setPasswordHash($passwordHash) {
        $this->passwordHash = $passwordHash;
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function setTimezone($timezone) {
        $this->timezone = $timezone;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }


    public function setPassword($password) {
        $salt = sha1(time());
        $this->passwordAlgorithm = 'sha1';
        $this->passwordSalt = $salt;
        $this->passwordHash = sha1($password . $salt);
    }
    
    public function isPassword($password) {
        $algorithm = $this->passwordAlgorithm;
        if (is_callable($algorithm)) {
            $hash = $algorithm($password . $this->passwordSalt);
            if ($hash == $this->passwordHash) {
                return true;
            }
        }
        return false;
    }

    public static function getRoleChoices() {
        return array(
            self::ROLE_GUEST => 'Guest',
            self::ROLE_MEMBER => 'Member',
            self::ROLE_MODERATOR => 'Moderator',
            self::ROLE_ADMIN => 'Admin',
        );
    }
    
    public function getDisplayName() {
        if (!empty($this->name)) {
            return $this->name;
        } else {
            return $this->username;
        }
    }

    public function getRoleLabel()
    {
        $roles = self::getRoleChoices();
        return $roles[$this->getRole()];
    }

    public function isGuest() {
        return ($this->role == self::ROLE_GUEST);
    }

    public function isModerator() {
        // true if either admin or moderator
        return ($this->role == self::ROLE_MODERATOR || $this->isAdmin());
    }

    public function isAdmin() {
        return ($this->role == self::ROLE_ADMIN);
    }

    public function isBlocked() {
        return ($this->status == self::STATUS_BLOCKED);
    }

    public function isDeleted() {
        return ($this->status == self::STATUS_DELETED);
    }
}
